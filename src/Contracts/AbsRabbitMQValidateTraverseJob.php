<?php
/**
 * Author: 沧澜
 * Date: 2019-12-30
 */

namespace VladimirYuldashev\LaravelQueueRabbitMQ\Contracts;

use Illuminate\Validation\ValidationException;
use InvalidArgumentException;
use Throwable;
use VladimirYuldashev\LaravelQueueRabbitMQ\Component\JobParams;

abstract class AbsRabbitMQValidateTraverseJob extends AbsRabbitMQJob
{
    
    /**
     * @var JobParams
     */
    protected $jobParams;
    
    /**
     * job handle
     */
    public function handle()
    {
        $this->jobParams = new JobParams($this->all());
        try {
            /* ======== 验证参数 ======== */
            $this->validate($this->jobParams);
            $this->doHandler($this->jobParams);
        } catch (ValidationException $exception) {
            $this->validateError($exception);
        }
    }
    
    /**
     * 队列执行处理
     * @param JobParams $params
     * @return mixed
     */
    abstract protected function doHandler(JobParams $params);
    
    /**
     * 验证规则
     * @param JobParams $validator
     * @throws ValidationException|Throwable
     */
    abstract protected function validate(JobParams $validator);
    
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