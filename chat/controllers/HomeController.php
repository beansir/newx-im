<?php
/**
 * @author: bean
 * @version: 1.0
 */
namespace chat\controllers;

use newx\base\BaseController;

class HomeController extends BaseController
{
    /**
     * 首页
     */
    public function actionIndex()
    {
        $data = [];
        $this->view('index', $data, false);
    }
}