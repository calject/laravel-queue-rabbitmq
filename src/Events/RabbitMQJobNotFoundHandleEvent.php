<?php
/**
 * Author: 沧澜
 * Date: 2019-12-30
 */

namespace VladimirYuldashev\LaravelQueueRabbitMQ\Events;


class RabbitMQJobNotFoundHandleEvent
{
    /**
     * running job class name
     * @var string
     */
    protected $jobClass;
    
    /**
     * the rabbit mq queue name
     * @var string
     */
    protected $queue = '';
    
    /**
     * @var array
     */
    protected $params = [];
    
    /**
     * RabbitMQJobEvent constructor.
     * @param string $jobClass
     * @param string $queue
     * @param array $params
     */
    public function __construct(string $jobClass, string $queue, array $params)
    {
        $this->jobClass = $jobClass;
        $this->queue = $queue;
        $this->params = $params;
    }
    
    /**
     * @return string
     */
    public function getJobClass(): string
    {
        return $this->jobClass;
    }
    
    /**
     * @return string
     */
    public function getQueue(): string
    {
        return $this->queue;
    }
    /**
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }
    
}