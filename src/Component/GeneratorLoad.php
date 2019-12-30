<?php
/**
 * Author: 沧澜
 * Date: 2019-12-30
 */

namespace VladimirYuldashev\LaravelQueueRabbitMQ\Component;


class GeneratorLoad
{
    /**
     * @param \Generator $list
     * @param \Closure $handle function($index, $value) {}
     */
    final public static function each(\Generator $list, \Closure $handle)
    {
        foreach ($list as $index => $value) {
            $value instanceof \Generator ? self::each($value, $handle) : call_user_func_array($handle, [$index, $value]);
        }
    }
}