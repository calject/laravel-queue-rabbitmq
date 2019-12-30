<?php
/**
 * Author: 沧澜
 * Date: 2019-12-30
 */

namespace VladimirYuldashev\LaravelQueueRabbitMQ\Component;


use ArrayAccess;
use Closure;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Throwable;

class JobParams implements ArrayAccess
{
    /**
     * @var array
     */
    protected $data = [];
    
    /**
     * @var array
     */
    protected $dataInstances = [];
    
    /**
     * @var Closure
     */
    protected $validateError;
    
    /**
     * 默认值
     * @var mixed
     */
    protected $defaultValue = null;
    
    /**
     * JobParams constructor.
     * @param array $data
     * @param null $defaultValue
     */
    public function __construct(array $data, $defaultValue = null)
    {
        $this->data = $data;
        $this->defaultValue = $defaultValue;
    }
    
    /**
     * @param $key
     * @param null $defaultValue
     * @return mixed|JobParams
     */
    public function with($key, $defaultValue = null)
    {
        $jobParams = &$this->dataInstances[$key];
        if (!$jobParams) {
            $jobParams = new static((array)($this->data[$key] ?? []), $defaultValue);
        }
        return $jobParams;
    }
    
    /**
     * 验证当前data字段数据
     * @param array $rules
     * @param Closure|null $error function(ValidationException $exception) {}
     * @return JobParams
     * @throws Throwable
     */
    public function validate(array $rules, Closure $error = null)
    {
        try {
            /* ======== 验证参数 ======== */
            Validator::make($this->data, $rules)->validate();
        } catch (ValidationException $exception) {
            $error && (call_user_func($error, $exception) || 1)
            || $this->validateError && (call_user_func($this->validateError, $error) || 1)
            || h_throw($exception);
        }
        return $this;
    }
    
    /**
     * 验证当前data任意字段数据
     * @param array $keyRules
     * @param Closure $error    function($key, ValidationException $exception) {}
     */
    public function validateMany(array $keyRules, Closure $error = null)
    {
        try {
            foreach ($keyRules as $key => $rule) {
                Validator::make((array)($this->data[$key] ?? []), $rule)->validate();
            }
        } catch (ValidationException $exception) {
            $error && call_user_func_array($error, [$key, $exception]);
        }
    }
    
    
    /**
     * @param string $key
     * @param mixed $default
     * @return mixed|null
     */
    public function input(string $key = null, $default = null)
    {
        return $key ? $this->data : ($this[$key] ?? $default);
    }
    
    /**
     * @return array
     */
    public function all(): array
    {
        return $this->data;
    }
    
    /**
     * @param Closure $error    function(ValidationException $exception) {}
     * @return $this
     */
    public function error(Closure $error)
    {
        $this->validateError = $error;
        return $this;
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
        return isset($this->data[$offset]);
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
        return $this->data[$offset] ?? $this->defaultValue;
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
        $this->data[$offset] = $value;
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
        unset($this->data[$offset]);
    }
    
    /**
     * @return array
     */
    public function __invoke()
    {
        return $this->all();
    }
}