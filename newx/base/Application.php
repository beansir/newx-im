<?php
/**
 * 应用主体
 * @author: bean
 * @version: 1.0
 */
namespace newx\base;

use Newx;
use newx\exception\AppException;
use newx\exception\BaseException;
use newx\helpers\ArrayHelper;
use newx\helpers\IniHelper;

class Application extends BaseObject
{
    /**
     * 配置信息
     * @var array
     */
    private $_config = [];

    /**
     * 应用名称
     * @var string
     */
    public $appName;

    /**
     * 执行控制器
     */
    public $controller;

    public $defaultController;

    /**
     * 执行函数
     */
    public $action;

    public $defaultAction;

    /**
     * 返回数据
     */
    public $return;

    /**
     * 组件
     * @var Component
     */
    public $component;

    /**
     * Application constructor.
     * @param array $config
     */
    public function __construct($config)
    {
        $this->_config = $config;
    }

    /**
     * 运行应用主体
     */
    public function run()
    {
        try {
            // 基础配置 解析路由 运行实例
            $this->configure()->analysisRouter()->instance();
        } catch (BaseException $e) {
            echo $e->throwOut();
            exit;
        }
    }

    /**
     * 解析请求
     * @throws AppException
     * @return $this
     */
    protected function analysisRouter()
    {
        $uris = explode('/', $_SERVER['REQUEST_URI']);
        unset($uris[0]);

        // 初始化
        $controller = '\\' . $this->appName . '\\controllers'; // 控制器
        $action = 'action' . ucfirst($this->defaultAction); // 行为函数

        // 空路由则获取默认控制器
        if (!ArrayHelper::value($uris, 1)) {
            $this->controller = $controller . '\\' . ucfirst($this->defaultController) . 'Controller';
            $this->action = $action;
            return $this;
        }

        // 检索路由控制器
        $controllerDir = Newx::getDir('module') . $controller; // 控制器文件首级路径
        foreach ($uris as $key => $uri) {
            if (empty($uri)) {
                continue;
            }
            $file = $controllerDir . '\\' . ucfirst($uri) . 'Controller.php';
            $file = str_replace('\\', '/', $file);
            if (file_exists($file)) { // 检索到控制器文件
                $controller .= '\\' . ucfirst($uri) . 'Controller';
                $action = ArrayHelper::value($uris, $key + 1, $this->defaultAction);
                $action = 'action' . ucfirst($action);
                $this->controller = $controller;
                $this->action = $action;
                return $this;
            } else { // 继续遍历文件夹检索控制器文件
                $controller .= '\\' . $uri;
                $controllerDir .= '\\' . $uri;
            }
        }

        // 未检索到路由控制器文件
        throw new AppException('controller not exists: ' . $controller);
    }

    /**
     * 运行实例
     */
    protected function instance()
    {
        // 创建实例
        $run = new $this->controller();
        if (!method_exists($run, $this->action)) {
            throw new AppException('action not exists: ' . $this->action);
        }
        $this->return = $run->{$this->action}();
    }

    /**
     * 基础配置
     * @throws AppException
     * @return $this
     */
    public function configure()
    {
        // 挂载应用配置
        Newx::setApp($this, $this->_config);

        $web = ArrayHelper::value($this->_config, 'web');
        if (empty($web)) {
            throw new AppException("web config not exists");
        }

        // 设置时区
        $timezone = ArrayHelper::value($web, 'timezone', 'Etc/GMT');
        IniHelper::setTimezone($timezone);

        // 应用名称
        $this->appName = ArrayHelper::value($web, 'name');
        if (empty($this->appName)) {
            throw new AppException("web config error: name");
        }

        // 默认控制器
        $this->defaultController = ArrayHelper::value($web, 'controller', 'home');

        // 默认方法
        $this->defaultAction = ArrayHelper::value($web, 'action', 'index');
        return $this;
    }

    /**
     * 析构函数
     */
    public function __destruct()
    {
        if (!empty($this->return)) {
            if (is_array($this->return)) {
                print_r($this->return);
            } else {
                echo $this->return;
            }
            exit();
        }
    }
}