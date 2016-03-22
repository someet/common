<?php

use yii\db\Schema;
use yii\db\Migration;

class m160317_072440_black_label_user_table extends Migration
{
    public function up()
    {
        $sql = <<<SQL
ALTER TABLE `user` CHANGE COLUMN `black_label` `black_label` tinyint(3) DEFAULT NULL COMMENT '0 正常报名 1被拉黑', CHANGE COLUMN `black_time` `black_time` int(11) DEFAULT NULL COMMENT '黑名单创建时间 为期30天';
SQL;
        $this->execute($sql);
        return true;

    }

    public function down()
    {

        $this->dropColumn('user', 'black_label');
        $this->dropColumn('user', 'black_time');
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
