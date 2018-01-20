<?php
/**
 * Created by PhpStorm.
 * @author bean
 * @time 2018/1/4 0004 11:58
 */
namespace chat\controllers;

class UserController extends Controller
{
    /**
     * 用户登录
     */
    public function actionLogin()
    {
        $this->view('login', [], false);
    }
}