<?php

use yii\db\Migration;

class m160721_083358_add_share_to_city extends Migration
{
    public function up()
    {
        $sql = <<<SQL
ALTER TABLE `city`
ADD COLUMN `img` VARCHAR(255) NULL DEFAULT NULL COMMENT '在首页banner显示的图片' AFTER `status`,
ADD COLUMN `share_title` VARCHAR(255) NULL DEFAULT NULL COMMENT '分享的标题' AFTER `img`,
ADD COLUMN `share_desc` VARCHAR(255) NULL DEFAULT NULL COMMENT '分享的描述' AFTER `share_title`,
ADD COLUMN `share_link` VARCHAR(255) NULL DEFAULT NULL COMMENT '分享的链接' AFTER `share_desc`,
ADD COLUMN `share_img` VARCHAR(255) NULL DEFAULT NULL COMMENT '分享的图片' AFTER `share_link`;
SQL;
        $this->execute($sql);
        return true;

    }

    public function down()
    {
        $this->dropColumn('city', 'img');
        $this->dropColumn('city', 'share_title');
        $this->dropColumn('city', 'share_desc');
        $this->dropColumn('city', 'share_link');
        $this->dropColumn('city', 'share_img');
        return true;
    }
}
