<?php
/**
 * Author: 沧澜
 * Date: 2019-12-30
 */

namespace VladimirYuldashev\LaravelQueueRabbitMQ\Component\Snippets;


abstract class AbsSnippet
{
    
    const PHP_HEAD = "<?php";
    
    /**
     * 是否添加php头定义<?php
     * @var bool
     */
    protected $isAddPhpHead = false;
    
    /**
     * 设置添加php头定义
     * @param bool $is_add
     * @return $this
     */
    public function addPhpHead(bool $is_add = true)
    {
        $this->isAddPhpHead = $is_add;
        return $this;
    }
    
    /**
     * get the str buffer
     * @return string
     */
    public function get(): string
    {
        $str = $this->handle();
        if ($this->isAddPhpHead) {
            $str = self::PHP_HEAD."\n\n".$str;
        }
        return $str;
    }
    
    /**
     * 字符处理
     * @return string
     */
    abstract protected function handle(): string ;
    
}