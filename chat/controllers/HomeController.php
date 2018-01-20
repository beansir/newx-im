<?php
/**
 * @author: bean
 * @version: 1.0
 */
namespace chat\controllers;

use chat\components\Cache;
use chat\components\Session;
use chat\models\UserModel;
use newx\base\BaseController;
use newx\data\Redis;
use newx\helpers\ArrayHelper;

class HomeController extends BaseController
{
    /**
     * 首页
     */
    public function actionIndex()
    {
        $data = [];

        // 获取用户信息
        $token = Session::get();
        $user = Redis::create()->hGet(Cache::REDIS_KEY_USER_INFO, $token);
        $user = json_decode($user, true);
        if (!$user) {
            $this->redirect('/user/login');
        }

        $data['token'] = $token;
        $data['nickname'] = ArrayHelper::value($user, 'nickname');

        $this->view('index', $data, false);
    }
}