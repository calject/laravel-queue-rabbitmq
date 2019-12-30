<?php
/**
 * Author: 沧澜
 * Date: 2019-12-30
 */

namespace VladimirYuldashev\LaravelQueueRabbitMQ\Constants;

/**
 * Interface RabbitMQEvent
 * @package VladimirYuldashev\LaravelQueueRabbitMQ\Constants
 */
interface RabbitMQEvent
{
    /* ======== predefined ======== */
    const JOB_NONE          = 0;
    const JOB_ALL           = (1 << 5) - 1;
    const JOB_ENV_DEBUG     = self::JOB_ALL;
    const JOB_ENV_PRODUCE   = self::JOB_RUN_EXCEPTION & self::JOB_HANDLE;
    
    /* ======== RabbitMQJob Event const ======== */
    const JOB_CREATE        = 1;
    const JOB_HANDLE        = 1 << 1;
    const JOB_BEFORE_RUN    = 1 << 2;
    const JOB_AFTER_RUN     = 1 << 3;
    const JOB_RUN_EXCEPTION = 1 << 4;
    
}