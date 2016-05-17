<?php

use yii\db\Schema;
use yii\db\Migration;

class m151017_095712_auth_init_roles extends Migration
{
    public function up()
    {
        $sql = <<<SQL
INSERT INTO `auth_item` (`name`, `type`, `description`, `rule_name`, `data`, `created_at`, `updated_at`) VALUES ('guest', '1', 'Guest', null, null, null, null);
INSERT INTO `auth_item` (`name`, `type`, `description`, `rule_name`, `data`, `created_at`, `updated_at`) VALUES ('user', '1', '会员', null, null, null, null);
INSERT INTO `auth_item` (`name`, `type`, `description`, `rule_name`, `data`, `created_at`, `updated_at`) VALUES ('pma', '1', 'Pma', null, null, null, null);
INSERT INTO `auth_item` (`name`, `type`, `description`, `rule_name`, `data`, `created_at`, `updated_at`) VALUES ('admin', '1', '管理员', null, null, null, null);

INSERT INTO `auth_item_child` (`parent`, `child`) VALUES ('user', 'guest');
INSERT INTO `auth_item_child` (`parent`, `child`) VALUES ('pma', 'user');
INSERT INTO `auth_item_child` (`parent`, `child`) VALUES ('admin', 'pma');
SQL;
        $this->execute($sql);

        return true;
    }

    public function down()
    {
        $sql = <<<SQL
DELETE FROM auth_item_child where parent in ('user', 'pma', 'admin');
DELETE FROM auth_item where name in ('user', 'pma', 'admin', 'guest') and type = '1';
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
