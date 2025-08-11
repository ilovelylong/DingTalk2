<?php

/*
 * This file is part of the mingyoung/dingtalk.
 *
 * (c) 张铭阳 <mingyoungcheung@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyDingTalk\WorkFlow;

use EasyDingTalk\Kernel\Client as BaseClient;

class Client extends BaseClient
    {
    /**
     * 发起审批实例
     *
     * @param array $params
     *
     * @return mixed
     */
    public $uriVersion = 'v1';
    public function create($params)
        {
        return $this->postJson('/v1.0/workflow/processInstances', $params);
        }

    public function selfcreate($params)
        {
        return $this->postJson('/v1.0/workflow/processCentres/instances', $params);
        }


    public function getProcessCode($name)
        {
        return $this->_GET('/v1.0/workflow/processCentres/schemaNames/processCodes', ['name' => $name]);
        }
    public function getSchemas($process_code)
        {
        return $this->_GET('/v1.0/workflow/forms/schemas/processCodes', ['processCode' => $process_code]);
        }
    /**
     * Summary of terminate
     * @param mixed $processInstanceId 实例ID
     * @param mixed $remark  撤销说明 
     * @param mixed $operatingUserId 操作人
     * @param mixed $isSystem   true：由系统直接终止，false：由指定的操作者终止（需要传发起人才能撤销）
     * @return array|object|string|\EasyDingTalk\Kernel\Support\Collection|\Psr\Http\Message\ResponseInterface
     * @author sunkaiyuan
     */
    public function terminate($processInstanceId, $remark = "系统", $operatingUserId = "", $isSystem = true)
        {
        return $this->postJson('/v1.0/workflow/processInstances/terminate', compact(
            'processInstanceId',
            'remark',
            'operatingUserId',
            'isSystem',
        ));
        }
    }
