<?php
/**
 * Author: 沧澜
 * Date: 2019-12-30
 */

namespace VladimirYuldashev\LaravelQueueRabbitMQ\Contracts;

use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;
use InvalidArgumentException;

abstract class AbsRabbitMQValidateJob extends AbsRabbitMQJob
{
    
    /**
     * job handle
     */
    public function handle()
    {
        try {
            /* ======== 验证参数 ======== */
            Validator::make($this->all(), $this->validate())->validate();
            $this->doHandler($this->all());
        } catch (ValidationException $exception) {
            $this->validateError($exception);
        }
    }
    
    /**
     * 队列执行处理
     * @param array $params
     * @return mixed
     */
    abstract protected function doHandler(array $params);
    
    /**
     * 验证规则
     * @return array
     */
    abstract protected function validate(): array;
    
    /**
     * 验证异常处理
     * @param ValidationException $exception
     * @return mixed
     */
    protected function validateError(ValidationException $exception)
    {
        throw new InvalidArgumentException(json_encode($exception->validator->errors()->all(), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
    }
    
 }