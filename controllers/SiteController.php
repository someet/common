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
                    'fetch'
                ]
            ],
        ];
    }

    /**
     * 站点首页
     */
    public function actionIndex()
    {
        return $this->renderPartial('index');
    }
    /**
    *站点首页信息
    *
    */
    public function actionFetch()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $activity_test_type_id = Yii::$app->params['activity.test_type_id'];

        // 所有授权关注的人数
        $countUser = User::find()->count('id');
        // 所有已经完善资料的人数
        $countUserInfo = User::find()
                        ->andwhere(['not',['wechat_id'=> null]])
                        ->count('wechat_id');
        // 今日新增授权关注人数
        $todayStart = strtotime("today");
        $todayEnd = $todayStart + 60*60*24;
        // print_r($todayEnd);
        $newToday = User::find()->where(['between','created_at',$todayStart,$todayEnd])->count('id');
        // 今日已经完善资料人数
        $newTodayUserInfo = User::find()
                            ->where(['between','created_at',$todayStart,$todayEnd])
                            ->andwhere(['not',['wechat_id'=> null]])
                            ->count('id');
        // 发起人数量（不包括admin）

        // 今日已经完善资料人数
        $newTodayUserInfo = User::find()
                            ->where(['between','created_at',$todayStart,$todayEnd])
                            ->andwhere(['not',['wechat_id'=> null]])
                            ->count('id');
        // PMA数量（不包括admin）
        $countPma = User::find()
                            ->joinWith('assignment')
                            ->where([
                                'status' => User::STATUS_ACTIVE,
                                'auth_assignment.item_name' => 'pma',
                            ])
                            ->with(['profile'])
                            ->orderBy(['id' => SORT_DESC])
                            ->asArray()
                            ->count();
        // print_r($countPma);
        $countFounder = User::find()
                            ->joinWith('assignment')
                            ->where([
                                'status' => User::STATUS_ACTIVE,
                                'auth_assignment.item_name' => 'founder',
                            ])
                            ->with(['profile'])
                            ->orderBy(['id' => SORT_DESC])
                            ->asArray()
                            ->count();
                // print_r($countFounder);

        // 本周活动数量（不包括测试）
        $countWeekActivity = Activity::find()
                            ->where('type_id!='.$activity_test_type_id)
                            ->where('start_time > '.getLastEndTime())
                            ->count('id');
        // 当前活动总报名名额， select activity_id ,count(activity_id) FROM answer GROUP BY activity_id  

        $countJoinAsc = [];
        $countJoinDesc = [];
        
        $countJoin = Answer::find()
                    ->select(['answer.activity_id activity_id','count(answer.activity_id) as countJoin','activity.title title','activity.peoples peoples'])
                    ->where('type_id!='.$activity_test_type_id)
                    ->andwhere('answer.created_at > '.getLastEndTime())
                    ->groupBy('answer.activity_id')
                    ->leftJoin('activity','answer.activity_id = activity.id')
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
                $countJoin[$key]['order'] = round($value['countJoin'] / $value['peoples']*100);
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
            foreach($countJoin AS $uniqid => $row){  
                foreach($row AS $key=>$value){  
                    $arrSort[$key][$uniqid] = $value;  
                }  
            } 

            // 处理多维数组 升序
            array_multisort($arrSort[$sort_asc['field']], constant($sort_asc['direction']), $countJoin);  //升序

            $countJoinAsc =  array_slice($countJoin,0,10);


            // 处理多维数组 降序
            array_multisort($arrSort[$sort_desc['field']], constant($sort_desc['direction']), $countJoin);  //降序
            $countJoinDesc = array_slice($countJoin,0,10);
        }

        // 当前活动总报名名额
        // $countAllJoin = Answer::find()
        //             ->where('created_at > '.getLastEndTime())
        //             ->count();  
        // 报名数量
        // $countAlreadyJoin = Activity::find()
        //             ->where('type_id!='.$activity_test_type_id)
        //             ->andwhere()
        //             ->andwhere('start_time > '.getLastEndTime())
        //             ->count();
        // 及已报名数量
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
