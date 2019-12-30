<?php
/**
 * Author: 沧澜
 * Date: 2019-12-30
 */

namespace VladimirYuldashev\LaravelQueueRabbitMQ\Models;

use VladimirYuldashev\LaravelQueueRabbitMQ\Contracts\IRabbitMQJobData;

class RabbitMQJobData implements IRabbitMQJobData
{
    /**
     * @var string
     */
    protected $queue = '';
    
    /**
     * @var string
     */
    protected $jobClass = '';
    
    /**
     * @var array
     */
    protected $params = [];
    
    /*---------------------------------------------- set ----------------------------------------------*/
    
    /**
     * @param string $queue
     * @return $this
     */
    public function setQueue(string $queue)
    {
        $this->queue = $queue;
        return $this;
    }
    
    /**
     * @param string $jobClass
     * @return $this
     */
    public function setJobClass(string $jobClass)
    {
        $this->jobClass = $jobClass;
        return $this;
    }
    
    /**
     * @param array $params
     * @return $this
     */
    public function setParams(array $params)
    {
        $this->params = $params;
        return $this;
    }
    
    /*---------------------------------------------- get ----------------------------------------------*/
    
    /**
     * @return string
     */
    public function queue(): string
    {
        return $this->queue;
    }
    
    /**
     * @return string
     */
    public function jobClass(): string
    {
        return $this->jobClass;
    }
    
    /**
     * @return array
     */
    public function params(): array
    {
        return $this->params;
    }
    
}