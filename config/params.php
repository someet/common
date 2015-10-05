<?php
/**
 * Application parameters
 */
return [
    'support.email' => 'webmaster@example.com',
    'support.name' => 'My Support',

    'user.passwordResetTokenExpire' => 3600,
    'user.emailConfirmationTokenExpire' => 43200, // 5 days

    'qiniu.upload_token_expires' => \DockerEnv::get('QINIU_UPLOAD_TOKEN_EXPIRES', 3600),
    'qiniu.access_domain' => \DockerEnv::get('QINIU_ACCESS_DOMAIN'),
    'qiniu.bucket' => \DockerEnv::get('QINIU_BUCKET'),
];
