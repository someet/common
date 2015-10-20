<?php

use yii\db\Schema;
use yii\db\Migration;

class m151020_071207_update_group_code_length_limit extends Migration
{
    public function up()
    {
        $sql = <<<SQL
ALTER TABLE `activity`
DROP COLUMN `group_code`,
ADD COLUMN `group_code` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL COMMENT '群二维码' AFTER `details`
SQL;
        $this->execute($sql);
        return true;

    }

    public function down()
    {
        echo "m151020_071207_update_group_code_length_limit cannot be reverted.\n";

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
