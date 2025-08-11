<?php

/*
 * This file is part of the mingyoung/dingtalk.
 *
 * (c) 张铭阳 <mingyoungcheung@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyDingTalk\Kernel;

class Logger
    {
    use Concerns\InteractsWithLogger;
    use Concerns\InteractsWithCache;
    /**
     * @var \EasyDingTalk\Application
     */
    protected $app;

    /**
     * Logger constructor.
     *
     * @param \EasyDingTalk\Application
     */
    public function __construct($app)
        {
        $this->app = $app;
        }
    public function debug($key, $data)
        {
        if ($this->app->config['log']['enable']) {
            $this->cache->set($key, $data);
            }
        return;
        }

    public function __call($name, $a)
        {
        if ($this->logger == null)
            $this->logger = $this->get();
        $this->logger->$name(...$a);
        }
    }
