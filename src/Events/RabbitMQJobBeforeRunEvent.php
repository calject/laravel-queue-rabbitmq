<?php
/**
 * Author: 沧澜
 * Date: 2019-12-30
 */

namespace VladimirYuldashev\LaravelQueueRabbitMQ\Events;


class RabbitMQJobBeforeRunEvent
{
    /**
     * 当前运行job class 类名
     * @var string
     */
    protected $jobName;
    
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
     * @param string $jobName
     * @param string $queue
     * @param array $payload
     */
    public function __construct(string $jobName, string $queue, array $payload)
    {
        $this->jobName = $jobName;
        $this->queue = $queue;
        $this->payload = $payload;
    }
    
    /**
     * @return string
     */
    public function getJobName(): string
    {
        return $this->jobName;
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