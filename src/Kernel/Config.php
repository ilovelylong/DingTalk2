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

/**
 * Class Config.
 *
 * @author EasyDingTalk <i@EasyDingTalk.me>
 */
class Config
{
    /**
     * @see http://docs.guzzlephp.org/en/latest/request-options.html
     *
     * @var array
     */
    protected $options = [
        'base_uri'        => null,
        'timeout'         => 3000,
        'connect_timeout' => 3000,
        'proxy'           => [],
    ];

    public $baseUri = [
        'base_uri' => 'https://oapi.dingtalk.com/',
        'v1' => 'https://api.dingtalk.com/',
    ];
    /**
     * @var bool
     */
    protected $autoTrimEndpointSlash = true;

    /**
     * Config constructor.
     *
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $this->options = array_merge($this->options, $options);
    }

    /**
     * @return string
     */
    public function getBaseUri($version = 'base_uri'): string
    {
        $this->options['base_uri'] = $this->baseUri[$version];
        return $this->baseUri[$version] ?? '';
    }

    /**
     * @param string $baseUri
     *
     * @return \EasyDingTalk\Kernel\Config
     */
    public function setBaseUri($baseUri): self
    {
        $this->options['base_uri'] = $baseUri;

        return $this;
    }

    /**
     * @return int
     */
    public function getTimeout(): int
    {
        return $this->options['timeout'] ?? 3000;
    }

    /**
     * @param int $timeout
     *
     * @return \EasyDingTalk\Kernel\Config
     */
    public function setTimeout($timeout): self
    {
        $this->options['timeout'] = $timeout;

        return $this;
    }

    /**
     * @return int
     */
    public function getConnectTimeout(): int
    {
        return $this->options['connect_timeout'] ?? 3000;
    }

    /**
     * @param int $connectTimeout
     *
     * @return \EasyDingTalk\Kernel\Config
     */
    public function setConnectTimeout($connectTimeout): self
    {
        $this->options['connect_timeout'] = $connectTimeout;

        return $this;
    }

    /**
     * @return array
     */
    public function getProxy(): array
    {
        return $this->options['proxy'] ?? [];
    }

    /**
     * @param array $proxy
     *
     * @return \EasyDingTalk\Kernel\Config
     */
    public function setProxy(array $proxy): self
    {
        $this->options['proxy'] = $proxy;

        return $this;
    }


    /**
     * @param string $key
     * @param mixed  $value
     *
     * @return $this
     */
    public function setOption($key, $value): self
    {
        $this->options[$key] = $value;

        return $this;
    }

    /**
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    public function getOption($key, $default = null)
    {
        return $this->options[$key] ?? $default;
    }

    /**
     * @param array $options
     *
     * @return $this
     */
    public function mergeOptions(array $options): self
    {
        $this->options = array_merge($this->options, $options);

        return $this;
    }

    /**
     * @param array $options
     *
     * @return $this
     */
    public function setOptions(array $options): self
    {
        $this->options = $options;

        return $this;
    }

    /**
     * @return array
     */
    public function getOptions($uriVersion): array
    {
        $this->options['base_uri'] = $this->baseUri[$uriVersion];
        return $this->options;
    }

    /**
     * @return bool
     */
    public function needAutoTrimEndpointSlash(): bool
    {
        return $this->autoTrimEndpointSlash;
    }

    /**
     * @return $this
     */
    public function disableAutoTrimEndpointSlash(): self
    {
        $this->autoTrimEndpointSlash = false;

        return $this;
    }
}
