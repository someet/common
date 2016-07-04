<?php

namespace app\controllers;

use Exception;
use someet\common\models\forms\LoginForm;
use someet\common\models\Activity;
use someet\common\models\Answer;
use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use e96\sentry\SentryHelper;
use yii\web\Response;
use someet\common\models\User;
use someet\common\models\Profile;

/**
 *
 * 站点控制器
 *
 * @author Maxwell Du <maxwelldu@someet.so>
 * @package app\controllers
 */
class SiteController extends BackendController
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post', 'get'],
                ],
            ],
            'access' => [
                'class' => '\app\components\AccessControl',
                'allowActions' => [
                    'error',
                    'logout',
                ]
            ],
        ];
    }

    /**
     * 站点首页
     */
    public function actionIndex()
    {
        $auth = Yii::$app->authManager;
        $current_user_id = Yii::$app->user->getId();
        $assignments = $auth->getAssignments($current_user_id);
        $isManager = array_key_exists('admin', $assignments);
        $isFounder = array_key_exists('founder', $assignments) || !array_key_exists('admin', $assignments);
        
        $checkNum = Activity::find()
                        ->where(['status' => Activity::STATUS_CHECK])
                        ->count();
        if ($isManager) {
             return $this->renderPartial(
            'index',
                [
                    'checkNum' => $checkNum,
                ]
            );
        }elseif ($isFounder) {
            return $this->renderPartial('founder');

        }

        // Yii::$app->response->format = Response::FORMAT_JSON;
        // $checkNum = Activity::find()
        //                 ->where(['status' => Activity::STATUS_CHECK])
        //                 ->count();

        // return $checkNum
        // return [
        //    'countUser' => $countUser,
        // ];
        //  return $this->render(
        //     'index',
        //     [
        //         'share' => $model,
        //         'week' => $week,
        //         'cost' => $cost,
        //     ]
        // );
    }
    /**
    *站点首页信息
    *
    */
    public function actionFetch()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        // 所有授权关注的人数
        $countUser = User::find()->count('id');

        // 所有已经完善资料的人数
        $countUserInfo = User::find()
                        ->andwhere(['not', ['wechat_id'=> null]])
                        ->count('wechat_id');

        // 所有已经完善资料的人数 男
        $countUserInfoBoy = User::find()
                        ->andwhere(['not', ['wechat_id'=> null]])
                        ->joinWith('profile')
                        ->andwhere('profile.sex ='.Profile::SEX_BOY)
                        ->count('wechat_id');

        // 所有已经完善资料的人数 女
        $countUserInfoGirl = User::find()
                        ->andwhere(['not', ['wechat_id'=> null]])
                        ->joinWith('profile')
                        ->andwhere('profile.sex ='.Profile::SEX_GIRL)
                        ->count('wechat_id');

        // 今日新增授权关注人数
        $todayStart = strtotime("today");
        $todayEnd = $todayStart + 60*60*24;
        $newToday = User::find()->where(['between', 'created_at', $todayStart, $todayEnd])->count('id');

        // 今日已经完善资料人数
        $newTodayUserInfo = User::find()
                            ->where(['between', 'created_at', $todayStart, $todayEnd])
                            ->andwhere(['not', ['wechat_id'=> null]])
                            ->count('id');

        // PMA数量
        $countPma = User::find()
                            ->joinWith('assignment')
                            ->where([
                                'status' => User::STATUS_ACTIVE,
                                'auth_assignment.item_name' => 'pma',
                            ])
                            ->asArray()
                            ->count();

        // 发起人数量
        $countFounder = User::find()
                            ->joinWith('assignment')
                            ->where([
                                'status' => User::STATUS_ACTIVE,
                                'auth_assignment.item_name' => 'founder',
                            ])
                            ->asArray()
                            ->count();


        // 本周活动数量（不包括测试）
        $countWeekActivity = Activity::find()
                            ->andWhere('start_time > ' . getLastEndTime())
                            ->andWhere(['status' => Activity::STATUS_RELEASE])
                            ->count('id');

        // 当前活动总报名名额， select activity_id ,count(activity_id) FROM answer GROUP BY activity_id
        $countJoinAsc = [];
        $countJoinDesc = [];
              
        $countJoin = Activity::find()
                    ->select(['activity.id activity_id','count(answer.activity_id) as countJoin','activity.title title','activity.peoples peoples','activity.field1'])
                    ->andWhere('activity.status = ' . Activity::STATUS_RELEASE)
                    ->andWhere('activity.start_time > ' . getLastEndTime())
                    ->groupBy('activity.id')
                    ->leftJoin('answer', 'answer.activity_id = activity.id')
                    ->asArray()
                    ->all();

        // 当前活动总报名名额
        $countAllJoin = 0;
        // 及已报名数量
        $countAlreadyJoin = 0;

        foreach ($countJoin as $key => $value) {
            $countAllJoin += $value['peoples'];
            $countAlreadyJoin += $value['countJoin'];
        }

        if (!empty($countJoin)) {
            foreach ($countJoin as $key => $value) {
                if ($value['peoples'] == 0) {
                    $countJoin[$key]['order'] = 0;
                } else {
                    $countJoin[$key]['order'] = round($value['countJoin'] / $value['peoples']*100);
                }
            }

            $sort_desc = array(
                'direction' => 'SORT_DESC', //排序顺序标志  SORT_DESC  降序；  SORT_ASC 升序
                'field'     => 'order',       //排序字段
            );

            $sort_asc = array(
                'direction' => 'SORT_ASC', //排序顺序标志  SORT_DESC  降序；  SORT_ASC 升序
                'field'     => 'order',       //排序字段
            );
            
            $arrSort = array();
            foreach ($countJoin as $uniqid => $row) {
                foreach ($row as $key => $value) {
                    $arrSort[$key][$uniqid] = $value;
                }
            }

            // 处理多维数组 升序
            array_multisort($arrSort[$sort_asc['field']], constant($sort_asc['direction']), $countJoin);  //升序

            $countJoinAsc =  $countJoin;
            // array_slice($countJoin,0,10);

            // 处理多维数组 降序
            array_multisort($arrSort[$sort_desc['field']], constant($sort_desc['direction']), $countJoin);  //降序
            $countJoinDesc = $countJoin;
            // array_slice($countJoin,0,10);

            // 通过的总数
            $pass_count = Answer::find()
                            ->where(['status' => Answer::STATUS_REVIEW_PASS ])
                            ->count();

            // 迟到人数
            $arrive_late = Answer::find()
                            ->where(['arrive_status' => Answer::STATUS_ARRIVE_LATE,'status' => Answer::STATUS_REVIEW_PASS])
                            ->count();
            // 请假人数
            $leave = Answer::find()
                            ->where(['leave_status' => Answer::STATUS_LEAVE_YES,'status' => Answer::STATUS_REVIEW_PASS])
                            ->count();
            // 爽约人数
            $arrive_no = Answer::find()
                            ->where(['arrive_status' => Answer::STATUS_ARRIVE_YET,'status' => Answer::STATUS_REVIEW_PASS])
                            ->count();
            // echo $pass_count;
            // echo $arrive_late;
            if ($pass_count > 0) {
                $late_ratio = round($arrive_late / $pass_count, 2) *100 ."%";
                $leave_ratio = round($leave / $pass_count, 2) *100 ."%";
                $arrive_no_ratio = round($arrive_no / $pass_count, 2) *100 ."%";
            } else {
                $late_ratio = "0%";
                $leave_ratio = "0%";
                $arrive_no_ratio = "0%";
            }
        }

        return [
           'countUser' => $countUser,
           'countUserInfo' => $countUserInfo,
           'countNewToday' => $newToday,
           'countnewNewTodayUserInfo' => $newTodayUserInfo,
           'countWeekActivity' => $countWeekActivity,
           'countJoin' => $countJoin,
           'countFounder'=>$countFounder,
           'countPma'=>$countPma,
           'countAllJoin'=>$countAllJoin,
           'countAlreadyJoin'=>$countAlreadyJoin,
           'countJoinAsc'=>$countJoinAsc,
           'countJoinDesc'=>$countJoinDesc,
           'countUserInfoBoy'=>$countUserInfoBoy,
           'countUserInfoGirl'=>$countUserInfoGirl,
           'late_ratio'=>$late_ratio,
           'leave_ratio'=>$leave_ratio,
           'arrive_no_ratio'=>$arrive_no_ratio,
        ];

    }
    /**
     * 用户退出
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }
}
