<?php
/**
 * Author: 沧澜
 * Date: 2019-12-30
 */

namespace VladimirYuldashev\LaravelQueueRabbitMQ\Component;


class ClassSerialize
{
    /**
     * to convert
     * @param string $search
     * @param string $replace
     * @param string $subject
     * @return string
     */
    public static function convert(string $search, string $replace, string $subject)
    {
        $searchStr = 'O:' . strlen($search) . ':"' . $search . '"';
        $replaceStr = 'O:' . strlen($replace) . ':"' . $replace . '"';
        return str_replace($searchStr, $replaceStr, $subject);
    }
}