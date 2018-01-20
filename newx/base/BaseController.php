<?php
/**
 * 控制器基类
 * @author: bean
 * @version: 1.0
 */

namespace newx\base;

use Newx;
use newx\exception\ViewException;

class BaseController extends BaseObject
{
    /**
     * 布局文件
     * @var string
     */
    public $layout = 'main';

    /**
     * 布局文件目录
     * @var string
     */
    public $layoutPath = 'layouts';

    /**
     * 视图文件名
     * @var string
     */
    protected $view;

    /**
     * 视图文件目录
     * @var string
     */
    public $viewPath = 'views';

    /**
     * 渲染的文件
     * @var string
     */
    protected $renderFile;

    /**
     * 渲染的数据
     * @var array
     */
    protected $renderData;

    /**
     * 请求体
     * @var Request
     */
    protected $request;

    /**
     * 渲染视图
     * @param string $view 视图
     * @param array $data 数据
     * @param bool $isLayout 是否渲染布局
     */
    public function view($view = null, $data = [], $isLayout = true)
    {
        $this->view = $view; // 配置视图
        $this->renderData = $data; // 配置渲染数据

        // 渲染验证
        $this->validate();

        // 渲染视图
        $html = $this->getViewFile()->render($data);

        if ($isLayout) {
            // 渲染布局
            $data['content'] = $html;
            $html = $this->getLayoutFile()->render($data);
        }

        echo $html;
        exit;
    }

    /**
     * 跳转URL TODO 简单实现，有待完善
     * @param string $url
     */
    public function redirect($url)
    {
        header("Location: {$url}");
        exit;
    }

    /**
     * 获取视图目录
     * @return string
     */
    private function getView()
    {
        return Newx::getDir('app') . $this->viewPath;
    }

    /**
     * 获得布局目录
     * @return string
     */
    private function getLayout()
    {
        return $this->getView() . '/' . $this->layoutPath . '/' . $this->layout;
    }

    /**
     * 获取视图文件
     * @throws ViewException
     * @return $this
     */
    private function getViewFile()
    {
        if (stristr($this->view, '/')) {
            $view = $this->view;
        } else {
            $class = basename(str_replace('\\', '/', self::className()));
            if (stristr($class, 'Controller')) {
                $class = lcfirst(str_replace('Controller', '', $class));
            } else {
                $class = lcfirst($class);
            }
            $view = $class . '/' . $this->view;
        }

        $viewFile = $this->getView() . "/" . $view . ".php";
        $viewFile = str_replace('\\', '/', $viewFile);

        if (!file_exists($viewFile)) {
            throw new ViewException('view not exists: ' . $viewFile);
        }

        $this->renderFile = $viewFile;

        return $this;
    }

    /**
     * 获取布局文件
     * @throws ViewException
     * @return $this
     */
    private function getLayoutFile()
    {
        $layoutFile = $this->getLayout() . '.php';

        if (!file_exists($layoutFile)) {
            throw new ViewException('layouts not exists: ' . $layoutFile);
        }

        $this->renderFile = $layoutFile;

        return $this;
    }

    /**
     * 渲染数据
     */
    private function render($data)
    {
        ob_start();
        ob_implicit_flush(false);
        extract($data, EXTR_OVERWRITE);
        require($this->renderFile);

        return ob_get_clean();
    }

    /**
     * 渲染验证
     */
    private function validate()
    {
        // 是否指定视图
        if (empty($this->view)) {
            throw new ViewException('view must be not null');
        }

        // 渲染数据是否为数组格式
        if (!is_array($this->renderData)) {
            throw new ViewException('render data must be array');
        }
    }

    /**
     * 获取相应体
     * @param string $type
     * @return Response
     */
    protected function getResponse($type = null)
    {
        return Response::create($type);
    }

    /**
     * 获取请求体
     * @return Request
     */
    protected function getRequest()
    {
        if (!$this->request) {
            $this->request = Request::create();
        }
        return $this->request;
    }
}