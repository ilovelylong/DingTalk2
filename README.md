DingTalk2
===============

原项目地址：https://github.com/mingyoung/dingtalk。
由于原项目停止维护了，自用打包一份
## 当前食用方式

composer require ilovelylong/dingtalk2

## 环境要求
-   PHP 7.1+
-   Composer
## 文档
 https://www.kancloud.cn/chinakaiyuan/dingtalk-sdk/

## 实例化

:::warning
文档中提及到的 `$app` 表示的是如下实例化后的 `EasyDingTalk\Application` 实例，就不在每个模块中描述了
:::

```php {4-23}
use EasyDingTalk\Application;

$config = [
    //**配置文件分钉钉配置项，和程序配置项目
    /*
    |-----------------------------------------------------------
    | 【必填】企业 corpId  钉钉配置项目
    |-----------------------------------------------------------
    */
    'corp_id' => 'XXXXXX',
    /*
    |-----------------------------------------------------------
    | 【选填】后台免登配置信息 钉钉配置项目
    |-----------------------------------------------------------
    | 如果你用到应用管理后台免登功能，需要配置该项
    */
    'sso_secret' => 'XXXXXX',
    
    //以下三个是 H5,小程序，机器人默认公有项，如果app 不设置或者设置了没用使用，默认调用此配置，
    "agentid" => "XXXXXX",
    "app_key" => "XXXXXX",
    "app_secret" => "XXXXXX",
    //小程序配置项目
    "miniappid"=>"xxxxxx",
    //H5,小程序 订阅配置项目
    "aes_key" => "XXXXXX",
    "token" => "XXXXXX",

    'app'=>[
        'robot01'=>[
              //以下三个是 H5,小程序，机器人公有项
                'kind'=>'robot',//必填
                "agentid" => "XXXXXX",
                "app_key" => "XXXXXX",
                "app_secret" => "XXXXXX", 
                //小程序配置项目
                "miniappid"=>"xxxxxx",
                //H5,小程序 订阅配置项目
                "aes_key" => "XXXXXX",
                "token" => "XXXXXX",
        ],
        'MFC'=>[
               'kind' => 'app', //必填
                // or 'app_id'
                'client_id' => 'XXXXX',
                // or 'app_secret' 
                'client_secret' => 'XXXXXXXXXXXXXXXXXXXXXX',
                // or 'redirect_url'
                'redirect' => 'https://www.XXX.com/',
                'scope' => 'snsapi_login',
        ]
    ]
];

$app = new Application($config);
$app_robot=$app->robot01; //返回robot01 配置的机器人
、、、或者
$app_robot=Application::robot01($config);//返回robot01 配置的机器人
、、、或者
$app = new Application($config);
$app_robot=$app->robot;//返回默认配置的机器人
都是返回一个机器人实例
```
 传递H5,小程序 配置后
 使用
 $app->MFC  
 返回对应的实例,用此实例可以调用对应的接口。具体使用demo =》https://gitee.com/chinakaiyuan/laravelgrid
## 应用免登录 说明
```php
        ......
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        //判断是不是需要钉钉浏览器打开
        if (strpos($user_agent, "DingTalk") !== false) {
        //没有登录的，就跳转登录页面
            if (!ISLOGIN){
                 $redirecturl = '登录成功后的跳转页面';
                    $HTML = <<<SCRIPT
                    <script src='https://g.alicdn.com/dingding/dingtalk-jsapi/2.10.3/dingtalk.open.js'></script>
                        <script>
                        dd.ready(function() {
                            dd.runtime.permission.requestAuthCode({
                                corpId: "{$app->config['corp_id']}",
                                onSuccess : function(res) {
                                 
                                    var url='你的授权页面地址?rl={$redirecturl}&code='+res.code // 通过该免登授权码可以获取用户身份
                                     parent.location.href=url;
                                },
                                onFail : function(err) {
                                    // 调用失败时回调
                                    alert('dd error: ' + JSON.stringify(err));
                                }
                            });
                        });
                    </script>
                    SCRIPT;
                    echo $HTML;
                    exit(0);
            }
                
         }  
       
       
        ......

        ......
            //根据上面 的前端地址获得code 
            $UserDataUser = $app->user->getUserByCode($_GET['code']);

            //登录逻辑代码
   ....
            header('Location:' . $_GET['rl']);
    
            
```
        
## 扫码登录 说明
跳转前端，我这样写
```html
        
        <span id="login_container"></span>
        <script src="https://g.alicdn.com/dingding/dinglogin/0.0.5/ddLogin.js"></script>
<script>
            var url = encodeURIComponent("你的登录授权地址");
        url = "https://oapi.dingtalk.com/connect/oauth2/sns_authorize?                                    appid=dingoawhqfo729flbetouo&response_type=code&scope=snsapi_login&state=STATE&redirect_uri=" + url;
        $("#DDscan").click(function() {

            var obj = DDLogin({
                id: "login_container", //这里需要你在自己的页面定义一个HTML标签并设置id，例如<div id="login_container"></div>或<span id="login_container"></span>
                goto: encodeURIComponent(url), //请参考注释里的方式
                style: "border:none;background-color:#FFFFFF;",
                width: "365",
                height: "400"
            });

        })
</script>
```
 
```php
....
$userCodeData = $app->app->stateless()->user();
```
当然你也可以这样写
```php
// 扫码登录第三方网站
$response = $app->app->withQrConnect()->redirect();

// 钉钉内免登第三方网站、密码登录第三方网站均同样使用如下方法跳转：
$response = $app->app->redirect();
// 回调页面统一使用如下方法来获取用户信息：
$user = $app->app->user();
```

免登录，和扫码登录用原文档代码，是相当简便的。但是我没试。
## 订阅消息 
按照原文档操作就没毛病了,加密解密，接口验证，配置文件写好，都自动的了。
日志在这里就调用了3次
EasyDingTalk\Kernel\Server 内，
1、获取的加密订阅数据
2、解密的加密数据
3、发送给钉钉的数据
订阅消息我大概这样处理
```php
        $server = $app->app;
        $server->push(DingTalkHandler::class);
        $server->serve()->send(); // ThinkPHP 等框架使用
......

class DingTalkHandler{

     public function __invoke($payload)
    {
        // 在此处处理你的业务逻辑
        $type = $payload['EventType'];
        switch ($type) {
            case 'bpms_task_change': //审批任务开始、结束、转交。
                $this->msgdata($payload, '审批任务事件');
                break;
            case 'bpms_instance_change': //审批实例开始、结束。
                $this->msgdata($payload, '审批实例事件');
                break;
            case 'attendance_check_record': //审批实例开始、结束。
                $this->msgdata($payload, '员工打卡事件');
                break;
            case 'attendance_schedule_change': //审批实例开始、结束。
                $this->msgdata($payload, '员工排班变更事件');
                break;
            case 'attendance_overtime_duration': //审批实例开始、结束。
                $this->msgdata($payload, '员工加班事件');
                break;
            case 'hrm_user_record_change': //审批实例开始、结束。
                $this->msgdata($payload, '员工信息变动');
                break;

            ......
            default:
                $this->msgdata($payload, '其他事件');
                break;
        }
    }
    public function msgdata($payload, $type){
        
    }
}
```

 

## 机器人回调消息

::: tip
SDK 目前提供三种方法以方便你监听钉钉机器人的@事件推送
当然你可以多次调用 `push` 方法，实现多个消息处理器
使用方式参考 上一章 [服务器事件](服务器事件.md)
唯一要注意的是，每一个处理器 都可以返回一个消息，当然也可以不返回消息。也就是说一次@机器人，可以自动回复多条消息。
:::
#### 获取 robot实例，返回多条消息
```php
//用robot01 配置文件 初始化机器人
$robot= $app->robot;
//每一个处理器 都可以返回一个消息
$robot->push(function () {
            $text = new EasyDingTalk\\Messages\\Text("123");
            return$text->toJson();
        });

$robot->push(DingTalkHandler::class);

$robot->push(function () {
            $text = new EasyDingTalk\\Messages\\Text("123456789");
            return$text->toJson();
        });
$robot->serve()->send(); // ThinkPHP 等框架使用
```
 #### 获取 robot实例，发送消息
::: tip
$this->robot 会自动在配置中找第一个kind 是robot的配置。 
$app->robot01 会找robot01 配置，根据kind 初始化一个机器人
:::
```php
//用robot01 配置文件 初始化机器人
$robot= $this->robot;
$text = new EasyDingTalk\\Messages\\Text("123456789");
$robot->send(json_encode($text));
```
```php
//用robot01 配置文件 批量发送单人消息
  $app->robot01->batchMsg(
            ["msgKey" => 'sampleMarkdown', "msgParam" => "{
            'title': '触发一个通知',
            'text': '### 触发一个通知\n**小标题**\n\n- 列表\nn- 列表\nn- 列表\nn'
       }"],
            ["userid", 'userid']
        );
```