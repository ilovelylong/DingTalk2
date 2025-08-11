<?php

/*
 * This file is part of the mingyoung/dingtalk.
 *
 * (c) 张铭阳 <mingyoungcheung@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyDingTalk\Attendance;

use EasyDingTalk\Kernel\Client as BaseClient;

class Client extends BaseClient
{
    /**
     * 企业考勤排班详情
     *
     * @param string   $date
     * @param int|null $offset
     * @param int|null $size
     *
     * @return mixed
     */
    public function schedules($date, $offset = null, $size = null)
    {
        return $this->postJson('topapi/attendance/listschedule', [
            'workDate' => $date, 'offset' => $offset, 'size' => $size,
        ]);
    }

    /**
     * 企业考勤组详情
     *
     * @param int|null $offset
     * @param int|null $size
     *
     * @return mixed
     */
    public function groups($offset = null, $size = null)
    {
        return $this->postJson('topapi/attendance/getsimplegroups', compact('offset', 'size'));
    }

    /**
     * 获取用户考勤组
     *
     * @param string $userId
     *
     * @return mixed
     */
    public function userGroup($userId)
    {
        return $this->postJson('topapi/attendance/getusergroup', ['userid' => $userId]);
    }

    /**
     * 获取打卡详情
     *
     * @param array $params
     *
     * @return mixed
     */
    public function records($params)
    {
        return $this->postJson('attendance/listRecord', $params);
    }

    /**
     * 获取打卡结果
     *
     * @param array $params
     *
     * @return mixed
     */
    public function results($params)
    {
        return $this->postJson('attendance/list', $params);
    }

    /**
     * 获取请假时长
     *
     * @param string $userId
     * @param string $from
     * @param string $to
     *
     * @return mixed
     */
    public function duration($userId, $from, $to)
    {
        return $this->postJson('topapi/attendance/getleaveapproveduration', [
            'userid' => $userId, 'from_date' => $from, 'to_date' => $to,
        ]);
    }

    /**
     * 查询请假状态
     *
     * @param array $params
     *
     * @return mixed
     */
    public function status($params)
    {
        return $this->postJson('topapi/attendance/getleavestatus', $params);
    }
    /**
     * 获取考勤报表列
     * https://developers.dingtalk.com/document/app/obtains-the-column-values-of-the-smart-attendance-report
     */
    public function getattcolumns($params)
    {
        return $this->postJson('topapi/attendance/getattcolumns', $params);
    }

    /**
     * 获取对应列值
     * https://developers.dingtalk.com/document/app/obtains-the-column-values-of-the-smart-attendance-report
     */
    public function getcolumnval($userid, array $column_id_list, $from_date, $to_date)
    {
        $params['userid'] = $userid;
        $params['column_id_list'] = $column_id_list;
        $params['from_date'] = $from_date;
        $params['to_date'] = $to_date;
        return $this->postJson('topapi/attendance/getcolumnval', $params);
    }
}
