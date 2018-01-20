<?php
/**
 * 分页
 * @author bean
 * @version 1.0
 */
namespace newx\views;

class Page
{
    public $url; // 请求地址
    public $numPageCount = 5; // 数字页显示的数量
    private $pageTotal; // 总页数
    private $nowPage; // 当前页
    private $firstHtml; // 首页
    private $lastHtml; // 上一页
    private $numHtml; // 数字页
    private $nextHtml; // 下一页
    private $endHtml; // 末页
    private $param; // 请求参数

    public function __construct($count, $pageSize, $nowPage)
    {
        $this->nowPage = $nowPage;
        $this->pageTotal = (int)ceil($count / $pageSize); // 总页数
    }

    public static function configure($count, $pageSize, $nowPage)
    {
        $count = $count > 0 ? $count : 1;
        $pageSize = $pageSize > 0 ? $pageSize : 1;
        $nowPage = $nowPage > 0 ? $nowPage : 1;
        return new self($count, $pageSize, $nowPage);
    }

    public function render()
    {
        try {
            // 配置请求地址
            if (!isset($this->url)) {
                $this->setUrl();
            }
            $this->setParam(); // 请求参数
            $this->setFirstHtml(); // 首页
            $this->setLastHtml(); // 上一页
            $this->setNumHtml(); // 数字页
            $this->setNextHtml(); // 下一页
            $this->setEndHtml(); // 末页
            return $this->getHtml();
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    private function setUrl()
    {
        // 默认当前路由地址
        $this->url = str_replace('.html', '', U(ACTION_NAME));
        return $this;
    }

    private function setParam()
    {
        $params = $_GET ? $_GET : $_POST;
        if ($params) {
            $param = '?';
            foreach ($params as $key => $value) {
                if ($key != 'p') {
                    $param .= "$key=$value&";
                }
            }
            $this->param = $param;
        }
    }

    private function setFirstHtml()
    {
        if ($this->nowPage > 1) {
            $html = '<li>';
            $html .= '<a href="javascript:;" onclick="getPage(\'' . $this->url . '/p/1.html' . $this->param . '\')">首页</a>';
            $html .= '</li>';
        } else {
            $html = '<li class="disabled">';
            $html .= '<a href="javascript:;">首页</a>';
            $html .= '</li>';
        }
        $this->firstHtml = $html;
    }

    private function setLastHtml()
    {
        if ($this->nowPage > 1) {
            $html = '<li>';
            $html .= '<a href="javascript:;" onclick="getPage(\'' . $this->url . '/p/' . ($this->nowPage - 1) . '.html' . $this->param . '\')">&laquo;</a>';
            $html .= '</li>';
        } else {
            $html = '<li class="disabled">';
            $html .= '<a href="javascript:;">&laquo;</a>';
            $html .= '</li>';
        }
        $this->lastHtml = $html;
    }

    private function setNumHtml()
    {
        $html = '';
        $avg = floor($this->numPageCount / 2);
        $start = $this->nowPage - $avg;
        $end = $this->nowPage + $avg;
        if ($start <= 0) {
            $start = 1;
        }
        $frontDiff = $this->nowPage - ($avg + 1);
        if ($frontDiff < 0) {
            $end += abs($frontDiff);
        }
        if (($end + 1 - $start) < $this->numPageCount) {
            $end += $this->numPageCount - ($end + 1 - $start);
        }
        if (($end + 1 - $start) > $this->numPageCount) {
            $end -= ($end + 1 - $start) - $this->numPageCount;
        }
        if ($end > $this->pageTotal) {
            $end = $this->pageTotal;
        }
        $backDiff = ($end - $this->nowPage) - $avg;
        if ($backDiff < 0) {
            $start -= abs($backDiff);
        }
        if ($start <= 0) {
            $start = 1;
        }
        for ($i = $start; $i <= $end; $i++) {
            if ($i == $this->nowPage) {
                $html .= '<li class="active">';
                $html .= '<a href="javascript:;">' . $i . '</a>';
                $html .= '</li>';
            } else {
                $html .= '<li>';
                $html .= '<a href="javascript:;" onclick="getPage(\'' . $this->url . '/p/' . $i . '.html' . $this->param . '\')">' . $i . '</a>';
                $html .= '</li>';
            }
        }
        $this->numHtml = $html;
    }

    private function setNextHtml()
    {
        if ($this->nowPage < $this->pageTotal) {
            $html = '<li>';
            $html .= '<a href="javascript:;" onclick="getPage(\'' . $this->url . '/p/' . ($this->nowPage + 1) . '.html' . $this->param . '\')">&raquo;</a>';
            $html .= '</li>';
        } else {
            $html = '<li class="disabled">';
            $html .= '<a href="javascript:;">&raquo;</a>';
            $html .= '</li>';
        }
        $this->nextHtml = $html;
    }

    private function setEndHtml()
    {
        if ($this->nowPage < $this->pageTotal) {
            $html = '<li>';
            $html .= '<a href="javascript:;" onclick="getPage(\'' . $this->url . '/p/' . $this->pageTotal . '.html' . $this->param . '\')">末页</a>';
            $html .= '</li>';
        } else {
            $html = '<li class="disabled">';
            $html .= '<a href="javascript:;">末页</a>';
            $html .= '</li>';
        }
        $this->endHtml = $html;
    }

    private function getHtml()
    {
        $html = '<ul>';
        $html .= $this->firstHtml . $this->lastHtml . $this->numHtml . $this->nextHtml . $this->endHtml;
        $html .= '</ul>';
        return $html;
    }

}