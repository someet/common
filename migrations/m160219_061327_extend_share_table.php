<?php

use yii\db\Schema;
use yii\db\Migration;

class m160219_061327_extend_share_table extends Migration
{
    public function up()
    {
        $sql = <<<SQL

DROP TABLE IF EXISTS `share`;
CREATE TABLE `share` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `page_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '页面id',
  `user_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '用户id',
  `title` varchar(200) DEFAULT NULL COMMENT '标题',
  `desc` varchar(200) DEFAULT NULL COMMENT '描述',
  `link` varchar(200) DEFAULT NULL COMMENT '链接',
  `imgurl` varchar(200) DEFAULT NULL COMMENT '图片链接',
  `created_at` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '0未启用 1启用',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COMMENT='分享内容表';

SQL;
        $this->execute($sql);
        return true;

    }

    public function down()
    {
        $this->dropTable('share');
        return true;
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
