<?php
/**
 * Author: 沧澜
 * Date: 2019-12-30
 */

use VladimirYuldashev\LaravelQueueRabbitMQ\Component\RabbitMQDispatcher;
use VladimirYuldashev\LaravelQueueRabbitMQ\Component\RabbitMQJob;
use VladimirYuldashev\LaravelQueueRabbitMQ\Contracts\IRabbitMQJobData;

if (!function_exists('rabbitmq_dispatch')) {
    /**
     * @param mixed $job
     * @return RabbitMQDispatcher
     */
    function rabbitmq_dispatch($job)
    {
        if ($job instanceof IRabbitMQJobData) {
            return RabbitMQDispatcher::dispatch($job);
        } else if (is_string($job) && class_exists($job)){
            return RabbitMQDispatcher::dispatch(new $job);
        } else {
            return RabbitMQDispatcher::make($job);
        }
    }
}

if (!function_exists('rabbitmq_dispatch_job')) {
    /**
     * @param $jobClass
     * @param array $params
     * @return RabbitMQDispatcher
     */
    function rabbitmq_dispatch_job($jobClass, $params = [])
    {
        return RabbitMQDispatcher::make(RabbitMQJob::make($jobClass, $params));
    }
}

if (!function_exists('rabbitmq_queue_def')) {
    /**
     * @return string
     */
    function rabbitmq_queue_def()
    {
        return app('config')->get('queue.connections.rabbitmq.queue', 'default');
    }
}


if (!function_exists('h_throw')) {
    /**
     * @param Throwable $throwable
     * @throws Throwable|mixed
     */
    function h_throw(Throwable $throwable)
    {
        throw $throwable;
    }
}


