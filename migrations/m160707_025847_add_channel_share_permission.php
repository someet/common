<?php

use yii\db\Migration;

class m160707_025847_add_channel_share_permission extends Migration
{
    public function up()
    {
       $sql = <<<SQL
ALTER TABLE activity_type 
ADD COLUMN share_title varchar(255) COMMENT '分享标题' AFTER status, ADD COLUMN share_desc varchar(255) COMMENT '分享描述' AFTER share_title, ADD COLUMN share_link varchar(255) COMMENT '分享链接' AFTER share_desc, ADD COLUMN share_img varchar(255) COMMENT '分享图片' AFTER share_link;
SQL;
        $this->execute($sql);
        return true;
    }

    public function down()
    {
         echo "m160707_025847_add_channel_share_permission cannot be reverted.\n";
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

