<?php
/**
 * Application parameters
 */
return [
    'support.email' => 'webmaster@example.com',
    'support.name' => 'My Support',

    'user.passwordResetTokenExpire' => 3600,
    'user.emailConfirmationTokenExpire' => 43200, // 5 days

    'qiniu.access_domain' => \DockerEnv::get('QINIU_ACCESS_DOMAIN'),
    'qiniu.bucket' => \DockerEnv::get('QINIU_BUCKET'),
];
