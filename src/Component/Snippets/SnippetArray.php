<?php
/**
 * Author: 沧澜
 * Date: 2019-12-30
 */

namespace VladimirYuldashev\LaravelQueueRabbitMQ\Component\Snippets;

use VladimirYuldashev\LaravelQueueRabbitMQ\Component\Options;

class SnippetArray extends AbsSnippet
{
    /**
     * 字符集
     * @var string
     */
    protected $strBuffer = '';
    
    /**
     * 添加头
     * @var string
     */
    protected $strHead = '';
    
    /**
     * 设置结束符
     * @var string
     */
    protected $endSymbol = '';
    
    /**
     * tabulator key 跳格数
     * @var int
     */
    protected $tab = 1;
    
    /**
     * 参数
     * @var int
     */
    protected $options = 0;
    
    /**
     * 处理的数据
     * @var array
     */
    protected $data = [];
    
    /**
     * 处理回调
     * @var \Closure
     */
    protected $callable;
    
    
    /**
     * @param array $data
     * @param \Closure|null $callable
     * @return static
     */
    public static function create(array $data = [], \Closure $callable = null)
    {
        $instance = new static();
        $instance->with($data);
        $instance->callable($callable ?? function($key, $value) {
            return "'$key' => '$value',";
        });
        return $instance;
    }
    
    /**
     * 设置遍历的数据
     * @param array $data
     * @return $this
     */
    public function with(array $data)
    {
        $this->data = $data;
        return $this;
    }
    
    /**
     * 添加头字符
     * @param string $head
     * @return $this
     */
    public function head(string $head)
    {
        $this->strHead = $head;
        return $this;
    }
    
    /**
     * 设置结束符
     * @param string $symbol
     * @return $this
     */
    public function end(string $symbol = ';')
    {
        $this->endSymbol = $symbol;
        return $this;
    }
    
    /**
     * 设置水平跳格参数
     * @param int $tab
     * @return $this
     */
    public function tab(int $tab)
    {
        $this->tab = $tab;
        return $this;
    }
    
    /**
     * 设置配置参数
     * @param int $options      参数列表
     * @param bool $is_delete   是否为删除操作
     * @return $this
     */
    public function options(int $options, bool $is_delete = false)
    {
        if ($is_delete) {
            Options::delete($this->options, $options);
        }else {
            Options::add($this->options, $options);
        }
        return $this;
    }
    
    
    /**
     * 设置回调
     * @param \Closure $callable
     * @return $this
     */
    public function callable(\Closure $callable)
    {
        $this->callable = $callable;
        return $this;
    }
    
    
    /**
     * 字符处理
     * @return string
     */
    protected function handle(): string
    {
        $this->strBuffer .= $this->strTab(1) . $this->strHead . "[\n";
        $itemBuffer = '';
        foreach ($this->data as $key => $value) {
            $str = call_user_func_array($this->callable, [$key, $value]);
            if (!empty($str)) {
                $itemBuffer .= $this->strTab() . rtrim($str, ',') . ',' . "\n";
            }
        }
        $this->strBuffer .= $itemBuffer . $this->strTab(1) . ']' . $this->endSymbol;
        return $this->strBuffer;
    }
    
    /**
     * invoke
     * @return string
     */
    public function __invoke()
    {
        return $this->get();
    }
    
    /*---------------------------------------------- component function ----------------------------------------------*/
    
    /**
     * @param int $diff 位移差值
     * @return string
     */
    protected function strTab(int $diff = 0)
    {
        $tabNum = $this->tab - $diff;
        return str_repeat("\t", $tabNum < 0 ? 0 : $tabNum);
    }
    
    
}