<?php
/**
 * Author: 沧澜
 * Date: 2019-12-30
 */

namespace VladimirYuldashev\LaravelQueueRabbitMQ\Component;

use VladimirYuldashev\LaravelQueueRabbitMQ\Contracts\AbsRabbitMQJob;
use VladimirYuldashev\LaravelQueueRabbitMQ\Events\RabbitMQJobNotFoundHandleEvent;

final class RabbitMQJob extends AbsRabbitMQJob
{
    /**
     * 执行job
     * @var string
     */
    public $jobClass = '';
    
    /**
     * LaravelRabbitMQJob constructor.
     * @param string $jobClass
     * @param array $params
     * @param array $opts
     * @param array $expand
     */
    public function __construct(string $jobClass, array $params = [], array $opts = [], $expand = [])
    {
        $this->onConnection('rabbitmq');
        $this->onQueue(app('config')->get('queue.connections.rabbitmq.queue', 'default'));
        $this->jobClass = $jobClass;
        $this->params = $params;
        $this->expand = $expand;
        $this->opts($opts);
    }
    
    /**
     * @param string $jobClass
     * @param array $params
     * @param array $opts
     * @return static
     */
    public static function make(string $jobClass, array $params = [], array $opts = [])
    {
        return new static($jobClass, $params, $opts);
    }
    
    /**
     * @param array $params
     * @return $this
     */
    public function params(array $params = [])
    {
        $this->params = $params;
        return $this;
    }
    
    /**
     * @param array $expand
     * @return RabbitMQJob
     */
    public function expand(array $expand)
    {
        $this->expand = $expand;
        return $this;
    }
    
    /**
     * @param int $retryTimes
     * @return $this
     */
    public function retryTimes(int $retryTimes)
    {
        $this->retryTimes = $retryTimes;
        return $this;
    }
    
    /**
     * @param int $maxTimes
     * @return $this
     */
    public function maxTimes(int $maxTimes)
    {
        $this->maxRetryTimes = $maxTimes;
        return $this;
    }
    
    /**
     * @param array $opts
     * @return RabbitMQJob
     */
    public function opts(array $opts)
    {
        foreach ($opts as $pro => $val) {
            $this->$pro = $val;
        }
        return $this;
    }
    
    /**
     * job handle
     */
    public function handle()
    {
        event(new RabbitMQJobNotFoundHandleEvent($this->jobClass, $this->queue, $this->params));
    }
    
}