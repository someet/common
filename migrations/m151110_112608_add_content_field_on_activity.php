<?php

use yii\db\Schema;
use yii\db\Migration;

class m151110_112608_add_content_field_on_activity extends Migration
{
    public function up()
    {
        $sql = <<<SQL
ALTER TABLE `activity`
ADD COLUMN `content` TEXT NULL DEFAULT NULL COMMENT '文案' AFTER `edit_status`;
SQL;
        $this->execute($sql);
        return true;

    }

    public function down()
    {
        $this->dropColumn('activity', 'content');

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
