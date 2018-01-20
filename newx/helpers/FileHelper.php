<?php
/**
 * 文件帮助类
 * @author bean
 * @version 1.0
 */
namespace newx\helpers;

class FileHelper
{
    const TYPE_COMMON = 'common'; // 普通类型 $_FILES

    const TYPE_BASE64 = 'base64'; // base64编码

    /**
     * 文件数据 $_FILES | base64
     * @var array
     */
    private $_file;

    /**
     * 主目录
     * @var string
     */
    protected $rootName; // 主目录

    /**
     * 二级目录
     * @var string
     */
    protected $secondName; // 二级目录

    /**
     * 允许的文件类型
     * @var array
     */
    protected $allowType;

    /**
     * 允许的文件大小
     * @var int
     */
    protected $allowSize;

    /**
     * 结果集
     * @var array
     */
    protected $response;

    /**
     * FileHelper constructor.
     */
    public function __construct()
    {
        $this->response['file'] = [];
        $this->response['error'] = [];
    }

    /**
     * 上传入口函数
     * @param array $file 支持多文件上传
     * @param string $type 文件编码类型
     * @return $this
     */
    public function upload($file, $type = null)
    {
        try {
            // 创建主目录
            if (!self::checkDir($this->rootName . $this->secondName)) {
                throw new \Exception('文件目录创建失败');
            }

            switch ($type) {
                case self::TYPE_COMMON:
                    $this->_file = self::dealFiles($file);
                    $this->common();
                    break;
                case self::TYPE_BASE64:
                    $this->_file = is_array($file) ? $file : [$file];
                    $this->base64();
                    break;
                default:
                    break;
            }
        } catch (\Exception $e) {
            $this->response['error'][] = $e->getMessage();
        }
        return $this;
    }

    /**
     * 普通类型文件上传
     * @throws \Exception
     */
    protected function common()
    {
        if (!$this->_file) {
            throw new \Exception('文件不存在');
        }
        foreach ($this->_file as $file) {
            $name = ArrayHelper::value($file, 'name'); // 临时文件名
            $tmpName = ArrayHelper::value($file, 'tmp_name'); // 临时文件地址
            $fileSize = ArrayHelper::value($file, 'size', 0); // 文件大小
            $fileType = self::getType($name); // 文件类型

            if (empty($tmpName)) {
                $this->response['error'][] = $name . ': 文件不存在';
                continue;
            }

            // 检查文件类型
            if (!in_array($fileType, $this->allowType)) {
                $this->response['error'][] = $name . ': 文件格式错误';
                continue;
            }

            // 检查文件大小
            if ($this->allowSize) {
                if ($fileSize > $this->allowSize) {
                    $this->response['error'][] = $name . ': 文件大小超过上限';
                    continue;
                }
            }
            $saveName = $this->getSaveName(); // 保存的文件名
            $fileName = $this->secondName . $saveName . '.' . $fileType; // 部分上传地址
            $newFileName = $this->rootName .$fileName; // 完整上传地址
            //echo $tmpName . '-----' . $newFileName;exit;
            if (!move_uploaded_file($tmpName, $newFileName)) {
                $this->response['error'][] = $name . ': 文件上传失败';
                continue;
            }
            $this->response['file'][] = $fileName;
        }
        return true;
    }

    /**
     * base64编码类型文件上传
     */
    protected function base64()
    {
        if (!$this->_file) {
            throw new \Exception('图片文件不存在');
        }
        foreach ($this->_file as $file) {
            if(preg_match('/^(data:\s*image\/(\w+);base64,)/', $file, $result)){
                $this->response['error'][] = '图片编码类型错误';
            }

            // 检查文件类型
            $type = $result[2];
            if(!in_array($type, $this->allowType)){
                $this->response['error'][] = '图片格式错误';
            }

            $saveName = $this->getSaveName(); // 保存的文件名
            $fileName = $this->secondName . $saveName . '.' . $type; // 部分上传地址
            $newFileName = $this->rootName .$fileName; // 完整上传地址

            if(!file_put_contents($newFileName, base64_decode(str_replace($result[1], '', $file)))){
                $this->response['error'][] = '图片上传失败';
            }
            $this->response['file'][] = $fileName;
        }
    }

    /**
     * 设置二级目录 默认空
     * @param null $name
     * @return $this
     */
    public function setSecondName($name = null)
    {
        $this->secondName = $name;
        return $this;
    }

    /**
     * 设置主目录 必须项
     * @param null $name
     * @return $this
     */
    public function setRootName($name = null)
    {
        $this->rootName = $name;
        return $this;
    }

    /**
     * 设置允许的文件类型 默认空
     * @param array $type
     * @return $this
     */
    public function setAllowType($type = [])
    {
        $this->allowType = $type;
        return $this;
    }

    /**
     * 设置允许的文件大小 默认空
     * @param int $size
     * @return $this
     */
    public function setAllowSize($size = 0)
    {
        $this->allowSize = $size;
        return $this;
    }

    /**
     * 生成保存的文件名
     * @return string
     */
    protected function getSaveName()
    {
        return md5(date('YmdHis') . mt_rand(100, 999));
    }

    /**
     * 获取结果集
     * @return array
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * 获取错误信息
     * @return array
     */
    public function getErrors()
    {
        return $this->response['error'];
    }

    /**
     * 获取第一个错误信息
     * @return string
     */
    public function getFirstError()
    {
        return $this->response['error'][0];
    }

    /**
     * 获取成功上传的文件
     * @return array
     */
    public function getFile()
    {
        return $this->response['file'];
    }

    // static
    // -----------------------------------------------------------------------------------------------------------------
    /**
     * 获取文件类型
     * @param string $fileName 文件完整名称（带后缀格式）
     * @return bool|string
     */
    public static function getType($fileName)
    {
        return substr($fileName, strrpos($fileName, '.') + 1, strlen($fileName));
    }

    /**
     * 重新封装$_FILES
     * @param array $file $_FILES的一维数组 例如：$_FILES[0]
     * @return array
     */
    public static function dealFiles($file)
    {
        $data = [];
        if (is_array($file['name'])) {
            foreach ($file['name'] as $key => $name) {
                $data[] = [
                    'name' => $name,
                    'type' => $file['type'][$key],
                    'tmp_name' => $file['tmp_name'][$key],
                    'error' => $file['error'][$key],
                    'size' => $file['size'][$key]
                ];
            }
        } else {
            $data[] = $file;
        }
        return $data;
    }

    /**
     * 检查并创建目录
     * @param $dir
     * @return bool
     */
    public static function checkDir($dir)
    {
        if (!file_exists($dir) && !mkdir($dir, 0777, true)) {
            return false;
        }
        return true;
    }

    /**
     * 图片格式化
     * @param $source_path
     * @param $target_width
     * @param $target_height
     * @return bool
     */
    public static function format($source_path, $target_width, $target_height)
    {
        $source_info = getimagesize($source_path);
        $source_width = $source_info[0];
        $source_height = $source_info[1];
        $source_mime = $source_info['mime'];
        $source_ratio = $source_height / $source_width;
        $target_ratio = $target_height / $target_width;

        if ($source_ratio > $target_ratio) { // 源图过高
            $cropped_width = $source_width;
            $cropped_height = $source_width * $target_ratio;
            $source_x = 0;
            $source_y = ($source_height - $cropped_height) / 2;
        } else if ($source_ratio < $target_ratio) { // 源图过宽
            $cropped_width = $source_height / $target_ratio;
            $cropped_height = $source_height;
            $source_x = ($source_width - $cropped_width) / 2;
            $source_y = 0;
        } else { // 源图适中
            $cropped_width = $source_width;
            $cropped_height = $source_height;
            $source_x = 0;
            $source_y = 0;
        }

        switch ($source_mime) {
            case 'image/gif':
                $source_image = imagecreatefromgif($source_path);
                break;
            case 'image/jpeg':
                $source_image = imagecreatefromjpeg($source_path);
                break;
            case 'image/png':
                $source_image = imagecreatefrompng($source_path);
                break;
            default:
                return false;
                break;
        }

        $target_image = imagecreatetruecolor($target_width, $target_height);
        $cropped_image = imagecreatetruecolor($cropped_width, $cropped_height);

        // 裁剪
        imagecopy($cropped_image, $source_image, 0, 0, $source_x, $source_y, $cropped_width, $cropped_height);
        // 缩放
        imagecopyresampled($target_image, $cropped_image, 0, 0, 0, 0, $target_width, $target_height, $cropped_width, $cropped_height);

        // 保存图片
        imagepng($target_image, $source_path);
        imagedestroy($target_image);

        return true;
    }

    /**
     * 获取文件名称
     * @param string $url 文件URL地址
     * @param boolean $has_suffix 是否带后缀格式
     * @return null|string
     */
    public static function getName($url = null, $has_suffix = true)
    {
        $name = '';
        if ($url) {
            $name = substr($url, strripos($url, '/') + 1);
            if (!$has_suffix) {
                $name = str_replace(substr($name, strripos($name, '.')), '', $name);
            }
        }
        return $name;
    }
}