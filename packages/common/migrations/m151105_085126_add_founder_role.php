<?php

use yii\db\Schema;
use yii\db\Migration;

class m151105_085126_add_founder_role extends Migration
{
    public function up()
    {
        $sql = <<<SQL
            INSERT INTO `auth_item` (`name`, `type`, `description`, `rule_name`, `data`, `created_at`, `updated_at`) VALUES ('founder', '1', '活动发起人', null, null, null, null);
            INSERT INTO `auth_item_child` (`parent`, `child`) VALUES ('founder', 'user');
            UPDATE `auth_item_child` SET `child`='founder' WHERE `parent`='pma' and`child`='user';
SQL;
        $this->execute($sql);

        return true;
    }

    public function down()
    {
        $sql = <<<SQL
            DELETE FROM auth_item_child where parent in ('founder');
            DELETE FROM auth_item where name in ('founder') and type = '1';
            UPDATE `auth_item_child` SET `child`='user' WHERE `parent`='pma' and`child`='founder';
SQL;
        $this->execute($sql);

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
