<?php
/**
 * Author: 沧澜
 * Date: 2019-12-30
 */

namespace VladimirYuldashev\LaravelQueueRabbitMQ\Laravel\Console\Commands;

use Illuminate\Console\Command;
use VladimirYuldashev\LaravelQueueRabbitMQ\Component\Folder;
use VladimirYuldashev\LaravelQueueRabbitMQ\Component\Snippets\SnippetArray;

class RabbitMQMapCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'calject:rabbitmq:map {dir?}';
    
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'RabbitMQ map 参数: dir:生成路径参数，多个目录使用,分割，默认目录为app/Jobs';
    
    /**
     * @var string
     */
    protected $mapPath = '';
    
    /**
     * @var string
     */
    protected $jobNamespace = 'App\\Jobs\\';
    
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $options = $this->argument();
        $root = app_path().'/Jobs';
        $this->mapPath = config_path('rabbitmq/map/');
        if (!is_dir($this->mapPath)) {
            mkdir($this->mapPath, 0755, true);
        }
        if (isset($options['dir']) && $options['dir']) {
            array_map(function ($dir) use ($root) {
                is_dir($root.'/'.$dir) && $this->create($root.'/'.$dir);
            }, explode(',', $options['dir']));
        } else {
            (new Folder($root))->eachDirs(function ($index, $dir) {
                $this->create($dir);
            });
        }
        echo "create finish \n";
    }
    
    /**
     * @param string $path
     * @return array
     */
    protected function create(string $path)
    {
        $map = [];
        $namespace = $this->jobNamespace.basename($path).'\\';
        (new Folder($path))->eachFiles(function ($index, $file) use ($namespace, &$map) {
            $jobName = rtrim(basename($file), '.php');
            $jobClass = $namespace.$jobName;
            $job = app()->make($jobClass);
            if ($job->alias ?? '') {
                $map['alias'][$job->alias] = $jobClass;
            }
            $map['class'][$jobName] = $jobClass;
        });
        
        $sniArr = SnippetArray::create($map['class'] + ($map['alias'] ?? []));
        $sniArr->callable(function ($key, $value) {
            return "'${key}' => '${value}',";
        });
        $strReader = $sniArr->addPhpHead()->head('return ')->end(';')->get();
        file_put_contents($this->mapPath.strtolower(basename($path)).'.php', $strReader);
    }
    
}