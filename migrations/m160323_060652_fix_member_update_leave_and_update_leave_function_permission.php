<?php

use yii\db\Schema;
use yii\db\Migration;

class m160323_060652_fix_member_update_leave_and_update_leave_function_permission extends Migration
{
    public function up()
    {
         $sql = <<<SQL
    UPDATE `auth_item_child` SET `parent` = 'user' WHERE `child` = '/mobile/member/cancel-apply' or `child`='/mobile/member/update-leave';
SQL;
         $this->execute($sql);
                return true;    
    }

    public function down()
    {
        $this->dropColumn('uga_question', 'anwers_num');

        return true;
    }

}
