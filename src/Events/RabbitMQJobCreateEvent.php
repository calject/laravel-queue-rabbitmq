<?php
/**
 * Author: 沧澜
 * Date: 2019-12-30
 */

namespace VladimirYuldashev\LaravelQueueRabbitMQ\Events;

use Interop\Amqp\AmqpConsumer;
use Interop\Amqp\AmqpMessage;
use Illuminate\Container\Container;
use VladimirYuldashev\LaravelQueueRabbitMQ\Queue\RabbitMQQueue;


class RabbitMQJobCreateEvent
{
    
    /**
     * queue name
     * @var string
     */
    protected $queue = '';
    
    /**
     * dispatch name
     * @var string
     */
    protected $jobClass = '';
    
    /**
     * @var Container
     */
    protected $container;
    /**
     * @var RabbitMQQueue
     */
    protected $connection;
    /**
     * @var AmqpConsumer
     */
    protected $consumer;
    /**
     * @var AmqpMessage
     */
    protected $message;
    
    /**
     * RabbitMQJobCreateEvent constructor.
     * @param string $queue
     * @param string $jobClass
     * @param Container $container
     * @param RabbitMQQueue $connection
     * @param AmqpConsumer $consumer
     * @param AmqpMessage $message
     */
    public function __construct(string $queue, string $jobClass, Container $container, RabbitMQQueue $connection, AmqpConsumer $consumer, AmqpMessage $message)
    {
        $this->queue = $queue;
        $this->jobClass = $jobClass;
        $this->container = $container;
        $this->connection = $connection;
        $this->consumer = $consumer;
        $this->message = $message;
    }
    
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
     * @return Container
     */
    public function container(): Container
    {
        return $this->container;
    }
    
    /**
     * @return RabbitMQQueue
     */
    public function connection(): RabbitMQQueue
    {
        return $this->connection;
    }
    
    /**
     * @return AmqpConsumer
     */
    public function consumer(): AmqpConsumer
    {
        return $this->consumer;
    }
    
    /**
     * @return AmqpMessage
     */
    public function message(): AmqpMessage
    {
        return $this->message;
    }
    
    /*---------------------------------------------- get ----------------------------------------------*/
    
    /**
     * @return string
     */
    public function getQueue(): string
    {
        return $this->queue;
    }
    
    /**
     * @return string
     */
    public function getJobClass(): string
    {
        return $this->jobClass;
    }
    
    /**
     * @return Container
     */
    public function getContainer(): Container
    {
        return $this->container;
    }
    
    /**
     * @return RabbitMQQueue
     */
    public function getConnection(): RabbitMQQueue
    {
        return $this->connection;
    }
    
    /**
     * @return AmqpConsumer
     */
    public function getConsumer(): AmqpConsumer
    {
        return $this->consumer;
    }
    
    /**
     * @return AmqpMessage
     */
    public function getMessage(): AmqpMessage
    {
        return $this->message;
    }
    
}