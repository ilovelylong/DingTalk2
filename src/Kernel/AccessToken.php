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

use EasyDingTalk\Kernel\Exceptions\InvalidCredentialsException;
use EasyDingTalk\Kernel\Client;
use function EasyDingTalk\tap;
use EasyDingTalk\Kernel\Traits\ResponseCastable;

class AccessToken
{
    use Concerns\InteractsWithCache, ResponseCastable;

    /**
     * @var \EasyDingTalk\Application
     */
    protected $app;

    /**
     * AccessToken constructor.
     *
     * @param \EasyDingTalk\Application
     */
    public function __construct($app)
    {
        $this->app = $app;
    }

    /**
     * 获取钉钉 AccessToken
     *
     * @return array
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function get()
    {
        if ($value = $this->getCache()->get($this->cacheFor())) {
            return $value;
        }

        return $this->refresh();
    }

    /**
     * 获取 AccessToken
     *
     * @return string
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function getToken()
    {
        return $this->get()['accessToken'];
    }

    /**
     * 刷新钉钉 AccessToken
     *
     * @return array
     */
    public function refresh()
    {
        $response = (new Client($this->app, false))->setVersion("v1")->requestRaw('/v1.0/oauth2/accessToken', 'POST', ['json' => [
            'appKey' => $this->app['config']->get('app_key'),
            'appSecret' => $this->app['config']->get('app_secret'),
        ]]);

        return tap($this->castResponseToType($response, 'array'), function ($value) {

            if (isset($value['errcode']) && 0 !== $value['errcode']) {
                throw new InvalidCredentialsException(json_encode($value));
            }
            $this->getCache()->set($this->cacheFor(), $value, $value['expireIn']);
        });
    }

    /**
     * 缓存 Key
     *
     * @return string
     */
    protected function cacheFor()
    {
        return sprintf('access_token.%s', $this->app['config']->get('app_key'));
    }
}
