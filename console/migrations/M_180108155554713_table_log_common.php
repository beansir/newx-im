<?php
namespace console\migrations;

use newx\data\Migration;

class M_180108155554713_table_log_common extends Migration
{
    /**
     * 迁移执行函数
     */
    public function go()
    {
        $sql = <<<SQL
CREATE TABLE `log_common` (
`id`  int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID' ,
`user_id`  int(11) NULL COMMENT '用户ID' ,
`ip`  varchar(50) NULL COMMENT 'IP地址' ,
`content`  text NULL COMMENT '内容' ,
`create_time`  datetime NULL COMMENT '创建时间' ,
PRIMARY KEY (`id`)
)
ENGINE=MyISAM
COMMENT='通用日志表'
CHECKSUM=0
DELAY_KEY_WRITE=0;
SQL;
        $this->execute($sql);
    }
}