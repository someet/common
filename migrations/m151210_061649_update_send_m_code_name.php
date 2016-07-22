<?php

use yii\db\Schema;
use yii\db\Migration;

class m151210_061649_update_send_m_code_name extends Migration
{
    public function up()
    {
        $sql = <<<SQL
UPDATE `auth_item` SET `name`='/mobile/member/send-m-code' WHERE `name`='/mobile/sms/send-m-code';
SQL;
        $this->execute($sql);
        return true;
    }

    public function down()
    {
        echo "m151210_061649_update_send_m_code_name cannot be reverted.\n";

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
