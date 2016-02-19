<?php

use yii\db\Schema;
use yii\db\Migration;

class m160217_085358_add_share_table extends Migration
{
    public function up()
    {
        $sql = <<<SQL

CREATE TABLE share (
    `id` int(11) NOT NULL,
    `title` varchar(200) COMMENT '分享的标题',
    `desc` varchar(200) COMMENT '分享描述',
    `link` varchar(200) COMMENT '分享链接',
    `imgurl` varchar(200) COMMENT '分享图片链接',
    PRIMARY KEY (`id`)
) DEFAULT CHARACTER SET utf8mb4 COMMENT='';

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
