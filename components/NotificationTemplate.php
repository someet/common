<?php
/**
 * Created by wsd312@163.com
 * User: wangshudong
 * Date: 16/1/29
 * Time: 下午3:36
 */

namespace app\components;

use yii\base\Component;
use someet\common\models\YellowCard;
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
    public static function fetchSuccessSmsData($start_time, $activity_name)
    {
        $start_time = $start_time > 0 ? date('n月j日', $start_time) : '';
        //获取通过的短信模板
        return "恭喜，你报名的{$start_time}“{$activity_name}”活动已通过筛选。详情请到微信公众号(SomeetInc)查看。";
    }
    /**
     * 获取失败的短信内容
     * @param integer $start_time 活动开始时间
     * @param string $activity_name 活动名称
     * @param string $reason 被拒绝的原因
     * @return string 失败的短信内容
     */
    public static function fetchFailSmsData($start_time, $activity_name, $reason = '')
    {
        $start_time = $start_time > 0 ? date('n月j日', $start_time) : '';
        //获取拒绝的短信模板
        return "Shit happens！{$reason}很抱歉你报名的{$start_time}“{$activity_name}”活动未通过筛选。祝下次好运。详情请到微信公众号(SomeetInc)查看。";
    }

    /**
     * 获取通知参加活动的短信内容
     * @param string $activity_name 活动名称
     * @return string 通知参加活动的短信内容
     */
    public static function fetchNotiSmsData($activity_name, $start_time, $weather)
    {
        //获取通知参加活动的短信
        return "你报名的活动“{$activity_name}”在今天的{$start_time}开始。{$weather}请合理安排时间出行，不要迟到哦。";
    }

    /**
     * 获取需要反馈的短信内容
     * @param string $activity_name 活动名称
     * @return string 通知参加活动的短信内容
     */
    public static function fetchNeedFeedbackSmsData($activity_name)
    {
        //获取需要反馈的短信内容
        return " 你好，你已成功参加“{$activity_name}”活动，请及时对活动进行反馈，之后会提高下次通过筛选概率哦。";
    }

    /*
     * 获取失败的微信模板消息
     * @param $openid openid
     * @param $account Account对象
     * @param $activity 活动对象
     * @return array
     * 活动报名失败通知
     */
    public static function fetchFailedWechatTemplateData($openid, $account, $activity, $reject_reason)
    {
        //获取失败的模板消息id
        $template_id = Yii::$app->params['sms.failed_template_id'];
        if (empty($template_id)) {
            //记录一个错误, 请设置失败的模板消息id
            Yii::error('请设置失败的模板消息id');
        }

        // 活动开始时间
        $activity_start_time = date('m月d号', $activity['start_time'])
                                . self::$week[date('w', $activity['start_time'])]
                                . date('H:i', $activity['start_time']);
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
                    "value" => "{$activity_start_time}",
                    "color" => "#173177"
                ],
                "keyword4" => [
                    "value" => "{$activity['address']}",
                    "color" => "#173177"
                ],
                "keyword5" => [
                    "value" => "{$reject_reason}",
                    "color" => "#173177"
                ],
                "remark" => [
                    "value" => "关于如何提高报名的成功率，这里有几个小tips，1.认真回答筛选问题； 2.尽早报名，每周二周三是活动推送时间",
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
    public static function fetchNotiWechatTemplateData($openid, $activity)
    {
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
    public static function fetchNeedFeedbackWechatTemplateData($openid, $account, $activity)
    {

        //获取需反馈活动通知的微信模板消息id
        $template_id = Yii::$app->params['sms.feedback_template_id'];
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
    public static function fetchSuccessWechatTemplateData($openid, $account, $activity)
    {
        //获取成功的模板消息id
        $template_id = Yii::$app->params['sms.success_template_id'];
        if (!empty($activity['group_code'])) {
            $url = Yii::$app->params['domain'].'join/'.$activity['id'];
        } else {
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

    /**
     * 获取活动签到成功的微信模板消息
     * @param $openid openid
     * @param $account Account对象
     * @param $activity 活动对象
     * @return array
     */
    public static function fetchSuccessCheckInWechatTemplateData($openid, $account, $activity)
    {
        //获取模板消息id
        $template_id = Yii::$app->params['sms.success_check_in_template_id'];
        $url = Yii::$app->params['domain'].'feedback/'.$activity['id'];
        if (empty($template_id)) {
            //记录一个错误, 请设置成功的模板消息id
            Yii::error('请设置签到成功的模板消息id');
        }

        //签到时间
        $check_in_time = date('m月d日', time())
            . self::$week[date('w', time())]
            . date('H:i', time());
        $data = [
            "touser" => "{$openid}",
            "template_id" => $template_id,
            "url" => $url,
            "topcolor" => "#FF0000",
            "data" => [
                "first" => [
                    "value" => "你好，你已签到成功。",
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
                    "value" => "{$check_in_time}",
                    "color" => "#173177"
                ],
                "keyword4" => [
                    "value" => "{$activity['address']}",
                    "color" => "#173177"
                ],
                "remark" => [
                    "value" => "感谢你的参加!结束后请及时反馈",
                    "color" => "#173177"
                ],
            ]
        ];
        return $data;
    }

    /**
     * 获取活动信用变更的微信模板消息
     * @param $openid openid
     * @param $account Account对象
     * @param $activity 活动对象
     * @return array
     */
    public static function fetchUpdateCreditWechatTemplateData($openid, $activity, $yellowcard)
    {
        //获取模板消息id
        $template_id = Yii::$app->params['sms.update_credit_template_id'];
        $url = Yii::$app->params['domain'].'member/credit-record/'.$activity['id'];
        if (empty($template_id)) {
            //记录一个错误, 请设置成功的模板消息id
            Yii::error('请设置签到成功的模板消息id');
        }

       // 黄牌数量
        $card_count = YellowCard::find()
                        ->select('id , sum(card_num) card_count')
                        ->where(['user_id' => $yellowcard->user_id])
                        ->asArray()
                        ->one();

        $card_category = [
                    '0' => '取消',
                    '1' => '迟到',
                    '2' => '请假',
                    '3' => '请假',
                    '4' => '爽约',
                    '5' => '带人',
                    '6' => '骚扰',
                    ];
        // 活动开始时间
        $activity_start_time = date('m月d号', $activity['start_time'])
                                . self::$week[date('w', $activity['start_time'])]
                                . date('H:i', $activity['start_time']);
        $data = [
            "touser" => "{$openid}",
            "template_id" => $template_id,
            "url" => $url,
            "topcolor" => "#FF0000",
            "data" => [
                "first" => [
                    "value" => "您好，因为参加活动时{$card_category[$yellowcard->card_category]}，影响发起人和其他参与者体验，受到黄牌警告{$yellowcard->card_num}张",
                    "color" => "#173177"
                ],
                "keyword1" => [
                    "value" => "{$activity['title']}",
                    "color" => "#173177"
                ],
                "keyword2" => [
                    "value" => "{$activity_start_time}",
                    "color" =>"#173177"
                ],
                "keyword3" => [
                    "value" => "{$activity['address']}",
                    "color" => "#173177"
                ],
                "keyword4" => [
                    "value" => "累计{$card_count['card_count']}张黄牌",
                    "color" => "#173177"
                ],
                "remark" => [
                    "value" => "30天内累计3张黄牌将无法报名，黄牌信息不会展示给他人，黄牌记录将在30天内自动过期。如有异议请在产品中申诉。",
                    "color" => "#173177"
                ],
            ]
        ];
        return $data;
    }


  /**
     * 获取活动取消 信用变更的微信模板消息
     * @param $openid OPENID
     * @param $answer 答案
     * @return array
     */
    public static function fetchUpdateCancelActivityWechatTemplateData($openid, $answer)
    {
        //获取模板消息id
        $template_id = Yii::$app->params['sms.update_credit_template_id'];
        $url = Yii::$app->params['domain'].'member/credit-record';
        if (empty($template_id)) {
            //记录一个错误, 请设置成功的模板消息id
            Yii::error('请设置取消报名的模板消息id');
        }

       // 黄牌数量
        $card_count = YellowCard::find()
                        ->select('id , sum(card_num) card_count')
                        ->where(['user_id' => $answer->user_id])
                        ->asArray()
                        ->one();

        $card_category = [
                    '0' => '取消',
                    '1' => '迟到',
                    '2' => '请假',
                    '3' => '请假',
                    '4' => '爽约',
                    '5' => '带人',
                    '6' => '骚扰',
                    ];
        // 活动开始时间
        $activity_start_time = date('m月d号', $answer->activity['start_time'])
                                . self::$week[date('w', $answer->activity['start_time'])]
                                . date('H:i', $answer->activity['start_time']);
        $data = [
            "touser" => "{$openid}",
            "template_id" => $template_id,
            "url" => $url,
            "topcolor" => "#FF0000",
            "data" => [
                "first" => [
                    "value" => "你好，取消报名成功！",
                    "color" => "#173177"
                ],
                "keyword1" => [
                    "value" => "{$answer->activity['title']}",
                    "color" => "#173177"
                ],
                "keyword2" => [
                    "value" => "{$activity_start_time}",
                    "color" =>"#173177"
                ],
                "keyword3" => [
                    "value" => "{$answer->activity['area']}",
                    "color" => "#173177"
                ],
                "keyword4" => [
                    "value" => "累计{$card_count['card_count']}张黄牌",
                    "color" => "#173177"
                ],
                "remark" => [
                    "value" => "30天内累计3张黄牌将无法报名，黄牌信息不会展示给他人，黄牌记录将在30天内自动过期。如有异议请在产品中申诉。",
                    "color" => "#173177"
                ],
            ]
        ];
        return $data;
    }
}
