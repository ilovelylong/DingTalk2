<?php

/*
 * This file is part of the mingyoung/dingtalk.
 *
 * (c) 张铭阳 <mingyoungcheung@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace EasyDingTalk\Im;

use EasyDingTalk\Kernel\Client as BaseClient;
use function PHPUnit\Framework\isNull;

class Client extends BaseClient
    {
    public static $robotCode = "";
    public static $cardTemplateId = "";
    public static $imGroupId = [];
    public static $imUserId = [];
    public function sendCards($params, $id, $imGroupId = null, $imUserId = null, $robotCode = null, $cardTemplateId = null, $notification = "收到一条消息")
        {
        //构成openSpaceId 数组
        $arraySpaceId = array_map(fn($v) => "IM_ROBOT.{$v}", $imUserId ?? self::$imUserId);
        $arraySpaceId = array_merge($arraySpaceId, array_map(fn($v) => "IM_GROUP.{$v}", $imGroupId ?? self::$imGroupId));

        $data = [
            'cardTemplateId'          => $cardTemplateId ?? self::$cardTemplateId,
            'outTrackId'              => $id,//消息ID
            'openSpaceId'             => "dtv1.card//" . implode(";", $arraySpaceId),
            'cardData'                => ['cardParamMap' => $params],
            'imGroupOpenDeliverModel' => [
                'robotCode' => $robotCode ?? self::$robotCode,
                "spaceType" => "IM_GROUP"
            ],
            'imRobotOpenDeliverModel' => [
                'robotCode' => $robotCode ?? self::$robotCode,
                "spaceType" => "IM_ROBOT"
            ],
            'imRobotOpenSpaceModel'   => [
                "supportForward"  => "true",
                // "notification"    => $notification,
                "lastMessageI18n" => [
                    "ZH_CN" => $notification
                ],
            ],
            'imGroupOpenSpaceModel'   => [
                "supportForward"  => "false",
                // "notification"    => $notification,
                "lastMessageI18n" => [
                    "ZH_CN" => $notification
                ],
            ]
        ];

        $this->uriVersion = 'v1';
        return $this->postJson('/v1.0/card/instances/createAndDeliver', $data);
        }
    }
