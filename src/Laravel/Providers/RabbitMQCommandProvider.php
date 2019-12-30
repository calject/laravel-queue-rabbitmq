<?php
/**
 * Author: 沧澜
 * Date: 2019-12-30
 */

namespace VladimirYuldashev\LaravelQueueRabbitMQ\Laravel\Providers;

use Illuminate\Support\ServiceProvider;
use VladimirYuldashev\LaravelQueueRabbitMQ\Laravel\Console\Commands\RabbitMQInstallCommand;
use VladimirYuldashev\LaravelQueueRabbitMQ\Laravel\Console\Commands\RabbitMQMapCommand;

class RabbitMQCommandProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot() {
        if ($this->app->runningInConsole()) {
            $this->commands([
                RabbitMQInstallCommand::class,
                RabbitMQMapCommand::class,
            ]);
        }
    }
    
    /**
     * Register the application services.
     *
     * @return void
     */
    public function register() {
    
    
    }
}