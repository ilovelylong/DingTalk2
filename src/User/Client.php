<?php

/*
 * This file is part of the mingyoung/dingtalk.
 *
 * (c) 张铭阳 <mingyoungcheung@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyDingTalk\User;

use EasyDingTalk\Kernel\Client as BaseClient;

class Client extends BaseClient
    {
    /**
     * 获取用户详情
     *
     * @param string $userid
     * @param string|null $lang
     *
     * @return mixed
     */
    public function get($userid, $lang = null)
        {
        return $this->_GET('user/get', compact('userid', 'lang'));
        }

    /**
     * 获取部门用户 Userid 列表
     *
     * @param int $departmentId
     *
     * @return mixed
     */
    public function getUserIds($departmentId)
        {
        return $this->_GET('user/getDeptMember', ['deptId' => $departmentId]);
        }
    /**
     * 获取用户部门
     * @param string $userid
     * @return array|object|string|\EasyDingTalk\Kernel\Support\Collection|\Psr\Http\Message\ResponseInterface
     * @author sunkaiyuan
     */
    public function listparentbyuser($userid)
        {
        return $this->postJson('topapi/v2/department/listparentbyuser', compact("userid"));
        }
    /**
     * 获取部门用户
     *
     * @param int $departmentId
     * @param int $offset
     * @param int $size
     * @param string $order
     * @param string $lang
     *
     * @return mixed
     */
    public function getUsers($dept_id, $cursor, $size = 100, $order_field = 'entry_asc', $language = 'zh_CN')
        {
        return $this->_GET('topapi/user/listsimple', [
            'dept_id'     => $dept_id,
            'cursor'      => $cursor,
            'size'        => $size,
            'order_field' => $order_field,
            'language'    => $language,
        ]);
        }


    /**
     * 获取部门用户详情
     *
     * @param int $departmentId
     * @param int $offset
     * @param int $size
     * @param string $order
     * @param string $lang
     *
     * @return mixed
     */
    public function getDetailedUsers($departmentId, $offset, $size, $order = null, $lang = null)
        {
        return $this->_GET('user/listbypage', [
            'department_id' => $departmentId,
            'offset'        => $offset,
            'size'          => $size,
            'order'         => $order,
            'lang'          => $lang,
        ]);
        }

    /**
     * 获取管理员列表
     *
     * @return mixed
     */
    public function administrators()
        {
        return $this->_GET('user/get_admin');
        }

    /**
     * 获取管理员通讯录权限范围
     *
     * @param string $userid
     *
     * @return mixed
     */
    public function administratorScope($userid)
        {
        return $this->_GET('topapi/user/get_admin_scope', compact('userid'));
        }

    /**
     * 根据 Unionid 获取 Userid
     *
     * @param string $unionid
     *
     * @return mixed
     */
    public function getUseridByUnionid($unionid)
        {
        return $this->_GET('user/getUseridByUnionid', compact('unionid'));
        }

    /**
     * 创建用户
     *
     * @param array $params
     *
     * @return mixed
     */
    public function create(array $params)
        {
        return $this->postJson('user/create', $params);
        }

    /**
     * 更新用户
     *
     * @param string $userid
     * @param array $params
     *
     * @return mixed
     */
    public function update($userid, array $params)
        {
        return $this->postJson('user/update', compact('userid') + $params);
        }

    /**
     * 删除用户
     *
     * @param $userid
     *
     * @return mixed
     */
    public function delete($userid)
        {
        return $this->_GET('user/delete', compact('userid'));
        }

    /**
     * 企业内部应用免登获取用户 Userid
     *
     * @param string $code
     *
     * @return mixed
     */
    public function getUserByCode($code)
        {
        return $this->_GET('user/getuserinfo', compact('code'));
        }

    public $code;
    public function getUserByAuthCode($code)
        {
        $this->uriVersion                = 'v1';
        $this->app['access_token']       = new Client($this->app, false);
        $this->app['access_token']->code = $code;
        return $this->_GET('v1.0/contact/users/me');
        }
    public function getToken()
        {
        $this->uriVersion = 'v1';
        $code             = $this->code;
        $clientId         = $this->app['config']->get('app_key');
        $clientSecret     = $this->app['config']->get('app_secret');
        $grantType        = 'authorization_code';
        $accessToken      = $this->postJson('v1.0/oauth2/userAccessToken', compact('clientId', 'clientSecret', 'grantType', 'code'))['accessToken'];
        return $accessToken;
        }
    /**
     * 批量增加员工角色
     *
     * @param array|string $userIds
     * @param array|string $roleIds
     *
     * @return mixed
     */
    public function addRoles($userIds, $roleIds)
        {
        $userIds = is_array($userIds) ? implode(',', $userIds) : $userIds;
        $roleIds = is_array($roleIds) ? implode(',', $roleIds) : $roleIds;

        return $this->postJson('topapi/role/addrolesforemps', compact('userIds', 'roleIds'));
        }

    /**
     * 批量删除员工角色
     *
     * @param array|string $userIds
     * @param array|string $roleIds
     *
     * @return mixed
     */
    public function removeRoles($userIds, $roleIds)
        {
        $userIds = is_array($userIds) ? implode(',', $userIds) : $userIds;
        $roleIds = is_array($roleIds) ? implode(',', $roleIds) : $roleIds;

        return $this->postJson('topapi/role/removerolesforemps', compact('userIds', 'roleIds'));
        }

    /**
     * 获取企业员工人数
     *
     * @param int $onlyActive
     *
     * @return mixed
     */
    public function getCount($onlyActive = 0)
        {
        return $this->_GET('user/get_org_user_count', compact('onlyActive'));
        }

    /**
     * 获取企业已激活的员工人数
     *
     * @return mixed
     */
    public function getActivatedCount()
        {
        return $this->getCount(1);
        }

    /**
     * 根据员工手机号获取 Userid
     *
     * @param string $mobile
     *
     * @return mixed
     */
    public function getUserIdByPhone($mobile = '')
        {
        return $this->_GET('user/get_by_mobile', compact('mobile'));
        }

    /**
     * 未登录钉钉的员工列表
     *
     * @param string $query_date
     * @param int $offset
     * @param int $size
     *
     * @return mixed
     */
    public function getInactiveUsers($query_date, $offset, $size)
        {
        return $this->postJson('topapi/inactive/user/get', [
            'query_date' => $query_date,
            'offset'     => $offset,
            'size'       => $size
        ]);
        }
    }
