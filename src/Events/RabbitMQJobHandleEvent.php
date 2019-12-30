<?php
/**
 * Author: 沧澜
 * Date: 2019-12-30
 */

namespace VladimirYuldashev\LaravelQueueRabbitMQ\Events;


class RabbitMQJobHandleEvent
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
    protected $payload = array();
    
    /**
     * RabbitMQJobBeforeRunEvent constructor.
     * @param string $jobClass
     * @param string $queue
     * @param array $payload
     */
    public function __construct(string $jobClass, string $queue, array $payload)
    {
        $this->jobClass = $jobClass;
        $this->queue = $queue;
        $this->payload = $payload;
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
    public function getPayload(): array
    {
        return $this->payload;
    }
}