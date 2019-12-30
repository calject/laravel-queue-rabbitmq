<?php
/**
 * Author: 沧澜
 * Date: 2019-12-30
 */

namespace VladimirYuldashev\LaravelQueueRabbitMQ\Component;


use Closure;

class OptCheck
{
    /**
     * @var int
     */
    protected $opts = 0;
    
    /**
     * OptCheck constructor.
     * @param int $opts
     */
    public function __construct(int $opts)
    {
        $this->opts = $opts;
    }
    
    /**
     * @param int $opts
     * @return OptCheck
     */
    public static function make(int $opts)
    {
        return new static($opts);
    }
    
    /**
     * @param int $check
     * @return bool
     */
    public function check(int $check): bool
    {
        return ($this->opts & $check) === $check;
    }
    
    /**
     * @param int $check
     * @param Closure $run
     * @return mixed
     */
    public function checkRun(int $check, Closure $run)
    {
        return $this->check($check) ? call_user_func($run) : null;
    }
    
    /**
     * @param mixed ...$args
     * @return bool
     */
    public function checkOr(... $args): bool
    {
        $args = (func_num_args() == 1 && is_array($args[0])) ? $args[0] : $args;
        $check = 0;
        foreach ($args as $arg) {
            $check &= $arg;
        }
        return (bool)($this->opts & $check);
    }
    
    /**
     * @param mixed ...$args
     * @return bool
     */
    public function checkAnd(... $args): bool
    {
        $args = (func_num_args() == 1 && is_array($args[0])) ? $args[0] : $args;
        $check = 0;
        foreach ($args as $arg) {
            $check &= $arg;
        }
        return ($this->opts & $check) === $check;
    }
    
    
    
}