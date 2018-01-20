<?php
/**
 * 数据迁移接口
 * @author bean
 * @time 2018/1/5 0005 13:43
 */
namespace newx\data;

interface MigrationInterface
{
    /**
     * 迁移执行函数
     */
    public function go();
}