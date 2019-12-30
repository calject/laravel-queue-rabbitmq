<?php
/**
 * Author: Vladimir Yuldashev
 * email: misterio92@gmail.com
 *
 * code modify:
 * Author: 沧澜
 * Date: 2019-12-30
 */

namespace VladimirYuldashev\LaravelQueueRabbitMQ\Queue\Jobs;

use Closure;
use Exception;
use Illuminate\Support\Str;
use Interop\Amqp\AmqpMessage;
use Illuminate\Queue\Jobs\Job;
use Interop\Amqp\AmqpConsumer;
use Illuminate\Queue\Jobs\JobName;
use Illuminate\Container\Container;
use Illuminate\Database\DetectsDeadlocks;
use Illuminate\Contracts\Queue\Job as JobContract;
use VladimirYuldashev\LaravelQueueRabbitMQ\Component\OptCheck;
use \VladimirYuldashev\LaravelQueueRabbitMQ\Component\RabbitMQJob as LaravelRabbitMQJob;
use Throwable;
use VladimirYuldashev\LaravelQueueRabbitMQ\Component\ClassSerialize;
use VladimirYuldashev\LaravelQueueRabbitMQ\Constants\RabbitMQEvent;
use VladimirYuldashev\LaravelQueueRabbitMQ\Events\RabbitMQJobAfterRunEvent;
use VladimirYuldashev\LaravelQueueRabbitMQ\Events\RabbitMQJobBeforeRunEvent;
use VladimirYuldashev\LaravelQueueRabbitMQ\Events\RabbitMQJobCreateEvent;
use VladimirYuldashev\LaravelQueueRabbitMQ\Events\RabbitMQJobHandleEvent;
use VladimirYuldashev\LaravelQueueRabbitMQ\Events\RabbitMQJobRunExceptionEvent;
use VladimirYuldashev\LaravelQueueRabbitMQ\Queue\RabbitMQQueue;

class RabbitMQJob extends Job implements JobContract
{
    use DetectsDeadlocks;

    /**
     * Same as RabbitMQQueue, used for attempt counts.
     */
    public const ATTEMPT_COUNT_HEADERS_KEY = 'attempts_count';

    protected $connection;
    protected $consumer;
    protected $message;
    
    /**
     * 事件监听配置
     * @var int
     */
    protected $eventOpt = 0;
    
    /**
     * 是否开启事件监听
     * @var bool
     */
    protected $eventDisable = false;
    
    /**
     * @var OptCheck
     */
    protected $eventCheck;
    

    public function __construct(
        Container $container,
        RabbitMQQueue $connection,
        AmqpConsumer $consumer,
        AmqpMessage $message
    ) {
        $this->container = $container;
        $this->connection = $connection;
        $this->consumer = $consumer;
        $this->message = $message;
        $this->queue = $consumer->getQueue()->getQueueName();
        $this->connectionName = $connection->getConnectionName();
        $this->eventDisable = config('rabbitmq.event_disable', false);
        $this->eventOpt = config('rabbitmq.event', 0);
        $this->eventCheck = OptCheck::make($this->eventOpt);
        $this->init($container, $connection, $consumer, $message);
    }
    
    /**
     * @param Container $container
     * @param RabbitMQQueue $connection
     * @param AmqpConsumer $consumer
     * @param AmqpMessage $message
     */
    protected function init(Container $container, RabbitMQQueue $connection, AmqpConsumer $consumer, AmqpMessage $message)
    {
        $rawBody = json_decode($message->getBody(), true);
        if ($rawBody['data']['commandName'] === LaravelRabbitMQJob::class) {
            $command = $rawBody['data']['command'];
            $runJob = $this->getRunJob($command);
            $mapRunJob = $this->getMapRunJob($runJob, $runJob);
            $this->checkEventRun(RabbitMQEvent::JOB_HANDLE, (function () use ($runJob, $rawBody) {
                event(new RabbitMQJobHandleEvent($runJob, $this->queue, $rawBody));
            })->bindTo($this));
            if ($mapRunJob && $mapRunJob !== LaravelRabbitMQJob::class && class_exists($mapRunJob)) {
                $rawBody['displayName'] = $mapRunJob;
                $rawBody['data']['commandName'] = $mapRunJob;
                $rawBody['data']['command'] = ClassSerialize::convert(LaravelRabbitMQJob::class, $mapRunJob, $command);
                $message->setBody(json_encode($rawBody));
            }
        }
        $runJob = $runJob ?? $rawBody['data']['commandName'];
        $this->checkEventRun(RabbitMQEvent::JOB_CREATE, (function () use ($container, $connection, $consumer, $message, $runJob) {
            event(new RabbitMQJobCreateEvent($this->queue, $runJob, $container, $connection, $consumer, $message));
        })->bindTo($this));
    }

    /**
     * Fire the job.
     *
     * @throws Exception
     *
     * @return void
     */
    public function fire(): void
    {
        try {
            $payload = $this->payload();

            [$class, $method] = JobName::parse($payload['job']);
    
            $this->checkEventRun(RabbitMQEvent::JOB_BEFORE_RUN, (function () use ($payload) {
                event(new RabbitMQJobBeforeRunEvent($payload['displayName'], $this->queue, $payload));
            })->bindTo($this));
            
            with($this->instance = $this->resolve($class))->{$method}($this, $payload['data']);
    
            $this->checkEventRun(RabbitMQEvent::JOB_AFTER_RUN, (function () use ($payload) {
                event(new RabbitMQJobAfterRunEvent($payload['displayName'], $this->queue, $payload));
            })->bindTo($this));
            
        } catch (Exception $exception) {
            if (
                $this->causedByDeadlock($exception) ||
                Str::contains($exception->getMessage(), ['detected deadlock'])
            ) {
                sleep(2);
                $this->fire();

                return;
            }

            throw $exception;
        }
    }

    /**
     * Get the number of times the job has been attempted.
     *
     * @return int
     */
    public function attempts(): int
    {
        // set default job attempts to 1 so that jobs can run without retry
        $defaultAttempts = 1;

        return $this->message->getProperty(self::ATTEMPT_COUNT_HEADERS_KEY, $defaultAttempts);
    }

    /**
     * Get the raw body string for the job.
     *
     * @return string
     */
    public function getRawBody(): string
    {
        return $this->message->getBody();
    }

    /** {@inheritdoc} */
    public function delete(): void
    {
        parent::delete();

        $this->consumer->acknowledge($this->message);
    }

    /** {@inheritdoc}
     * @throws Exception
     */
    public function release($delay = 0): void
    {
        parent::release($delay);

        $this->delete();

        $body = $this->payload();

        /*
         * Some jobs don't have the command set, so fall back to just sending it the job name string
         */
        if (isset($body['data']['command']) === true) {
            $job = $this->unserialize($body);
        } else {
            $job = $this->getName();
        }

        $data = $body['data'];

        $this->connection->release($delay, $job, $data, $this->getQueue(), $this->attempts() + 1);
    }

    /**
     * Get the job identifier.
     *
     * @return string
     * @throws \Interop\Queue\Exception
     */
    public function getJobId(): string
    {
        return $this->message->getCorrelationId();
    }

    /**
     * Sets the job identifier.
     *
     * @param string $id
     *
     * @return void
     */
    public function setJobId($id): void
    {
        $this->connection->setCorrelationId($id);
    }

    /**
     * Unserialize job.
     *
     * @param array $body
     *
     * @throws Exception
     *
     * @return mixed
     */
    protected function unserialize(array $body)
    {
        try {
            /* @noinspection UnserializeExploitsInspection */
            return unserialize($body['data']['command']);
        } catch (Exception $exception) {
            if (
                $this->causedByDeadlock($exception) ||
                Str::contains($exception->getMessage(), ['detected deadlock'])
            ) {
                sleep(2);

                return $this->unserialize($body);
            }

            throw $exception;
        }
    }
    
    /**
     * @param string $command
     * @return string
     */
    protected function getRunJob(string $command)
    {
        try {
            $rabbitMQJob = unserialize($command);
            return ($rabbitMQJob instanceof LaravelRabbitMQJob) ? $rabbitMQJob->jobClass : $command;
        } catch (Throwable $exception) {
            $this->checkEventRun(RabbitMQEvent::JOB_RUN_EXCEPTION, (function () {
                event(new RabbitMQJobRunExceptionEvent($this->queue, $this->payload()));
            })->bindTo($this));
            return $command;
        }
    }
    
    /**
     * @param string $runJob
     * @param mixed $default
     * @return mixed|string
     */
    protected function getMapRunJob(string $runJob, $default = null)
    {
        $queueMap = config('rabbitmq.map.' . $this->queue, []);
        if (is_string($queueMap)) {
            if (class_exists('App\Jobs\\'.ucfirst($this->queue).'\\'.$queueMap)) {
                return 'App\Jobs\\'.ucfirst($this->queue).'\\'.$queueMap;
            } else {
                return class_exists($queueMap) ? $queueMap : (config($queueMap, [])[$runJob] ?? $default);
            }
        } elseif (class_exists('App\Jobs\\'.ucfirst($this->queue).'\\'.$runJob)) {
            return 'App\Jobs\\'.ucfirst($this->queue).'\\'.$runJob;
        } else {
            return $queueMap[$runJob] ?? $default;
        }
    }
    
    /**
     * @param int $check
     * @param Closure $run
     */
    protected function checkEventRun(int $check, Closure $run)
    {
        if ($this->eventDisable) {
            $this->eventCheck->checkRun($check, $run);
        }
    }
}
