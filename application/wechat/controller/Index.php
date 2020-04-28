<?php
namespace app\wechat\controller;
use think\Controller;
use EasyWeChat\Factory;
class Index extends Controller
{



        public function server()
        {
//            $jssdkObj = new Jssdk('wx4f5820641c06df69', 'f059c89a24367cfacb5e41b62cad734c');
            $config = [
                'app_id' => 'wx4f5820641c06df69',
                'secret' => 'f059c89a24367cfacb5e41b62cad734c',
                'token' => 'hxfkd',
                'response_type' => 'array',
            ];
            $app = Factory::officialAccount($config);

            $response = $app->server->serve();
                // 将响应输出
            $response->send();exit;

        }

        public function auth()
        {
            $config = [
                'app_id' => 'wx4f5820641c06df69',
                'secret' => 'f059c89a24367cfacb5e41b62cad734c',
                'token' => 'hxfkd',
                'response_type' => 'array',
                'oauth' => [
                    'scopes'   => ['snsapi_userinfo'],
                    'callback' => '/wechat/index/oauth_callback',
                ],
            ];
            $app = Factory::officialAccount($config);
            $oauth = $app->oauth;
            // 未登录
            if (empty($_SESSION['wechat_user'])) {
                $_SESSION['target_url'] = '/wechat/index/oauth_callback';
                return $oauth->redirect();
            }
            // 已经登录过
            $user = $_SESSION['wechat_user'];

        }

        public function oauth_callback()
        {
            $config = [
                'app_id' => 'wx4f5820641c06df69',
                'secret' => 'f059c89a24367cfacb5e41b62cad734c',
                'token' => 'hxfkd',
                'response_type' => 'array',
                'oauth' => [
                    'scopes'   => ['snsapi_userinfo'],
                    'callback' => '/wechat/index/oauth_callback',
                ],
            ];
            $app = Factory::officialAccount($config);
            $user = $app->oauth->user();
            dump($user);
            $_SESSION['wechat_user'] = $user->toArray();

//            $targetUrl = empty($_SESSION['target_url']) ? '/' : $_SESSION['target_url'];
//
//            header('location:'. $targetUrl);

        }


}
