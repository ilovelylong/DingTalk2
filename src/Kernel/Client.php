<?php

/*
 * This file is part of the EasyDingTalk/http.
 *
 * (c) EasyDingTalk <i@EasyDingTalk.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyDingTalk\Kernel;

use GuzzleHttp\Client as GuzzleClient;
use EasyDingTalk\Kernel\Traits\HasHttpRequests;
use GuzzleHttp\Middleware;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Class Client.
 *
 * @author EasyDingTalk <i@EasyDingTalk.me>
 */
class Client
{
    use HasHttpRequests {
        request as performRequest;
    }
    /**
     * @var \EasyDingTalk\Kernel\Config
     */
    protected $config;


    /**
     * @var
     */
    protected $uriVersion = 'base_uri';


    public function setVersion($uriVersion)
    {
        $this->uriVersion = $uriVersion;
        return $this;
    }

    /**
     * Client constructor.
     *
     * @param \EasyDingTalk\Kernel\Config|array $config
     */
    public function __construct($app, $withToken = true)
    {
        $this->app = $app;
        $this->setVersion($this->uriVersion)->withAccessTokenMiddleware($withToken)->withRetryMiddleware();
        $this->config = $this->normalizeConfig($this->app['config']->toArray());
    }

    /**
     * @param $name
     *
     * @return mixed
     */
    public function __get($name)
    {
        // if (isset($this->app['config'][$name])) {
        //     $this->app->extendconfig = $this->app['config'][$name];
        //     $apptype = $this->app->extendconfig;
        // } else $apptype = $name;
        return $this->app[$name];
    }

    /**
     * GET request.
     *
     * @param string $url
     * @param array  $query
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyDingTalk\Kernel\Support\Collection|array|object|string
     */
    public function _GET(string $url, array $query = [])
    { 
        return $this->request($url, 'GET', ['query' => $query]);
    }


    /**
     * DELETE request.
     *
     * @param string $url
     * @param array  $query
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyDingTalk\Kernel\Support\Collection|array|object|string
     */
    public function _DELETE(string $url, array $query = [])
    {
        return $this->request($url, 'DELETE', ['query' => $query]);
    }


    /**
     * PUT request.
     *
     * @param string $url
     * @param array  $query
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyDingTalk\Kernel\Support\Collection|array|object|string
     */
    public function _PUT(string $url, array $data = [])
    {
        return $this->request($url, 'PUT', ['form_params' => $data]);
    }
    /**
     * POST request.
     *
     * @param string $url
     * @param array  $data
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyDingTalk\Kernel\Support\Collection|array|object|string
     */
    public function _POST(string $url, array $data = [])
    {
        return $this->request($url, 'POST', ['form_params' => $data]);
    }

    /**
     * JSON request.
     *
     * @param string       $url
     * @param string|array $data
     * @param array        $query
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyDingTalk\Kernel\Support\Collection|array|object|string
     */
    public function postJson(string $url, array $data = [], array $query = [])
    {
        return $this->request($url, 'POST', ['query' => $query, 'json' => $data]);
    }

    /**
     * Upload file.
     *
     * @param string $url
     * @param array  $files
     * @param array  $form
     * @param array  $query
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyDingTalk\Kernel\Support\Collection|array|object|string
     */
    public function upload(string $url, array $files = [], array $form = [], array $query = [])
    {
        $multipart = [];

        foreach ($files as $name => $contents) {
            $contents = \is_resource($contents) ?: \fopen($contents, 'r');
            $multipart[] = \compact('name', 'contents');
        }

        foreach ($form as $name => $contents) {
            $multipart = array_merge($multipart, $this->normalizeMultipartField($name, $contents));
        }

        return $this->request($url, 'POST', ['query' => $query, 'multipart' => $multipart]);
    }

    /**
     * @param string $uri
     * @param string $method
     * @param array  $options
     * @param bool   $returnRaw
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyDingTalk\Kernel\Support\Collection|array|object|string
     */
    public function request(string $uri, string $method = 'GET', array $options = [], $returnRaw = false)
    {

        if ($this->config->getBaseUri($this->uriVersion) && $this->config->needAutoTrimEndpointSlash()) {
            $uri = ltrim($uri, '/');
        }

        $response = $this->performRequest($uri, $method, $options);

        return $this->castResponseToType(
            $response,
            $returnRaw ? 'raw' : $this->config->getOption('response_type')
        );
    }

    /**
     * @param string $url
     * @param string $method
     * @param array  $options
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     *
     * @return array|object|\EasyDingTalk\Kernel\Support\Collection|\Psr\Http\Message\ResponseInterface|string
     */
    public function requestRaw(string $url, string $method = 'GET', array $options = [])
    {
        return $this->request($url, $method, $options, true);
    }

    /**
     * Return GuzzleHttp\Client instance.
     *
     * @return \GuzzleHttp\ClientInterface
     */
    public function getHttpClient(): \GuzzleHttp\ClientInterface
    {
        if (!$this->httpClient) {
            $this->httpClient = new GuzzleClient($this->config->getOptions($this->uriVersion));
        }

        return $this->httpClient;
    }

    /**
     * @return \EasyDingTalk\Kernel\Config
     */
    public function getConfig(): \EasyDingTalk\Kernel\Config
    {

        return $this->config;
    }

    /**
     * @param \EasyDingTalk\Kernel\Config $config
     *
     * @return \EasyDingTalk\Kernel\Client
     */
    public function setConfig(\EasyDingTalk\Kernel\Config $config): self
    {
        $this->config = $config;

        return $this;
    }

    /**
     * @param string $name
     * @param mixed  $contents
     *
     * @return array
     */
    public function normalizeMultipartField(string $name, $contents)
    {
        $field = [];

        if (!is_array($contents)) {
            return [compact('name', 'contents')];
        }
        foreach ($contents as $key => $value) {
            $key = sprintf('%s[%s]', $name, $key);
            $field = array_merge($field, is_array($value) ? $this->normalizeMultipartField($key, $value) : [['name' => $key, 'contents' => $value]]);
        }

        return $field;
    }

    /**
     * @param mixed $config
     *
     * @return \EasyDingTalk\Kernel\Config
     */
    protected function normalizeConfig($config): \EasyDingTalk\Kernel\Config
    {
        if (\is_array($config)) {
            $config = new Config($config);
        }

        if (!($config instanceof Config)) {
            throw new \InvalidArgumentException('config must be array or instance of EasyDingTalk\Kernel\Config.');
        }

        return $config;
    }


    /**
     * @return $this
     */
    public function withAccessTokenMiddleware($withToken)
    {

        if (isset($this->getMiddlewares()['access_token']) || !$withToken) {

            return $this;
        }

        $middleware = function (callable $handler) {

            return function (RequestInterface $request, array $options) use ($handler) {

                if ($this->app['access_token'] && $this->uriVersion == 'base_uri') {
                    parse_str($request->getUri()->getQuery(), $query);

                    $request = $request->withUri(
                        $request->getUri()->withQuery(http_build_query(['access_token' => $this->app['access_token']->getToken()] + $query))
                    );
                } else
                if ($this->uriVersion != 'base_uri') {
                    $request =  $request->withHeader('x-acs-dingtalk-access-token', $this->app['access_token']->getToken());
                }

                return $handler($request, $options);
            };
        };

        $this->pushMiddleware($middleware, 'access_token');

        return $this;
    }

    /**
     * @return $this
     */
    public function withRetryMiddleware()
    {
        if (isset($this->getMiddlewares()['retry'])) {
            return $this;
        }

        $middleware = Middleware::retry(function ($retries, RequestInterface $request, ResponseInterface $response = null) {
            if (is_null($response) || $retries < 1) {
                return false;
            }

            if (in_array(json_decode($response->getBody(), true)['errcode'] ?? null, [40001])) {
                $this->app['access_token']->refresh();

                return true;
            }
        });

        $this->pushMiddleware($middleware, 'retry');

        return $this;
    }
}
