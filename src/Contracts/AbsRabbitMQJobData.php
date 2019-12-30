<?php
/**
 * Author: 沧澜
 * Date: 2019-12-30
 */

namespace VladimirYuldashev\LaravelQueueRabbitMQ\Contracts;

/**
 * Class AbsRabbitMQJobData
 * @package VladimirYuldashev\LaravelQueueRabbitMQ\Contracts
 */
abstract class AbsRabbitMQJobData implements IRabbitMQJobData
{
    
    /**
     * 执行的队列名称
     * @return string
     */
    public function queue(): string
    {
        return rabbitmq_queue_def();
    }
    
    /**
     * 队列配置 [key => value] 示例['tries' => 1]
     * @return array
     */
    public function opts(): array
    {
        return [];
    }
    
}