<?php

/*
 * This file is part of the EasyDingTalk/http.
 *
 * (c) EasyDingTalk <i@EasyDingTalk.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyDingTalk\Kernel\Responses;

use GuzzleHttp\Psr7\Response as GuzzleResponse;
use EasyDingTalk\Kernel\Support\Collection;
use EasyDingTalk\Kernel\Support\XML;
use Psr\Http\Message\ResponseInterface;

/**
 * Class Response.
 *
 * @author EasyDingTalk <i@EasyDingTalk.me>
 */
class Response extends GuzzleResponse
{
    /**
     * @return string
     */
    public function getBodyContents(): string
    {
        $this->getBody()->rewind();
        $contents = $this->getBody()->getContents();
        $this->getBody()->rewind();

        return $contents;
    }

    /**
     * @param \Psr\Http\Message\ResponseInterface $response
     *
     * @return \EasyDingTalk\Kernel\Responses\Response
     */
    public static function buildFromPsrResponse(ResponseInterface $response): self
    {
        return new static(
            $response->getStatusCode(),
            $response->getHeaders(),
            $response->getBody(),
            $response->getProtocolVersion(),
            $response->getReasonPhrase()
        );
    }

    /**
     * Build to json.
     *
     * @return string
     */
    public function toJson(): string
    {
        return json_encode($this->toArray());
    }

    /**
     * Build to array.
     *
     * @return array
     */
    public function toArray(): array
    {
        $content = $this->getBodyContents();

        if (false !== stripos($this->getHeaderLine('Content-Type'), 'xml') || 0 === stripos($content, '<xml')) {
            return XML::parse($content);
        }

        $array = json_decode($this->getBodyContents(), true);

        if (JSON_ERROR_NONE === json_last_error()) {
            return (array) $array;
        }

        return [];
    }

    /**
     * Get collection data.
     *
     * @return \EasyDingTalk\Kernel\Support\Collection
     */
    public function toCollection(): \EasyDingTalk\Kernel\Support\Collection
    {
        return new Collection($this->toArray());
    }

    /**
     * @return object
     */
    public function toObject(): object
    {
        return json_decode($this->getBodyContents());
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->getBodyContents();
    }
}
