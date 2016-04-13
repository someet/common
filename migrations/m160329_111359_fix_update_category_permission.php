<?php

use yii\db\Schema;
use yii\db\Migration;

class m160329_111359_fix_update_category_permission extends Migration
{
    public function up()
    {
        $sql = <<<SQL
ALTER TABLE `yellow_card` CHANGE COLUMN `card_category` `card_category` tinyint(3) DEFAULT 0 COMMENT '选择类别（黄牌原因理由）1 迟到 2请假1  3请假2 4爽约 5带人 6骚扰';
SQL;
        $this->execute($sql);
        return true;
    }

    public function down()
    {
        echo "m160329_111359_fix_update_category_permission cannot be reverted.\n";

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

