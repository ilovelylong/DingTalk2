<?php

/*
 * @Author: sunkaiyuan 
 * @Date: 2021-11-15 16:13:50 
 * @Last Modified by: sunkaiyuan
 * @Last Modified time: 2021-11-23 11:02:04
 */

namespace EasyDingTalk\App;

use EasyDingTalk\Kernel\Client as BaseClient;
use Symfony\Component\HttpFoundation\RedirectResponse;
use EasyDingTalk\Kernel\Exceptions\InvalidArgumentException;
use EasyDingTalk\Kernel\Exceptions\RuntimeException;
use function EasyDingTalk\tap;
use Symfony\Component\HttpFoundation\Response;
use EasyDingTalk\Kernel\Traits\HasStateParameter;

class Client extends BaseClient
{
    use HasStateParameter;

    /**
     * @var array
     */
    protected $credential;

    /**
     * @var bool
     */
    protected $withQrConnect = false;

    /**
     * @var string|null
     */
    protected $redirect;

    /**
     * @param string $name
     *
     * @return $this
     */
    // public function use($name)
    // {
    //     $this->app['config'] = $this->app['config']->get('app')[$name];
    //     $this->app['encryptor']->use($name);
    //     return $this;
    // }
    //callable
    protected $handlers = [];
    /**
     * @return string
     */
    public function getRedirectUrl()
    {
        return $this->redirect ?: $this->app['config']['redirect'];
    }

    /**
     * @param string $url
     *
     * @return $this
     */
    public function setRedirectUrl($url)
    {
        $this->redirect = $url;

        return $this;
    }

    /**
     * @return $this
     */
    public function withQrConnect()
    {
        $this->withQrConnect = true;
        $this->app['config']['scope'] = 'snsapi_login';
        return $this;
    }

    /**
     * Redirect to the authentication page.
     *
     * @param string|null $url
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function redirect($url = null)
    {
        $query = [

            'appid' => $this->app['config']['app_key'],
            'response_type' => 'code',
            'scope' => $this->app['config']['scope'] == 'snsapi_login' ? 'snsapi_login' : 'snsapi_auth',
            'state' => $this->makeState(),
            'redirect_uri' => $url ?: $this->getRedirectUrl(),
        ];

        return new RedirectResponse(
            sprintf('https://oapi.dingtalk.com/connect/%s?%s', $this->withQrConnect ? 'qrconnect' : 'oauth2/sns_authorize', http_build_query(array_filter($query)))
        );
    }

    /**
     * @return array
     *
     * @throws \EasyDingTalk\Auth\InvalidStateException
     */
    public function user()
    {
        if (!$this->hasValidState($this->app['request']->get('state'))) {
            throw new InvalidStateException();
        }

        $data = [
            'tmp_auth_code' => $this->app['request']->get('code'),
        ];

        $query = [
            'accessKey' => $this->app['config']['app_key'],
            'timestamp' => $timestamp = (int) microtime(true) * 1000,
            'signature' => $this->signature($timestamp),
        ];

        return $this->postJson('sns/getuserinfo_bycode', $data, $query);
    }

    /**
     * 计算签名
     *
     * @param int $timestamp
     *
     * @return string
     */
    public function signature($timestamp)
    {
        return base64_encode(hash_hmac('sha256', $timestamp, $this->app['config']['app_secret'], true));
    }


    /**
     * Handle the request.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function callback()
    {
        foreach ($this->handlers as $handler) {
            $handler->__invoke($this->getPayload());
        }

        $this->app['logger']->debug('Request received: ', [
            'method' => $this->app['request']->getMethod(),
            'uri' => $this->app['request']->getUri(),
            'content' => $this->app['request']->getContent(),
        ]);

        return tap(new Response(
            $this->app['encryptor']->encrypt('success'),
            200,
            ['Content-Type' => 'application/json']
        ), function ($response) {
            $this->app['logger']->debug('Response created:', ['content' => $response->getContent()]);
        });
    }



    /**
     * Handle the request.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function serve()
    {
        foreach ($this->handlers as $handler) {
            $handler->__invoke($this->getPayload());
        }

        $this->app['logger']->debug('Request received: ', [
            'method' => $this->app['request']->getMethod(),
            'uri' => $this->app['request']->getUri(),
            'content' => $this->app['request']->getContent(),
        ]);

        return tap(new Response(
            $this->app['encryptor']->encrypt('success'),
            200,
            ['Content-Type' => 'application/json']
        ), function ($response) {
            $this->app['logger']->debug('Response created:', ['content' => $response->getContent()]);
        });
    }



    /**
     * Push handler.
     *
     * @param \Closure|string|object $handler
     *
     * @return void
     *
     * @throws \EasyDingTalk\Kernel\Exceptions\InvalidArgumentException
     */
    public function push($handler)
    {
        if (is_string($handler)) {
            $handler = function ($payload) use ($handler) {
                return (new $handler($this->app))->__invoke($payload);
            };
        }

        if (!is_callable($handler)) {
            throw new InvalidArgumentException('Invalid handler');
        }

        array_push($this->handlers, $handler);
    }

    /**
     * Get request payload.
     *
     * @return array
     */
    public function getPayload()
    {
        $payload = json_decode($this->app['request']->getContent(), true);

        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new RuntimeException('No payload received');
        }

        $result = $this->app['encryptor']->decrypt(
            $payload['encrypt'],
            $this->app['request']->get('signature'),
            $this->app['request']->get('nonce'),
            $this->app['request']->get('timestamp')
        );

        return json_decode($result, true);
    }
}
