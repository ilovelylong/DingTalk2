<?php

/*
 * @Author: sunkaiyuan 
 * @Date: 2021-11-17 16:28:57 
 * @Last Modified by: sunkaiyuan
 * @Last Modified time: 2021-11-17 18:01:38
 */

namespace EasyDingTalk\Kernel\Concerns;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

trait InteractsWithLogger
{
    /**
     * @var Monolog\Logger
     */
    protected $logger;

    /**
     * @return Monolog\Logger
     */
    public function get()
    {
        if ($this->logger) {
            return $this->logger;
        }

        if (property_exists($this, 'logger') && $this->app->offsetExists('logger') && ($this->app['logger'] instanceof Logger)) {
            return $this->logger = $this->app['logger'];
        }

        return $this->logger = $this->createDefaultCache();
    }

    public function set($logger)
    {
        $this->logger = $logger;
    }
    /**
     * @return Monolog\Logger
     */
    protected function createDefaultLog()
    {
        return new Logger(
            'EasyDingTalk',
            [new StreamHandler(
                isset($this->app->config['log']['path']) ? $this->app->config['log']['path'] : sys_get_temp_dir() . '/EasyDingTalk.log',
                isset($this->app->config['log']['level']) ? $this->app->config['log']['level'] : 'debug'
            )]
        );
    }
}
