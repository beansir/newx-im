<?php
namespace console\migrations;

use newx\data\Migration;

class M_180105142224562_table_user extends Migration
{
    /**
     * 迁移执行函数
     */
    public function go()
    {
        $sql = <<<SQL
CREATE TABLE `user` (
`id`  int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID' ,
`name`  varchar(50) NULL COMMENT '账号' ,
`nickname`  varchar(50) NULL COMMENT '昵称' ,
`password`  varchar(50) NULL COMMENT '密码' ,
`status`  tinyint NULL DEFAULT 1 COMMENT '状态 0.禁用 1.正常' ,
`inline`  tinyint NULL DEFAULT 0 COMMENT '是否在线 0.否 1.是' ,
`session_id`  varchar(50) COMMENT 'SESSION ID' ,
`login_time`  datetime NULL COMMENT '登录时间' ,
`create_time`  datetime NULL COMMENT '注册时间' ,
PRIMARY KEY (`id`)
)
ENGINE=InnoDB
COMMENT='用户表';
SQL;
        $this->execute($sql);
    }
}