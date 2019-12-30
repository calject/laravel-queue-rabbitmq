<?php
/**
 * Author: 沧澜
 * Date: 2019-12-30
 */

namespace VladimirYuldashev\LaravelQueueRabbitMQ\Contracts;

use ArrayAccess;
use Carbon\Carbon;
use Closure;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use RuntimeException;
use VladimirYuldashev\LaravelQueueRabbitMQ\Component\RabbitMQDispatcher;
use VladimirYuldashev\LaravelQueueRabbitMQ\Component\RabbitMQJob;
use VladimirYuldashev\LaravelQueueRabbitMQ\Component\RetryJob;
use VladimirYuldashev\LaravelQueueRabbitMQ\Exceptions\RabbitMQJobException;

abstract class AbsRabbitMQJob implements ShouldQueue, ArrayAccess
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    /**
     * retry times
     * @var int
     */
    public $tries = 1;
    
    /**
     * time out
     * @var int
     */
    public $timeout = 600;
    
    /**
     * job别名参数(用于生产job别名映射)
     * @var string
     */
    public $alias = '';
    
    /**
     * 当前重试次数 0
     * @var int
     */
    protected $retryTimes = 0;
    
    /**
     * 最大重试次数 0:不重试
     * @var int
     */
    protected $maxRetryTimes = 0;
    
    /**
     * job params
     * @var array
     */
    protected $params = [];
    
    /**
     * 拓展数据域
     * @var array
     */
    protected $expand = [];
    
    /**
     * @param string $key
     * @param mixed $default
     * @return mixed|null
     */
    protected function input(string $key = null, $default = null)
    {
        return $key ? $this->params : ($this[$key] ?? $default);
    }
    
    /**
     * @return array
     */
    protected function all(): array
    {
        return $this->params;
    }
    
    /**
     * @param string|null $key
     * @return array|mixed
     */
    protected function expands(string $key = null)
    {
        return $this->expand[$key] ?? $this->expand;
    }
    
    /**
     * @param int $maxTimes 最大重试次数
     * @param int $second   延迟执行时间(秒)
     * @param Closure|null $exceedFunc  次数超限回调
     * @param Closure|null $retryFunc   重试触发回调
     * @return RetryJob
     */
    protected function retry(int $maxTimes, int $second = 0, Closure $exceedFunc = null, Closure $retryFunc = null)
    {
        if ($this->retryTimes < $maxTimes) {
            $dispatch = $this->retryDispatch()
                ->retryTimes($this->retryTimes + 1)
                ->maxRetryTimes($maxTimes)
                ->delay($second);
            $retryFunc && call_user_func($retryFunc, $dispatch);
            return $dispatch;
        } else {
            $dispatch = $this->retryDispatch()->setIsDispatch(false);
            $exceedFunc && call_user_func($exceedFunc, $dispatch);
            return $dispatch;
        }
    }
    
    /**
     * @param int $maxTimes 最大重试次数
     * @param int $second   延迟执行时间(秒)
     * @return RetryJob
     */
    protected function retryWithFailed(int $maxTimes, int $second = 0)
    {
        return $this->retry($maxTimes, $second, function () {
            $this->failed(new RabbitMQJobException(static::class . ' has been attempted too many times or run too long. The job may have previously timed out. {"exception":"[object] (' . RabbitMQJobException::class . '(code: 0): ' . static::class . ' has been attempted too many times or run too long. The job may have previously timed out.)[stacktrace]'));
        });
    }
    
    /**
     * @return RetryJob
     */
    private function retryDispatch()
    {
        return (new RetryJob(static::class, $this->params))
            ->onQueue($this->queue)
            ->expand($this->expand);
    }
    
    /**
     * Whether a offset exists
     * @link https://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset <p>
     * An offset to check for.
     * </p>
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     * @since 5.0.0
     */
    public function offsetExists($offset)
    {
        return isset($this->params[$offset]);
    }
    
    /**
     * Offset to retrieve
     * @link https://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset <p>
     * The offset to retrieve.
     * </p>
     * @return mixed Can return all value types.
     * @since 5.0.0
     */
    public function offsetGet($offset)
    {
        return $this->params[$offset] ?? null;
    }
    
    /**
     * Offset to set
     * @link https://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param mixed $value <p>
     * The value to set.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetSet($offset, $value)
    {
        throw new RuntimeException('can not set any property in ' . static::class . '.');
    }
    
    /**
     * Offset to unset
     * @link https://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetUnset($offset)
    {
        throw new RuntimeException('can not unset any property in ' . static::class . '.');
    }
}