<?php
/**
 * Author: 沧澜
 * Date: 2019-12-30
 */

namespace VladimirYuldashev\LaravelQueueRabbitMQ\Contracts;


interface IRabbitMQJobData
{
    /**
     * 执行的队列名称
     * @return string
     */
    public function queue(): string;
    
    /**
     * 执行的Job别名或完整类路径(带命名空间的类名)
     * @return string
     */
    public function jobClass(): string;
    
    /**
     * 执行参数列表
     * @return array
     */
    public function params(): array;
    
    /**
     * 队列配置 [key => value] 示例['tries' => 1]
     * @return array
     */
    public function opts(): array;
}