<?php
/**
 * Created by PhpStorm.
 * @author bean
 * @time 2018/1/5 0005 16:18
 */
namespace chat\controllers\api;

use chat\components\Cache;
use chat\components\Session;
use chat\models\UserModel;
use newx\base\Container;
use newx\base\Request;
use newx\data\Redis;
use newx\helpers\ArrayHelper;
use newx\helpers\TimeHelper;

class UserController extends Controller
{
    /**
     * 用户登录
     */
    public function actionLogin()
    {
        $data = [];
        try {
            $name = $this->getRequest()->post('name');
            if (!$name) {
                throw new \Exception('请输入用户名');
            }
            $password = $this->getRequest()->post('password');
            if (!$password) {
                throw new \Exception('请输入密码');
            }
            $user = UserModel::model()->where(['name' => $name])->one();
            if (!$user) {
                throw new \Exception('用户名或密码错误');
            }
            if ($user->password != $password) {
                throw new \Exception('用户名或密码错误');
            }
            if ($user->status == UserModel::STATUS_CLOSE) {
                throw new \Exception('该账号已被禁用');
            }

            // 登录者会话ID
            $session_id = Session::get();

            // 清空该用户上一次登录者缓存信息
            $old_session_id = $user->session_id;
            if ($session_id != $old_session_id) {
                Redis::create()->hDel(Cache::REDIS_KEY_USER_INFO, $old_session_id);
            }

            // 缓存该用户此次登录者的信息
            $cache_data = [
                'id' => $user->id,
                'name' => $user->name,
                'nickname' => $user->nickname
            ];
            Redis::create()->hSet(Cache::REDIS_KEY_USER_INFO, $session_id, json_encode($cache_data));

            // 登录信息更新入库
            $user->session_id = $session_id;
            $user->login_time = TimeHelper::format();
            if (!$user->save()) {
                throw new \Exception('登录失败');
            }

            $data['token'] = $user->session_id;
            $data['nickname'] = $user->nickname;

            $this->getResponse()->success('登录成功', $data);
        } catch (\Exception $e) {
            $this->getResponse()->error($e->getMessage());
        }
    }
}