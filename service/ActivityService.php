<?php
namespace someet\common\services;

use dektrium\user\models\Account;
use app\components\DataValidationFailedException;
use app\components\ObjectNotExistsException;
use someet\common\models\Activity;
use someet\common\models\ActivityFeedback;
use someet\common\models\ActivityType;
use someet\common\models\Answer;
use someet\common\models\Profile;
use someet\common\models\RActivityFounder;
use Yii;

class AnswerService extends \someet\common\models\Activity
{
	/**
	 * 更新报名率
	 * @return 
	 */
	public function updateRepalyRate($activity_id)
	{
		$model = Answer::findOne($activity_id);
		$model 
		 $apply_rate =[];
        foreach ($activities as  $answerKey => $answerValue) {
            // print_r($answerValue['id']);
            // die;
            // 报名数量
            $answerNum = Answer::find(['activity_id' => $answerValue['id']])->count();
            // print_r($answerNum);
            // die;
            // 取消报名
            $cancelApplyNum = Answer::find()->where(['activity_id' => $answerValue['id'],'apply_status' => Answer::APPLY_STATUS_YET])->count();
            //请假人数
            $leaveNum = Answer::find()->where(['activity_id' => $answerValue['id'],'leave_status'=>Answer::STATUS_LEAVE_YES])->count();            
            // 报名率＝（报名数量－取消报名－请假人数）／理想人数
            // print_r($answerKey);
            $activities[$answerKey]['apply_rate'] = ($answerNum - $cancelApplyNum - $leaveNum) / $answerValue['ideal_number'];
            $apply_rate[$answerKey]['apply_rate'] = ($answerNum - $cancelApplyNum - $leaveNum) / $answerValue['ideal_number'];
        }
        array_multisort($apply_rate, SORT_DESC, $activities); 
        print_r($activities);
	}


	public function ActivityDate()
	{
		 // 转化json格式
        $week =json_decode($week);
        $cost =json_decode($cost);


        Yii::$app->response->format = Response::FORMAT_JSON;

        //获取隐藏的活动分类编号
        $activity_test_type_ids = ActivityType::find()->select('id')->where(['status' => ActivityType::STATUS_NORMAL])->all();
        $activity_test_type_ids = is_array($activity_test_type_ids) ? array_column($activity_test_type_ids, 'id') : [];

        $session = Yii::$app->session;


        // 设置 周末数组 session
        if (!empty($week)) {
            if ($session->has('week')) {
                if ($session->get('week') != $week) {
                    $session->set('week', $week);
                    $week = $session->get('week');
                }
            } else {
                $session->set('week', $week);
            }
        }

        // 设置 花费数组 session
        if (!empty($cost)) {
            if ($session->has('cost')) {
                if ($session->get('cost') != $cost) {
                    $session->set('cost', $cost);
                    $cost = $session->get('cost');
                }
            } else {
                $session->set('cost', $cost);
            }
        }


        $data = Activity::find()
            ->joinWith('type')
            ->where(['activity_type.status' => ActivityType::STATUS_NORMAL])
            ->andWhere(['activity.status' => Activity::STATUS_RELEASE])
            ->orWhere('activity.status = ' . Activity::STATUS_SHUT . '&& activity_type.status = ' . ActivityType::STATUS_NORMAL)
            ->andWhere('activity.end_time > '.getLastEndTime())
            ->with(
                [
                'user',
                'user.profile',
                'type',
                'answerList',
                ]
            );

        if (!empty($week)) {
            $weekArr = [];

            // 非周末
            if (in_array(10, $week)) {
                $weekArr = ['1','2','3','4','5'];
            }

            //周日
            if (in_array(0, $week)) {
                $weekArr[] = 0;
            }

            //周六
            if (in_array(6, $week)) {
                $weekArr[] = 6;
            }
            count($weekArr)>0 && $data->andwhere(['week' => $weekArr]);
        }

        if (!empty($cost)) {
            if (in_array(0, $cost) && in_array(1, $cost)) {
                $data->andWhere('cost >= 0');
            } elseif (in_array(0, $cost)) {
                $data->andWhere('cost = 0');
                // $costArr = 'cost = 0';
            } elseif (in_array(1, $cost)) {
                $data->andWhere('cost > 0');
            }
            // $data->andwhere($costArr);
        }

        $pages = new Pagination(['totalCount' => $data->count()]);
        $activities = $data->offset($pages->offset)->limit($pages->limit);
        // foreach ($activities as  $answerKey => $answerValue) {
        //     // print_r($answerValue['id']);
        //     // die;
        //     // 报名数量
        //     $answerNum = Answer::find(['activity_id' => $answerValue->id])->count();
        //     // print_r($answerNum);
        //     // die;
        //     // 取消报名
        //     $cancelApplyNum = Answer::find()->where(['activity_id' => $answerValue->id,'apply_status' => Answer::APPLY_STATUS_YET])->count();
        //     //请假人数
        //     $leaveNum = Answer::find()->where(['activity_id' => $answerValue->id,'leave_status'=>Answer::STATUS_LEAVE_YES])->count();            
        //     // 报名率＝（报名数量－取消报名－请假人数）／理想人数
        //     // print_r($answerKey);
        //     $answerValue->apply_rate = ($answerNum - $cancelApplyNum - $leaveNum) / $answerValue ->ideal_number;
        //     // $apply_rate[$answerKey]['apply_rate'] = ($answerNum - $cancelApplyNum - $leaveNum) / $answerValue->ideal_number;
        // }




            $activities
            // ->asArray()
            ->orderBy(
                [
                'is_full' => SORT_ASC, //是否报满正序
                'case when `activity`.`status` = 30 then 0 else 1 end' => SORT_DESC, //活动关闭沉底
                'apply_rate' => SORT_DESC, //置顶降序
                'is_top' => SORT_DESC, //置顶降序
                'display_order' => SORT_ASC, //手动排序正序
                'id' => SORT_DESC, //编号排序倒序
                ]
            )
            // ->all();
            echo "<pre>";
            // $Activitiesi =  new Activitiesi();
        // foreach ($activities as $key => $value) {
        //     $value->applyrate = '1111111111';
            print_r($activities);
        // var_dump($value);
        die;
        // }
        // $apply_rate =[];
        // foreach ($activities as  $answerKey => $answerValue) {
        //     // print_r($answerValue['id']);
        //     // die;
        //     // 报名数量
        //     $answerNum = Answer::find(['activity_id' => $answerValue['id']])->count();
        //     // print_r($answerNum);
        //     // die;
        //     // 取消报名
        //     $cancelApplyNum = Answer::find()->where(['activity_id' => $answerValue['id'],'apply_status' => Answer::APPLY_STATUS_YET])->count();
        //     //请假人数
        //     $leaveNum = Answer::find()->where(['activity_id' => $answerValue['id'],'leave_status'=>Answer::STATUS_LEAVE_YES])->count();            
        //     // 报名率＝（报名数量－取消报名－请假人数）／理想人数
        //     // print_r($answerKey);
        //     $activities[$answerKey]['apply_rate'] = ($answerNum - $cancelApplyNum - $leaveNum) / $answerValue['ideal_number'];
        //     $apply_rate[$answerKey]['apply_rate'] = ($answerNum - $cancelApplyNum - $leaveNum) / $answerValue['ideal_number'];
        // }
        // array_multisort($apply_rate, SORT_DESC, $activities); 
        // print_r($activities);

        if ($activities) {
            return ['activities' => $activities, 'pages' => $pages];
        } else {
            return ['activities' => null, 'pages' => $pages];
        }
	}

}