<?php
/**
 * 数据迁移
 * @author bean
 * @time 2018/1/4 0004 14:36
 */
namespace newx\data;

use newx\base\Container;
use newx\exception\AppException;
use Newx;

class Migration implements MigrationInterface
{
    /**
     * 默认数据库
     * @var string
     */
    public $database = 'default';

    /**
     * 迁移文件名前缀
     * @var string
     */
    public $filePrefix = 'M';

    /**
     * 应用初始化
     */
    public function init()
    {
        try {
            // 应用目录
            $appPath = Newx::getDir('app');

            // 创建迁移文件夹
            if (!file_exists($appPath . 'migrations')) {
                mkdir($appPath . 'migrations');
            }

            // 创建初始化文件
            $initFile = $appPath . 'migrations/Init.php';
            $initFileResource = fopen($initFile, 'w');
            $content = file_get_contents(NEWX_PATH . '/templates/migration/init.php');
            $content = str_replace('newx\templates', 'console', $content);
            fwrite($initFileResource, $content);
            fclose($initFileResource);

            // 开始初始化
            (new \console\migrations\Init())->go();
        } catch (AppException $e) {
            echo "{$e->getMessage()} in {$e->getFile()} : {$e->getLine()}\n";
        }
        exit;
    }

    /**
     * 创建数据迁移文件
     * @param $fileName
     */
    public function create($fileName)
    {
        try {
            // 验证规则
            $this->validate();

            // 应用目录
            $appPath = Newx::getDir('app');

            // 生成唯一文件名
            $fileName = $this->filePrefix . '_' . date('ymdHis') . rand(100, 999) . '_' . $fileName;

            // 创建文件
            $file = $appPath . 'migrations/' . $fileName . '.php';
            $fileResource = fopen($file, 'w');
            $content = file_get_contents(NEWX_PATH . '/templates/migration/create.php');

            // 转换命名空间
            $content = str_replace('newx\templates', 'console', $content);

            // 转换类名
            $content = str_replace('Create', $fileName, $content);

            // 写入文件
            fwrite($fileResource, $content);
            fclose($fileResource);

            // 文件记录入库
            $sql = <<<SQL
INSERT INTO `migration` (`id`, `time`) VALUES ('{$fileName}', date_format(now(), '%Y-%m-%d %H:%i:%s'));
SQL;
            $this->execute($sql);

            echo "migration file create success: $fileName\n";
        } catch (AppException $e) {
            echo "{$e->getMessage()} in {$e->getFile()} : {$e->getLine()}\n";
        }
        exit;
    }

    /**
     * 执行数据迁移
     * @param int $param 参数，正数=迁移个数，负数=迁移第N个，空=迁移所有
     */
    public function run($param = null)
    {
        try {
            // 验证规则
            $this->validate();

            // 获取迁移文件
            $files = $this->getFile();

            // 参数指定的数值都是从最新文件开始计算（倒序）
            krsort($files);

            // 处理参数重组迁移文件
            if ($param) {
                $files = $this->handleParam($files, $param);
            }

            if ($files) {
                // 打印迁移文件
                foreach ($files as $file) {
                    echo "  $file\n";
                }

                // 迁移确认
                if ($this->scanf('run these the migrations?')) {
                    foreach ($files as $file) {
                        $this->handleFile($file);
                    }
                }
            } else {
                echo "no conditional migration\n";
            }
        } catch (AppException $e) {
            echo "{$e->getMessage()} in {$e->getFile()} : {$e->getLine()}\n";
        }
        exit;
    }

    /**
     * I/O 迁移确认
     * @param $message
     * @return bool
     */
    private function scanf($message)
    {
        // 输出流 php://stdout
        fwrite(\STDOUT, $message . ' [ yes or no ]:');

        // 输入流 php://stdin
        $input = trim(rtrim(fgets(\STDIN), PHP_EOL));

        if (!strcasecmp($input, 'y') || !strcasecmp($input, 'yes')) {
            return true;
        }
        if (!strcasecmp($input, 'n') || !strcasecmp($input, 'no')) {
            return false;
        }

        return $this->scanf($message);
    }

    /**
     * 验证规则
     * @throws AppException
     */
    private function validate()
    {
        // 检查应用是否已初始化
        $initFile = Newx::getDir('app') . 'migrations/Init.php';
        if (!file_exists($initFile)) {
            throw new AppException('application uninitialized');
        }

        $sql = <<<SQL
SELECT * FROM `migration`;
SQL;
        $data = $this->query($sql);
        if (!$data) {
            throw new AppException('application uninitialized');
        }
    }

    /**
     * 获取所有符合条件的迁移文件
     * @return array
     */
    private function getFile()
    {
        // 应用目录
        $appPath = Newx::getDir('app');

        // 获取迁移文件
        $data = [];
        $files = scandir($appPath . 'migrations');
        foreach ($files as $file) {
            if (stristr($file, '.php')) {
                $fileName = str_replace('.php', '', $file);
                $sql = <<<SQL
SELECT * FROM `migration` WHERE id='{$fileName}';
SQL;
                $res = $this->query($sql);
                if (!$res || $res[0]['status'] == 0) {
                    $data[] = $fileName;
                }
            }
        }
        return $data;
    }

    /**
     * 重组迁移文件
     * @param array $files
     * @param int $param
     * @return array
     */
    private function handleParam($files, $param)
    {
        if (!$files || !$param) {
            return $files;
        }
        if ($param > 0) { // 指定N个文件
            $count = (int)$param;
            $index = '';
        } else if ($param < 0) { // 指定第N个文件
            $count = count($files);
            $index = abs($param);
        } else {
            return $files;
        }

        $data = [];
        $flag = 1;
        foreach ($files as $file) {
            if ($flag > $count) {
                continue;
            }
            if ($index) {
                if ($index == $flag) {
                    $flag = $count;
                    $data[] = $file;
                }
            } else {
                $data[] = $file;
            }
            $flag++;
        }
        return $data;
    }

    /**
     * 执行迁移文件
     * @param string $file 迁移文件名
     */
    private function handleFile($file)
    {
        // 迁移类全名
        $className = '\\console\\migrations\\' . $file;

        // 开始迁移
        /**
         * @var MigrationInterface $migration
         */
        $migration = new $className();
        $migration->go();

        // 迁移记录更新
        $status = 'error';
        $sql = <<<SQL
SELECT * FROM `migration` WHERE id='{$file}';
SQL;
        if ($this->query($sql)) {
            $sql = <<<SQL
UPDATE `migration` SET status=1 WHERE id='{$file}';
SQL;
            if ($this->execute($sql)) {
                $status = 'success';
            }
        } else {
            $sql = <<<SQL
INSERT INTO `migration` (`id`, `status`, `time`) VALUES ('{$file}', 1, date_format(now(),'%Y-%m-%d %H:%i:%s'));
SQL;
            if ($this->execute($sql)) {
                $status = 'success';
            }
        }
        echo "{$file}: {$status}\n";
    }

    /**
     * 执行SQL
     * @param string $sql
     * @return int|string|mixed
     */
    protected function execute($sql)
    {
        return Container::db($this->database)->createCommand($sql)->execute();
    }

    /**
     * 查询SQL
     * @param $sql
     * @return array
     */
    protected function query($sql)
    {
        return Container::db($this->database)->createCommand($sql)->query();
    }

    /**
     * 迁移执行函数
     */
    public function go(){}
}