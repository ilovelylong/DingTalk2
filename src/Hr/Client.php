<?php

/*
 * This file is part of the mingyoung/dingtalk.
 *
 * (c) 张铭阳 <mingyoungcheung@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyDingTalk\Hr;

use EasyDingTalk\Kernel\Client as BaseClient;

class Client extends BaseClient
{
    /**
     * 获取所以用户列表，递归方式调用
     * @param callable $callable
     * @param string $status_list
     * @param string|null $offset
     * @param string|null $size
     * @return mixed
     */
    protected function List($callback, int $offset = 0, int $size = 50, String $status_list = '2,3,5,-1')
    {
        if (method_exists($this, $callback))
            $result = call_user_func(array($this, $callback), $offset, $size, $status_list);
        else
            return [];
        if ((int)$result['result']['next_cursor'] != 0) {
            $next_result = $this->List($callback, $result['result']['next_cursor']);
            $result['result']['data_list'] = array_merge($result['result']['data_list'], $next_result['result']['data_list']);
        }
        return $result;
    }
    /**
     * 获取在职用户列表
     *
     * @param string $status_list
     * @param string|null $offset
     * @param string|null $size
     * @return mixed
     */
    public function getQueryonjob(int $offset = 0, int $size = 50, String $status_list = '2,3,5,-1',)
    {
        return $this->postJson('topapi/smartwork/hrm/employee/queryonjob', ['status_list' => $status_list, 'offset' => $offset, 'size' => $size]);
    }
    /**
     * 获取在职用户列表--所有
     *
     */
    public function getQueryonjobList()
    {
        return $this->List("getQueryonjob");
    }
    /**
     * 获取待入职员工列表
     *
     * @param string|null $offset
     * @param string|null $size
     * @return mixed
     */
    public function getQuerypreentry(int $offset = 0, int $size = 50)
    {
        return $this->postJson('topapi/smartwork/hrm/employee/querypreentry', ['offset' => $offset, 'size' => $size]);
    }

    /**
     * 获取待入职员工列表--所有
     *
     */
    public function getQuerypreentryList()
    {
        return $this->List("getQuerypreentry");
    }
    /**
     * 获取离职员工列表
     *
     * @param array $userid_list
 
     * @return mixed
     */
    public function getQuerydimission(int $offset = 0, int $size = 50)
    {
        return $this->postJson('topapi/smartwork/hrm/employee/querydimission', ['offset' => $offset, 'size' => $size]);
    }
    /**
     * 获取离职员工列表--所有
     *
     */
    public function getgetQuerydimissionList()
    {
        return $this->List("getQuerydimission");
    }
    /**
     * 获取员工离职信息
     *
     * @param array $userid_list
 
     * @return mixed
     */
    public function getListdimission(array $userid_list)
    {
        $userIds = implode(',', $userid_list);
        return $this->postJson('topapi/smartwork/hrm/employee/listdimission', ['userid_list' => $userIds]);
    }

}
