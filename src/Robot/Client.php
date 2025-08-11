<?php

/*
 * @Author: sunkaiyuan 
 * @Date: 2021-11-15 16:13:50 
 * @Last Modified by: sunkaiyuan
 * @Last Modified time: 2021-11-30 19:35:47
 */

namespace EasyDingTalk\Robot;

use EasyDingTalk\Kernel\Client as BaseClient;
use Symfony\Component\HttpFoundation\RedirectResponse;
use EasyDingTalk\Kernel\Exceptions\InvalidArgumentException;
use EasyDingTalk\Kernel\Exceptions\RuntimeException;
use function EasyDingTalk\tap;
use Symfony\Component\HttpFoundation\Response;

class Client extends BaseClient
{

    //连接版本
    protected $uriVersion;
    //待发送的消息
    protected $msg;
    //string 机器人群消息token
    protected $webHookToken = '';
    protected $app_secret = '';

    protected $url;
    protected $query = [];
    //callable
    protected $handlers = [];
    /**
     * Handle the request.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function serve()
    {
        //验证签名https://developers.dingtalk.com/document/robots/receive-message
        if (!$this->verification())
            return false;

        foreach ($this->handlers as $handler) {
            $data[] = $handler->__invoke($this->getPayload());
        }

        return tap(new Response(
            $data[count($data) - 1],
            200,
            ['Content-Type' => 'application/json']
        ), function ($response) {
            // $this->app['logger']->debug('Response created:', ['content' => $response->getContent()]);
        });
    }
    //验证签名
    public function verification()
    {
        $heads = $this->app['request']->server->getHeaders();
        if (
            $heads['SIGN'] !=  $this->signature($heads['TIMESTAMP']) //签名不正确
            || abs($heads['TIMESTAMP'] / 1000 - time()) > 3600 //时间戳过期
        ) {
            // $this->app['logger']->debug('received:', [
            //     'error' => '签名不合法',
            //     'headers' => $this->app['request']->server->getHeaders(),
            //     'method' => $this->app['request']->getMethod(),
            //     'uri' => $this->app['request']->getUri(),
            //     'content' => $this->app['request']->getContent(),
            // ]);
            return false;
        }
        return true;
    }
    //单聊天消息
    public function singleMsg($msg = [], $user = '')
    {
        return  $this->batchMsg($msg, $user);
    }
    //群发单聊消息

    // 单聊天{
    //     "robotCode" : "dingxxxxxx",
    //     "userIds" : [ "manager1234" ],
    //     "msgKey" : "sampleMarkdown",
    //     "msgParam" : "{\"text\": \"hello text\",\"title\": \"hello title\"}"
    //   }
    public function batchMsg($msg, $userIds = [])
    {
        $this->uriVersion = 'v1';
        $this->msg['robotCode'] = $this->app['config']->get('app_key');
        $this->msg['userIds'] = $this->mergeUser(isset($this->msg['userIds']) ? $this->msg['userIds'] : [], $userIds);
        $this->msg['msgKey'] = $msg['msgKey'];
        $this->msg['msgParam'] = $msg['msgParam'];
        $this->url = "/v1.0/robot/oToMessages/batchSend";

        return $this->send();
    }

    public function send()
    {
        $this->app['logger']->debug('Request created:', ['content' => $this->msg]);
        return tap($this->postJson($this->url, $this->msg, $this->query), function ($Response) {
            $this->app['logger']->debug('Response received: ', [
                'content' => $Response,
            ]);
        });
    }
    public function mergeUser($defuserIds, $userIds)
    {
        return [...$defuserIds, ...$userIds];
    }
    /**
     * @user
     *
     * @param array|string $phones
     * @return Client
     * @static 
     */
    public function atPhones($phones)
    {
        $this->msg['at']['atMobiles'] =  $this->mergeUser(isset($this->msg['at']['atMobiles']) ? $this->msg['at']['atMobiles'] : [], $phones);
        return $this;
    }
    /**
     * @user
     *
     * @param array|string $userids
     * @return Client
     * @static 
     */
    public function atUserids($userids)
    {
        $this->msg['at']['atUserIds'] = $this->mergeUser(isset($this->msg['at']['atUserIds']) ? $this->msg['at']['atUserIds'] : [], $userids);
        return $this;
    }

    public function atAll()
    {
        $this->msg['at']['isAtAll'] = true;
        return $this;
    }


    public function setWebHookToken($webHookToken)
    {
        $this->webHookToken = $webHookToken;
        return $this;
    }

    public function setSppSecret($app_secret)
    {
        $this->app_secret = $app_secret;
        return $this;
    }
    public function sendGroup($message)
    {

        $timestamp = time() . '000';
        $this->uriVersion = 'base_uri';
        $this->url = '/robot/send';
        $this->query = [
            'sign' => urlencode($this->signature($timestamp)),
            'timestamp' => $timestamp,
            'access_token' => $this->webHookToken
        ];
        $this->msg = $message;
        $this->popMiddleware("access_token");
        return  $this->send();
    }


    /**
     * Push handler.
     *
     * @param \Closure|string|object $handler
     *
     * @return text、markdown、整体跳转actionCard类型、独立跳转actionCard类型、feedCard，false
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
     * 计算签名
     *
     * @param int $timestamp
     *
     * @return string
     */
    public function signature($timestamp)
    {
        $key = $this->app_secret != "" ? $this->app_secret : $this->app['config']['app_secret'];
        return base64_encode(hash_hmac('sha256', $timestamp . "\n" . $key,  $key, true));
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
        return $payload;
    }
}
