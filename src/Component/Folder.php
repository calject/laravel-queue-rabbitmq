<?php
/**
 * Author: 沧澜
 * Date: 2019-12-30
 */

namespace VladimirYuldashev\LaravelQueueRabbitMQ\Component;

use Closure;
use Generator;

class Folder
{
    
    /**
     * 读取的文件路径
     * @var string
     */
    protected $dir = '';
    
    /**
     * GeneratorFileLoad constructor.
     * @param string $dir
     */
    public function __construct($dir)
    {
        $this->dir = realpath($dir);
    }
    
    /**
     * do handle
     * @param Closure $handle function($index, $value) {}
     */
    public function eachFiles(Closure $handle)
    {
        GeneratorLoad::each($this->readEachFiles(), $handle);
    }
    
    /**
     * @param Closure $handle function($index, $value) {}
     */
    public function eachDirs(Closure $handle)
    {
        GeneratorLoad::each($this->readEachDir(), $handle);
    }
    
    /**
     * @return array
     */
    public function readToArray(): array
    {
        $files = [];
        GeneratorLoad::each($this->readEachFiles(), function ($index, $value) use (&$files) {
            $files[] = $value;
        });
        return $files;
    }
    
    /**
     * @return array
     */
    public function readDirToArray(): array
    {
        $dirs = [];
        GeneratorLoad::each($this->readEachDir(), function ($index, $value) use (&$dirs) {
            $dirs[] = $value;
        });
        return $dirs;
    }
    
    /**
     * 读取目录下所有文件名
     * @return Generator
     */
    public function readEachFiles()
    {
        return $this->handleReadEachFiles($this->dir);
    }
    
    /**
     * @return Generator
     */
    public function readEachDir()
    {
        return $this->handleReadDir($this->dir);
    }
    
    /**
     * 读取目录下所有文件夹
     * @param string $dir
     * @return Generator
     */
    private function handleReadDir(string $dir)
    {
        /* ======== 转绝对路径 ======== */
        if ($handle = opendir($dir)) {
            while (($fl = readdir($handle)) !== false) {
                $temp = $dir . DIRECTORY_SEPARATOR . $fl;
                if (in_array($fl, ['.', '..']))
                    continue;
                if (is_dir($temp))
                    yield $temp;
            }
        }
    }
    
    
    /**
     * 读取目录下所有文件名
     * @param string $dir 递归查找的路径名
     * @return Generator
     */
    private function handleReadEachFiles(string $dir)
    {
        /* ======== 转绝对路径 ======== */
        if ($handle = opendir($dir)) {
            while (($fl = readdir($handle)) !== false) {
                $temp = $dir . DIRECTORY_SEPARATOR . $fl;
                if (in_array($fl, ['.', '..']))
                    continue;
                is_dir($temp) ? yield $this->handleReadEachFiles($temp) : yield $temp;
            }
        }
    }
}