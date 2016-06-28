<?php

use yii\db\Schema;
use yii\db\Migration;

class m160201_101220_fix_uga_question_table extends Migration
{
    public function up()
    {
         $sql = <<<SQL
    ALTER TABLE `uga_question` CHANGE COLUMN `anwers_num` `answer_num` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '问题回答的总数量';
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
