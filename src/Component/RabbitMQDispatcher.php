<?php
/**
 * Author: 沧澜
 * Date: 2019-12-30
 */

namespace VladimirYuldashev\LaravelQueueRabbitMQ\Component;

use Illuminate\Foundation\Bus\PendingDispatch;
use VladimirYuldashev\LaravelQueueRabbitMQ\Contracts\IRabbitMQJobData;

class RabbitMQDispatcher extends PendingDispatch
{
    
    /**
     * @param mixed $job
     * @return RabbitMQDispatcher
     */
    public static function make($job)
    {
        return new static($job);
    }
    
    /**
     * @param IRabbitMQJobData $data
     * @return RabbitMQDispatcher
     */
    public static function dispatch(IRabbitMQJobData $data)
    {
        $job = RabbitMQJob::make($data->jobClass(), $data->params());
        if ($data->queue()) {
            $job->onQueue($data->queue());
        }
        return new static($job);
    }
    
    
}