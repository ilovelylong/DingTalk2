<?php

/*
 * This file is part of the mingyoung/dingtalk.
 *
 * (c) 张铭阳 <mingyoungcheung@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyDingTalk\Todo;

use EasyDingTalk\Kernel\Client as BaseClient;

class Client extends BaseClient
{
    protected $uriVersion = 'v1';
    /**
     * 发起待办
     * https://developers.dingtalk.com/document/app/add-dingtalk-to-do-task 参数说明
     * @param string $owner_unionId 所有者 必填
     * @param string $subject   //标题 必填
     * @param string $creatorUnionId //创建人 必填
     *  @param string $params
     * @return mixed
     */
    public function add($owner_unionId, $subject, $creatorUnionId, array $params)
    {
        //creatorId 必填
        $params['subject'] = $subject;
        $params['creatorId'] = $creatorUnionId;
        return $this->postJson("/v1.0/todo/users/{$owner_unionId}/tasks?operatorId={$creatorUnionId}", $params);
    }


    /**
     * 待办详情
     * https://developers.dingtalk.com/document/app/add-dingtalk-to-do-task 参数说明
     * @param string $unionId 所有者 必填
     * @param string $taskId   //待办ID。  必填
     * @return mixed
     */
    public function detailTaskId($unionId, $taskId)
    {


        return $this->_GET("/v1.0/todo/users/{$unionId}/tasks/{$taskId}");
    }
    /**
     * 待办详情
     * https://developers.dingtalk.com/document/app/add-dingtalk-to-do-task 参数说明
     * @param string $unionId 所有者 必填
     * @param string $sourceId   //待办业务来源sourceId。  必填
     * @return mixed
     */
    public function detailSourceId($unionId, $sourceId)
    {


        return $this->_GET("/v1.0/todo/users/{$unionId}/tasks/{$sourceId}");
    }
    /**
     * 删除待办
     * https://developers.dingtalk.com/document/app/add-dingtalk-to-do-task 参数说明
     * @param string $unionId 所有者 必填
     * @param string $taskId   //待办ID。  必填
     * @return mixed
     */
    public function delete($unionId, $taskId, $operatorId)
    {
        return $this->_DELETE("/v1.0/todo/users/{$unionId}/tasks/{$taskId}?operatorId={$operatorId}");
    }

    /**
     * 更新待办
     * https://developers.dingtalk.com/document/app/add-dingtalk-to-do-task 参数说明
     * @param string $unionId 所有者 必填
     * @param string $taskId   //待办ID。  必填
     * @return mixed
     */
    public function update($unionId, $taskId, $operatorId, array $params)
    {
        return $this->_PUT("/v1.0/todo/users/{$unionId}/tasks/{$taskId}?operatorId={$operatorId}", $params);
    }

    /**
     * 更新待办执行者状态
     * https://developers.dingtalk.com/document/app/add-dingtalk-to-do-task 参数说明
     * @param string $unionId 所有者 必填
     * @param string $taskId   //待办ID。  必填
     * @return mixed
     */
    public function updateExecutorStatus($unionId, $taskId, $operatorId, array $params)
    {
        return $this->_PUT("/v1.0/todo/users/{$unionId}/tasks/{$taskId}/executorStatus?operatorId={$operatorId}", $params);
    }


    /**
     *  待办任务列表
     * https://developers.dingtalk.com/document/app/add-dingtalk-to-do-task 参数说明
     * @param string $unionId 所有者 必填
     * @param string $taskId   //待办ID。  必填
     * @return mixed
     */
    public function list($unionId, array $params = [])
    {
        // $params['isDone']='';
        // $params['nextToken']='';
        return $this->postJson("/v1.0/todo/users/{$unionId}/org/tasks/query", $params);
    }
    /**
     *  待办任务列表all
     * https://developers.dingtalk.com/document/app/add-dingtalk-to-do-task 参数说明
     * @param string $unionId 所有者 必填
     * @param string $taskId   //待办ID。  必填
     * @return mixed
     */
    public function gettodolist($unionId, array $params)
    {

        $result = $this->list($unionId, $params);
        if (isset($result['result']['nextToken']) && $result['result']['nextToken'] != null) {
            $params['nextToken'] = $result['result']['nextToken'];
            $resultBuf = $this->gettodolist($unionId,  $params);
            $result['todoCards'] = array_merge($result['todoCards'], $resultBuf['result']['todoCards']);
        }
        return $result;
    }
}
