<?php
/**
 * Application parameters
 */
return [
    'support.email' => 'webmaster@example.com',
    'support.name' => 'My Support',

    'adminEmail' => 'maxwelldu@someet.so',

    'user.passwordResetTokenExpire' => 3600,
    'user.emailConfirmationTokenExpire' => 43200, // 5 days

    'qiniu.upload_token_expires' => \DockerEnv::get('QINIU_UPLOAD_TOKEN_EXPIRES', 3600),
    'qiniu.access_domain' => \DockerEnv::get('QINIU_ACCESS_DOMAIN'),
    'qiniu.bucket' => \DockerEnv::get('QINIU_BUCKET'),

    'yunpian.api_key' => \DockerEnv::get('YUNPIAN_API_KEY'),

    'sms.success_template_id' => \DockerEnv::get('WEIXIN_TEMPLATE_SUCCESS_ID'),
    'sms.wait_template_id' => \DockerEnv::get('WEIXIN_TEMPLATE_WAIT_ID'),
    'sms.failed_template_id' => \DockerEnv::get('WEIXIN_TEMPLATE_FAILED_ID'),
    'sms.noti_template_id' => \DockerEnv::get('WEIXIN_TEMPLATE_NOTI_ID'),
    'sms.feedback_template_id' => \DockerEnv::get('WEIXIN_TEMPLATE_FEEDBACK_ID'),
    'sms.success_check_in_template_id' => \DockerEnv::get('WEIXIN_TEMPLATE_CHECK_IN_ID'),


    'domain' => \DockerEnv::get('WECHAT_DOMAIN'),

    'weather.cityid' => \DockerEnv::get('WEATHER_CITYID'),
];
