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

use EasyDingTalk\Kernel\Exceptions\InvalidArgumentException;
use EasyDingTalk\Kernel\Support\File;

/**
 * Class StreamResponse.
 *
 * @author EasyDingTalk <i@EasyDingTalk.me>
 */
class StreamResponse extends Response
{
    /**
     * @param string $directory
     * @param string $filename
     *
     * @throws \EasyDingTalk\Kernel\Exceptions\InvalidArgumentException
     *
     * @return string
     */
    public function save(string $directory, string $filename = ''): string
    {
        $this->getBody()->rewind();

        $directory = rtrim($directory, '/');

        if (!is_dir($directory)) {
            mkdir($directory, 0755, true); // @codeCoverageIgnore
        }

        if (!is_writable($directory)) {
            throw new InvalidArgumentException(sprintf("'%s' is not writable.", $directory));
        }

        $contents = $this->getBody()->getContents();

        if (empty($filename)) {
            if (preg_match('/filename="(?<filename>.*?)"/', $this->getHeaderLine('Content-Disposition'), $match)) {
                $filename = $match['filename'];
            } else {
                $filename = md5($contents);
            }
        }

        if (empty(pathinfo($filename, PATHINFO_EXTENSION))) {
            $filename .= File::getStreamExt($contents);
        }

        file_put_contents($directory.'/'.$filename, $contents);

        return $filename;
    }

    /**
     * @param string $directory
     * @param string $filename
     *
     * @throws \EasyDingTalk\Kernel\Exceptions\InvalidArgumentException
     *
     * @return string
     */
    public function saveAs(string $directory, string $filename): string
    {
        return $this->save($directory, $filename);
    }
}
