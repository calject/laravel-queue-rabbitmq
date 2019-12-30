<?php
/**
 * Author: 沧澜
 * Date: 2019-12-30
 */

namespace VladimirYuldashev\LaravelQueueRabbitMQ\Laravel\Console\Commands;

use Illuminate\Console\Command;

class RabbitMQInstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'calject:rabbitmq:install {terminal?}';
    
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'RabbitMQ init 参数: --c|consumer:"consumer init" --p|producer:"producer init"';
    
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
        $checkCommand = function ($key, array $comments) use ($options): bool {
            return isset($options[$key]) && in_array($options[$key], $comments);
        };
        if ($checkCommand('terminal', ['--c', 'consumer'])) {
            $config_path = dirname(__DIR__, 4).'/config/rabbitmq_consumer.php';
        } else if ($checkCommand('terminal', ['--p', 'consumer'])){
            $config_path = dirname(__DIR__, 4).'/config/rabbitmq_producer.php';
        } else {
            goto end;
        }
        $filePath = config_path('rabbitmq.php');
        $config_content = file_get_contents($config_path);
        /* ======== 写入到配置文件目录 ======== */
        file_put_contents($filePath, $config_content);
        end:
    }
    
    
}