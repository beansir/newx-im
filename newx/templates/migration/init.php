<?php
namespace newx\templates\migrations;

use newx\data\Migration;

class Init extends Migration
{
    /**
     * 迁移执行函数
     */
    public function go()
    {
        $sql = <<<SQL
CREATE TABLE `migration` (
`id`  varchar(255) NOT NULL ,
`status` tinyint(1) NOT NULL DEFAULT 0 ,
`time`  datetime NULL ,
PRIMARY KEY (`id`)
)
SQL;
        $this->execute($sql);

        $sql = <<<SQL
INSERT INTO `migration` (`id`, `status`, `time`) VALUES ('Init', 1, date_format(now(),'%Y-%m-%d %H:%i:%s'));
SQL;
        $this->execute($sql);

        echo "application init success\n";
        exit;
    }
}