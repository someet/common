<?php
/**
 * Created by wsd312@163.com
 * User: wangshudong
 * Date: 16/1/29
 * Time: 下午3:36
 */

namespace app\components;

use yii\base\Component;
use Yii;
use yii\base\Exception;
use yii\base\InvalidConfigException;

class NotificationTemplate extends Component
{
    public static $week = [
        0 => '周天',
        1 => '周一',
        2 => '周二',
        3 => '周三',
        4 => '周四',
        5 => '周五',
        6 => '周六',
    ];

    /**
     * 获取成功的短信内容
     * @param integer $start_time 活动开始时间
     * @param string $activity_name 活动名称
     * @return string 短信内容
     */
    public static function fetchSuccessSmsData($start_time, $activity_name) {
        $start_time = $start_time > 0 ? date('n月j日', $start_time) : '';
        //获取通过的短信模板
        return "恭喜，你报名的{$start_time}“{$activity_name}”活动已通过筛选。详情请到微信公众号(SomeetInc)查看。";
    }
    /**
     * 获取等待的短信内容
     * @param string $activity_name 活动名称
     * @return string 等待的短信内容
     */
    public static function fetchWaitSmsData($activity_name) {
        //获取拒绝的短信模板
        return "你好，你报名的“{$activity_name}”活动，发起人正在筛选中，我们将会在24小时内短信给你最终筛选结果，请耐心等待。谢谢你的支持，系统短信，请勿回复。";
    }
    /**
     * 获取失败的短信内容
     * @param integer $start_time 活动开始时间
     * @param string $activity_name 活动名称
     * @param string $reason 被拒绝的原因
     * @return string 失败的短信内容
     */
    public static function fetchFailSmsData($start_time, $activity_name, $reason = '') {
        $start_time = $start_time > 0 ? date('n月j日', $start_time) : '';
        //获取拒绝的短信模板
        return "Shit happens！{$reason}很抱歉你报名的{$start_time}“{$activity_name}”活动未通过筛选。祝下次好运。详情请到微信公众号(SomeetInc)查看。";
    }

    /**
     * 获取通知参加活动的短信内容
     * @param string $activity_name 活动名称
     * @return string 通知参加活动的短信内容
     */
    public static function fetchNotiSmsData($activity_name, $start_time, $weather) {
        //获取通知参加活动的短信
        return "你报名的活动“{$activity_name}”在今天的{$start_time}开始。{$weather}请合理安排时间出行，不要迟到哦。";
    }

    /**
     * 获取需要反馈的短信内容
     * @param string $activity_name 活动名称
     * @return string 通知参加活动的短信内容
     */
    public static function fetchNeedFeedbackSmsData($activity_name) {
        //获取需要反馈的短信内容
        return " 你好，你已成功参加“{$activity_name}”活动，请及时对活动进行反馈，之后会提高下次通过筛选概率哦。";
    }



    /*
     * 获取等待的微信模板消息
     * @param $openid openid
     * @param $activity 活动对象
     * @return array
     */
    public static function fetchWaitWechatTemplateData($openid, $activity) {
        //获取等待的模板消息id
        $template_id = Yii::$app->params['sms.wait_template_id'];
        if (empty($template_id)) {
            //记录一个错误, 请设置等待的模板消息id
            Yii::error('请设置等待的模板消息id');
        }
        $start_time = date('m月d日', $activity['start_time'])
            . self::$week[date('w', $activity['start_time'])]
            . date('H:i', $activity['start_time'])
            . '开始';
        $data = [
            "touser" => "{$openid}",
            "template_id" => $template_id,
            "url" => Yii::$app->params['domain'],
            "topcolor" => "#FF0000",
            "data" => [
                "first" => [
                    "value" => "你报名的活动正在筛选，请耐心等待",
                    "color" => "#173177"
                ],
                "keyword1" => [
                    "value" => "{$activity['title']}",
                    "color" =>"#173177"
                ],
                "keyword2" => [
                    "value" => "{$start_time}",
                    "color" => "#173177"
                ],
                "keyword3" => [
                    "value" => "{$activity['area']}",
                    "color" => "#173177"
                ],
                "remark" => [
                    "value" => "请随时关注Someet服务号的通知，及时收到筛选结果信息。",
                    "color" => "#173177"
                ],
            ]
        ];
        return $data;
    }
    /*
     * 获取失败的微信模板消息
     * @param $openid openid
     * @param $account Account对象
     * @param $activity 活动对象
     * @return array
     */
    public static function fetchFailedWechatTemplateData($openid, $account, $activity) {
        //获取失败的模板消息id
        $template_id = Yii::$app->params['sms.failed_template_id'];
        if (empty($template_id)) {
            //记录一个错误, 请设置失败的模板消息id
            Yii::error('请设置失败的模板消息id');
        }
        $data = [
            "touser" => "{$openid}",
            "template_id" => $template_id,
            "url" => Yii::$app->params['domain'],
            "topcolor" => "#FF0000",
            "data" => [
                "first" => [
                    "value" => "抱歉，你报名的活动未通过筛选",
                    "color" => "#173177"
                ],
                "keyword1" => [
                    "value" => "{$account['username']}",
                    "color" => "#173177"
                ],
                "keyword2" => [
                    "value" => "{$activity['title']}",
                    "color" =>"#173177"
                ],
                "keyword3" => [
                    "value" => "",
                    "color" => "#173177"
                ],
                "keyword4" => [
                    "value" => "",
                    "color" => "#173177"
                ],
                "keyword5" => [
                    "value" => "发起人未通过你的报名申请。",
                    "color" => "#173177"
                ],
                "remark" => [
                    "value" => "每个人都有被拒绝的时候，点击详情，试试更多其他活动吧！",
                    "color" => "#173177"
                ],
            ]
        ];
        return $data;
    }
    /*
     * 获取参加活动通知的微信模板消息
     * @param $openid openid
     * @param $activity 活动对象
     * @return array
     */
    public static function fetchNotiWechatTemplateData($openid, $activity) {
        //获取失败的模板消息id
        $template_id = Yii::$app->params['sms.noti_template_id'];
        if (empty($template_id)) {
            //记录一个错误, 请设置失败的模板消息id
            Yii::error('请设置失败的模板消息id');
        }
        $start_time = date('Y年m月d日', $activity['start_time']);
        $data = [
            "touser" => "{$openid}",
            "template_id" => $template_id,
            "url" => Yii::$app->params['domain'],
            "topcolor" => "#FF0000",
            "data" => [
                "first" => [
                    "value" => "你好，你预定的活动马上开始！",
                    "color" => "#173177"
                ],
                "keyword1" => [
                    "value" => "{$activity['title']}",
                    "color" => "#173177"
                ],
                "keyword2" => [
                    "value" => "{$activity['address']}",
                    "color" =>"#173177"
                ],
                "keyword3" => [
                    "value" => "{$start_time}",
                    "color" => "#173177"
                ],
                "remark" => [
                    "value" => "请合理安排时间出行，不要迟到哦。",
                    "color" => "#173177"
                ],
            ]
        ];
        return $data;
    }

    /*
     * 获取需反馈活动通知的微信模板消息
     * @param $openid openid
     * @param $activity 活动对象
     * @return array
     */
    public static function fetchNeedFeedbackWechatTemplateData($openid, $account, $activity) {

        //获取需反馈活动通知的微信模板消息id
        $template_id = Yii::$app->params['sms.feedback_template_id'];
        if (empty($template_id)) {
            //记录一个错误, 请设置失败的模板消息id
            Yii::error('请设置失败的模板消息id111');
        }
        $start_time = date('Y年m月d日', $activity['start_time']);
        $data = [
            "touser" => "{$openid}",
            "template_id" => $template_id,
            "url" => Yii::$app->params['domain'],
            "topcolor" => "#FF0000",
            "data" => [
                "first" => [
                    "value" => "你好，你已成功参加Someet活动，请及时对活动进行反馈。",
                    "color" => "#173177"
                ],
                "keyword1" => [
                    "value" => "{$account['username']}",
                    "color" => "#173177"
                ],
                "keyword2" => [
                    "value" => "{$activity['title']}",
                    "color" =>"#173177"
                ],
                "keyword3" => [
                    "value" => "{$start_time}",
                    "color" => "#173177"
                ],
                "keyword4" => [
                    "value" => "{$activity['area']}",
                    "color" => "#173177"
                ],
                "remark" => [
                    "value" => "反馈后会提高下次通过筛选概率哦。",
                    "color" => "#173177"
                ],
            ]
        ];
        return $data;
    }
    /**
     * 获取成功的微信模板消息
     * @param $openid openid
     * @param $account Account对象
     * @param $activity 活动对象
     * @return array
     */
    public static function fetchSuccessWechatTemplateData($openid, $account, $activity) {
        //获取成功的模板消息id
        $template_id = Yii::$app->params['sms.success_template_id'];
        if (!empty($activity['group_code'])) {
            $url = $activity['group_code'];
        }else{
            $url = Yii::$app->params['domain'].'activity/'.$activity['id'];            
        }
        if (empty($template_id)) {
            //记录一个错误, 请设置成功的模板消息id
            Yii::error('请设置成功的模板消息id');
        }
        $start_time = date('m月d日', $activity['start_time'])
            . self::$week[date('w', $activity['start_time'])]
            . date('H:i', $activity['start_time'])
            . '开始';
        $data = [
            "touser" => "{$openid}",
            "template_id" => $template_id,
            "url" => $url,
            // "url" => Yii::$app->params['domain'].'activity/'.$activity['id'],
            "topcolor" => "#FF0000",
            "data" => [
                "first" => [
                    "value" => "恭喜，你报名的活动已通过筛选！",
                    "color" => "#173177"
                ],
                "keyword1" => [
                    "value" => "{$account['username']}",
                    "color" => "#173177"
                ],
                "keyword2" => [
                    "value" => "{$activity['title']}",
                    "color" =>"#173177"
                ],
                "keyword3" => [
                    "value" => "{$start_time}",
                    "color" => "#173177"
                ],
                "keyword4" => [
                    "value" => "{$activity['address']}",
                    "color" => "#173177"
                ],
                "remark" => [
                    "value" => "点击查看群二维码，扫码进入活动群。",
                    "color" => "#173177"
                ],
            ]
        ];
        return $data;
    }
}