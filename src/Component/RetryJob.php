<?php
/**
 * Author: 沧澜
 * Date: 2019-12-30
 */

namespace VladimirYuldashev\LaravelQueueRabbitMQ\Component;


use Carbon\Carbon;

class RetryJob
{
    protected $queue = 'default';
    protected $jobClass = '';
    protected $params = [];
    protected $retryTimes = 0;
    protected $maxRetryTimes = 0;
    protected $opts = [];
    protected $expand = [];
    protected $delay = 0;
    /**
     * @var bool
     */
    protected $isDispatch = true;
    
    /**
     * RetryJob constructor.
     * @param string $jobClass
     * @param array $params
     * @param array $opts
     */
    public function __construct(string $jobClass, array $params = [], array $opts = [])
    {
        $this->jobClass = $jobClass;
        $this->params = $params;
        $this->opts = $opts;
    }
    
    /**
     * @param string $queue
     * @return $this
     */
    public function queue(string $queue)
    {
        $this->queue = $queue;
        return $this;
    }
    
    /**
     * @param string $queue
     * @return $this
     */
    public function onQueue(string $queue)
    {
        $this->queue = $queue;
        return $this;
    }
    
    /**
     * @param array $params
     * @return $this
     */
    public function params(array $params)
    {
        $this->params = $params;
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
     * @param int $maxRetryTimes
     * @return $this
     */
    public function maxRetryTimes(int $maxRetryTimes)
    {
        $this->maxRetryTimes = $maxRetryTimes;
        return $this;
    }
    
    /**
     * @param array $expand
     * @return $this
     */
    public function expand(array $expand)
    {
        $this->expand = $expand;
        return $this;
    }
    
    /**
     * @param array $opts
     * @return $this
     */
    public function opts(array $opts)
    {
        $this->opts = $opts;
        return $this;
    }
    
    /**
     * @param array $opts
     * @return $this
     */
    public function appendOpts(array $opts)
    {
        $this->opts = $opts + $this->opts;
        return $this;
    }
    
    /**
     * @param int $delay
     * @return $this
     */
    public function delay(int $delay)
    {
        $this->delay = $delay;
        return $this;
    }
    
    /**
     * @param bool $isDispatch
     * @return $this
     */
    public function setIsDispatch(bool $isDispatch)
    {
        $this->isDispatch = $isDispatch;
        return $this;
    }
    
    /**
     * @return bool
     */
    public function isDispatch(): bool
    {
        return $this->isDispatch;
    }
    
    public function __destruct()
    {
        if ($this->isDispatch) {
            rabbitmq_dispatch(
                RabbitMQJob::make($this->jobClass, $this->params, $this->opts + [
                        'retryTimes' => $this->retryTimes,
                        'maxRetryTimes' => $this->maxRetryTimes,
                        'tries' => 1
                    ])->expand($this->expand)
            )->onQueue($this->queue)->delay(Carbon::now()->addSecond($this->delay));
        }
    }
}