<?php

/*
 * This file is part of the mingyoung/dingtalk.
 *
 * (c) 张铭阳 <mingyoungcheung@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyDingTalk\Role;

use EasyDingTalk\Kernel\Client as BaseClient;

class Client extends BaseClient
{
    /**
     * 获取角色列表
     *
     * @param int $offset
     * @param int $size
     *
     * @return mixed
     */
    public function list($offset = null, $size = null)
    {
        return $this->postJson('topapi/role/list', compact('offset', 'size'));
    }

    /**
     *  待办任务列表all
     * https://developers.dingtalk.com/document/app/add-dingtalk-to-do-task 参数说明
     * @param string $unionId 所有者 必填
     * @param string $taskId   //待办ID。  必填
     * @return mixed
     */
    public function getListAll($offset = 0,   $size = 200)
    {

        $result = $this->list($offset, 200);
        if ($result['result']['hasMore'] == true) {
            $offset++;
            $resultBuf = $this->getListAll($offset,  $size);
            $result['result']['list'] = array_merge($result['result']['list'], $resultBuf['result']['list']);
        }
        return $result['list'];
    }

    /**
     * 获取角色下的员工列表
     *
     * @param int $roleId
     * @param int $offset
     * @param int $size
     *
     * @return mixed
     */
    public function getUsers($roleId, $offset = null, $size = null)
    {
        return $this->postJson('topapi/role/simplelist', compact('offset', 'size') + ['role_id' => $roleId]);
    }

    /**
     * 获取角色组
     *
     * @param int $groupId
     *
     * @return mixed
     */
    public function getGroups($groupId)
    {
        return $this->postJson('topapi/role/getrolegroup', ['group_id' => $groupId]);
    }

    /**
     * 获取角色详情
     *
     * @param int $roleId
     *
     * @return mixed
     */
    public function getRole($roleId)
    {
        return $this->postJson('topapi/role/getrole', compact('roleId'));
    }

    /**
     * 创建角色
     *
     * @param int    $groupId
     * @param string $roleName
     *
     * @return mixed
     */
    public function create($groupId, $roleName)
    {
        return $this->postJson('role/add_role', compact('groupId', 'roleName'));
    }

    /**
     * 更新角色
     *
     * @param int    $roleId
     * @param string $roleName
     *
     * @return mixed
     */
    public function update($roleId, $roleName)
    {
        return $this->postJson('role/update_role', compact('roleId', 'roleName'));
    }

    /**
     * 删除角色
     *
     * @param int $roleId
     *
     * @return mixed
     */
    public function delete($roleId)
    {
        return $this->postJson('topapi/role/deleterole', ['role_id' => $roleId]);
    }

    /**
     * 创建角色组
     *
     * @param string $name
     *
     * @return mixed
     */
    public function createGroup($name)
    {
        return $this->postJson('role/add_role_group', compact('name'));
    }
}
