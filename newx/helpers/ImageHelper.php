<?php
/**
 * 图像帮助类
 * @author bean
 * @version 1.0
 */
namespace newx\helpers;

class ImageHelper
{
    private $_save_image;
    private $_save_resource;
    private $_save_width;
    private $_save_height;
    private $_save_quality;

    private $_origin_image;
    private $_origin_resource;
    private $_origin_type;
    private $_origin_width;
    private $_origin_height;

    private $_allow_type = ['jpg', 'jpeg', 'png'];
    private $_error;

    /**
     * ImageHelper constructor.
     * @param string $origin_image 源图像文件
     * @param string $save_image 新图像文件
     */
    public function __construct($origin_image, $save_image = null)
    {
        $this->_origin_image = $origin_image;
        $this->_save_image = $save_image;
    }

    /**
     * 图像配置
     * @param string $origin_image 源图像文件（绝对路径），例如：/data/www/origin.jpg
     * @param string $save_image 新图像文件（绝对路径），不传默认覆盖源图像
     * @return ImageHelper
     */
    public static function get($origin_image, $save_image = null)
    {
        return new self($origin_image, $save_image);
    }

    /**
     * 初始化图像
     * @throws \Exception
     */
    private function _init()
    {
        // 获取图像信息
        $data = getimagesize($this->_origin_image);
        if (!$data) {
            throw new \Exception('获取图像信息失败');
        }
        $this->_origin_width = $data[0];
        $this->_origin_height = $data[1];

        // 检查图像类型
        $type = image_type_to_extension($data[2], false);
        if (!in_array($type, $this->_allow_type)) {
            throw new \Exception('图像类型错误');
        }
        $this->_origin_type = $type;

        if ($this->_save_image) { // 指定新图像文件
            $type = strrchr($this->_save_image, '.');
            if (!$type) {
                // 未指定新图像类型（后缀名），默认使用源图像类型
                $this->_save_image = $this->_save_image . '.' . $this->_origin_type;
            }
        } else { // 未指定新图像文件，默认覆盖源图像
            $this->_save_image = $this->_origin_image;
        }

        // 创建图像资源
        $resource = ('imagecreatefrom' . $this->_origin_type)($this->_origin_image);
        if (!$resource) {
            throw new \Exception('创建图像资源失败');
        }
        $this->_origin_resource = $resource;
    }

    /**
     * 等比例压缩图像
     * @param int $percent 0.1 ~ 1
     * @param int $quality 0 ~ 100
     * @return bool
     */
    public function percentCompress($percent = 1, $quality = 75)
    {
        try {
            $this->_init();
            $this->_save_width = $this->_origin_width * $percent;
            $this->_save_height = $this->_origin_height * $percent;
            $this->_save_quality = $quality;
            return $this->compress();
        } catch (\Exception $e) {
            $this->_error = $e->getMessage() . "in {$e->getFile()} : {$e->getLine()}";
            return false;
        }

    }

    /**
     * 自定义缩放图像
     * @param int $width
     * @param int $height
     * @param int $quality 0 ~ 100
     * @return bool
     */
    public function customizeCompress($width = 0, $height = 0, $quality = 75)
    {
        try {
            $this->_init();
            $this->_save_width = $width;
            $this->_save_height = $height;
            $this->_save_quality = $quality;
            return $this->compress();
        } catch (\Exception $e) {
            $this->_error = $e->getMessage() . "in {$e->getFile()} : {$e->getLine()}";
            return false;
        }
    }

    /**
     * 压缩处理
     * @return bool
     * @throws \Exception
     */
    private function compress()
    {
        // 新建真彩色图像
        $this->_save_resource = imagecreatetruecolor($this->_save_width, $this->_save_height);

        // 重采样拷贝部分图像并调整大小
        imagecopyresampled(
            $this->_save_resource,
            $this->_origin_resource,
            0,
            0,
            0,
            0,
            $this->_save_width,
            $this->_save_height,
            $this->_origin_width,
            $this->_origin_height
        );

        // 输出图象到文件
        $res = ('image' . $this->_origin_type)($this->_save_resource, $this->_save_image, $this->_save_quality);
        if (!$res) {
            throw new \Exception('图像压缩失败');
        }
        return true;
    }

    /**
     * 获取错误信息
     * @return string
     */
    public function getError()
    {
        return $this->_error;
    }

    /**
     * ImageHelper destructor.
     */
    public function __destruct()
    {
        // 销毁图像资源
        if ($this->_origin_resource) {
            imagedestroy($this->_origin_resource);
        }
        if ($this->_save_resource) {
            imagedestroy($this->_save_resource);
        }
    }

    /**
     * 图像转base64编码
     * @param string $image 图像文件地址
     * @return string|null
     */
    public static function base64($image)
    {
        $image_info = getimagesize($image);
        if (!$image_info) {
            return null;
        }

        // 方式一 file_get_contents
        // 单纯转换数据，该方式比fread的性能好很多
        $data = file_get_contents($image);

        // 方式二 fread
        //$fp = fopen($image,'rb', 0);
        //$data = fread($fp, filesize($image));
        //fclose($fp);

        return 'data:' . $image_info['mime'] . ';base64,' . chunk_split(base64_encode($data));
    }
}