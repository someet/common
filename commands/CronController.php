<?php
	namespace app\commands;

	use Yii;
	class CronController  extends \yii\console\Controller
	{
		/**
		 * 发送短信通知
		 * 每天10点执行
		 *
		 * @param string $time 时间
		 */
		public function actionSendSms($time)
		{
			echo "test", PHP_EOL;
			
			Yii::error('fsddfsdfs');
		}
	}
