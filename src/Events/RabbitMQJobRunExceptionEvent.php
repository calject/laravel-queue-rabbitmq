<?php
/**
 * Author: 沧澜
 * Date: 2019-12-30
 */

namespace VladimirYuldashev\LaravelQueueRabbitMQ\Events;


class RabbitMQJobRunExceptionEvent
{
    /**
     * run job queue name
     * @var string
     */
    protected $queue;
    
    /**
     *  $payload object data
     *  {
     *      "displayName": "VladimirYuldashev\\LaravelQueueRabbitMQ\\Component\\RabbitMQJob",
     *      "job": "Illuminate\\Queue\\CallQueuedHandler@call",
     *      "maxTries": 1,
     *      "timeout": 600,
     *      "timeoutAt": null,
     *      "data": {
     *          "commandName": "VladimirYuldashev\\LaravelQueueRabbitMQ\\Component\\RabbitMQJob",
     *          "command": "O:60:\"VladimirYuldashev\\LaravelQueueRabbitMQ\\Component\\RabbitMQJob\":11:{s:8:\"jobClass\";s:5:\"query\";s:5:\"tries\";i:1;s:7:\"timeout\";i:600;s:9:\"\u0000*\u0000params\";a:2:{s:4:\"test\";s:3:\"123\";s:2:\"id\";s:3:\"456\";}s:6:\"\u0000*\u0000job\";N;s:10:\"connection\";s:8:\"rabbitmq\";s:5:\"queue\";s:7:\"payment\";s:15:\"chainConnection\";N;s:10:\"chainQueue\";N;s:5:\"delay\";N;s:7:\"chained\";a:0:{}}"
     *      },
     *      "id": "p7j1bnFJEntxyNM2J1EgT1alU4W5TUAQ"
     *  }
     * @var array
     */
    protected $payload;
    
    /**
     * RabbitMQJobRunExceptionEvent constructor.
     * @param string $queue
     * @param array $payload
     */
    public function __construct(string $queue, array $payload)
    {
        $this->queue = $queue;
        $this->payload = $payload;
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