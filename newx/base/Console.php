<?php
/**
 * @author: bean
 * @version: 1.0
 */
namespace newx\base;

use Newx;
use newx\data\Migration;
use newx\exception\AppException;
use newx\exception\BaseException;
use newx\helpers\ArrayHelper;
use newx\helpers\IniHelper;
use newx\helpers\StringHelper;
use newx\library\swoole\SwooleInterface;

class Console extends BaseObject
{
    /**
     * 组件
     * @var Component
     */
    public $component;

    /**
     * 配置信息
     * @var array
     */
    private $_config = [];

    /**
     * 参数信息
     * @var array
     */
    private $_argv = [];

    /**
     * 应用名称
     * @var string
     */
    protected $appName;

    /**
     * 当前命令
     * @var string
     */
    protected $option;

    /**
     * 当前参数
     * @var array
     */
    protected $params = [];

    /**
     * Application constructor.
     * @param array $config
     * @param array $argv
     */
    public function __construct($config, $argv)
    {
        $this->_config = $config;
        $this->_argv = $argv;
    }

    /**
     * 运行应用主体
     */
    public function run()
    {
        try {
            // 基础配置
            $this->configure();

            if (method_exists($this, $this->option)) {
                $this->{$this->option}();
            } else {
                $this->common();
            }
        } catch (BaseException $e) {
            echo $e->getMessage() . "\n";
            exit;
        }
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

        // 获取命令选项
        $this->option = ArrayHelper::value($this->_argv, 1);
        if (!$this->option) {
            throw new AppException('console option not exists');
        }

        // 获取参数
        $argvCount = count($this->_argv);
        if ($argvCount > 2) {
            for ($i = 2; $i < $argvCount; $i++) {
                $this->params[] = $this->_argv[$i];
            }
        }

        // 获取配置
        $config = ArrayHelper::value($this->_config, 'app');
        if (empty($config)) {
            throw new AppException("app config not exists");
        }

        // 应用名称
        $appName = ArrayHelper::value($config, 'name');
        if (empty($appName)) {
            throw new AppException("app config error: name not exists");
        }
        $this->appName = $appName;

        // 设置时区
        $timezone = ArrayHelper::value($config, 'timezone', 'Etc/GMT');
        IniHelper::setTimezone($timezone);

        return $this;
    }

    /**
     * 普通控制台命令
     */
    protected function common()
    {
        $options = explode('/', $this->option);
        if (count($options) == 1) {
            throw new AppException('console action not exists');
        }

        // 获取执行函数
        $action = $options[count($options) - 1];
        $action = 'action' . ucfirst($action);
        unset($options[count($options) - 1]);

        // 获取控制器
        $controller = $options[count($options) - 1];
        $controller = StringHelper::lower2upper($controller, '-') . 'Controller';
        unset($options[count($options) - 1]);
        if ($options) {
            $controller = implode('\\', $options) . '\\' . $controller;
        }
        $controller = '\\' . $this->appName . '\\controllers\\' . $controller;

        // 创建实例
        $app = new $controller();
        if (!method_exists($app, $action)) {
            throw new AppException('console action not exists: ' . $action);
        }
        $app->{$action}(); // TODO 参数传递暂未实现
    }

    /**
     * 数据迁移
     */
    protected function migrate()
    {
        $migration = new Migration();
        $operate = ArrayHelper::value($this->_argv, 2);
        switch ($operate) {
            case 'init':
                $migration->init();
                break;
            case 'create':
                $fileName = ArrayHelper::value($this->_argv, 3);
                if (!$fileName) {
                    throw new AppException('file name is not specified');
                }
                $migration->create($fileName);
                break;
            default:
                $param = ArrayHelper::value($this->_argv, 2);
                $migration->run($param);
                break;
        }
    }

    /**
     * 异步通信服务
     */
    protected function server()
    {
        // 获取服务
        $server = ArrayHelper::value($this->_argv, 2);
        if (!$server) {
            throw new AppException('server not exists');
        }

        // 服务配置
        $config = ArrayHelper::value($this->_config, ['server', $server]);
        if (!$config) {
            throw new AppException("server config error: $server not exists");
        }

        // 服务类
        $server = '\\' . $this->appName . '\\server\\' . StringHelper::lower2upper($server, '-');

        /**
         * 启动服务
         * @var SwooleInterface $app
         */
        $app = new $server($config);
        $app->start();
    }

}