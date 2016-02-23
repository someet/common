<?php

use yii\db\Schema;
use yii\db\Migration;

class m151012_103629_update_special_table_add_is_top_field extends Migration
{
    public function up()
    {
        $sql = <<<SQL
ALTER TABLE `special`
DROP COLUMN `share_times`,
DROP COLUMN `display_order`,
ADD COLUMN `is_top` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否置顶 0 正常 1 置顶' AFTER `poster`,
ADD COLUMN `display_order` INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '显示排序' AFTER `is_top`,
ADD COLUMN `share_times` INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '分享次数' AFTER `display_order`;
SQL;
        $this->execute($sql);
        return true;

    }

    public function down()
    {
        echo "m151012_103629_update_special_table_add_is_top_field cannot be reverted.\n";

        return false;
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
