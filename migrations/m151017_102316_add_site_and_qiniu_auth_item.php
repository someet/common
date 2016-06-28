<?php

use yii\db\Schema;

use yii\db\Migration;

class m151017_102316_add_site_and_qiniu_auth_item extends Migration
{
    public function up()
    {
        $items = [
            '/backend/site/index' => ['pma'],
            '/backend/qiniu/get-upload-token' => ['pma'],
            '/backend/qiniu/create-completely-url' => ['pma'],
        ];

        $authItemTemplate = <<<SQL
INSERT INTO `auth_item` (`name`, `type`, `description`, `rule_name`, `data`, `created_at`, `updated_at`) VALUES ('%s', '2', '', null, null, null, null);
SQL;
        $itemChildTemplate = <<<SQL
        INSERT INTO `auth_item_child` (`parent`, `child`) VALUES ('%s', '%s');
SQL;
        $sql = '';
        foreach ($items as $item => $roles) {
            $sql .= sprintf($authItemTemplate, $item);
            foreach ($roles as $role) {
                $sql .= sprintf($itemChildTemplate, $role, $item);
            }
        }
        $this->execute($sql);

        return true;
    }

    public function down()
    {
        echo "m151017_102316_add_site_and_qiniu_auth_item cannot be reverted.\n";

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
