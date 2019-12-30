<?php
/**
 * Author: 沧澜
 * Date: 2019-12-30
 */

namespace VladimirYuldashev\LaravelQueueRabbitMQ\Component;


class Options
{
    /**
     * 删除配置参数
     * @param int $options
     * @param int $deletes
     * @return int
     */
    public static function delete(int &$options, int $deletes): int
    {
        return $options &= ~$deletes;
    }
    
    /**
     * 添加配置参数
     * @param int $options
     * @param int $adds
     * @return int
     */
    public static function add(int &$options, int $adds): int
    {
        return $options |= $adds;
    }
    
    /**
     * 检查配置项
     * @param int $options
     * @param int $check
     * @return bool
     */
    public static function check(int $options, int $check): bool
    {
        return ($options & $check) === $check;
    }
    
}