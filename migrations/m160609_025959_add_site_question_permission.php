<?php

use yii\db\Schema;
use yii\db\Migration;

class m160609_025959_add_site_question_permission extends Migration
{
    public function up()
    {
       $this->update('auth_item_child',['parent' => 'founder'],'child = "/backend/site/index" ');
       $this->update('auth_item_child',['parent' => 'founder'],'child = "/backend/activity-tag/index" ');
       $this->update('auth_item_child',['parent' => 'founder'],'child = "/backend/question/view-by-activity-id" ');
       $this->update('auth_item_child',['parent' => 'founder'],'child = "/backend/question/update" ');
       $this->update('auth_item_child',['parent' => 'founder'],'child = "/backend/question/create" ');
       $this->update('auth_item_child',['parent' => 'founder'],'child = "/backend/activity-type/index " ');
       $this->update('auth_item_child',['parent' => 'founder'],'child = "/backend/qiniu/get-upload-token " ');
       $this->update('auth_item_child',['parent' => 'founder'],'child = "/backend/qiniu/create-completely-url  " ');
       
        return true;
    }

    public function down()
    {
        echo "m160609_025959_add_site_question_permission cannot be reverted.\n";

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
