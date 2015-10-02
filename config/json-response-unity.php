<?php
/**
 * For response config
 *
 * Usage:
 *
 * ~~~
 * #file: main.php
 *
 * return [
 *   'components' => [
 *     'response' => [
 *       'class' => 'yii\web\Response',
 *       'on beforeSend' => require(__DIR__ . '/../../common/config/json-response-unity.php'),
 *     ],
 *   ],
 * ];
 * ~~~
 */

return function ($event) {
    /* @var \yii\web\Response $response */
    $response = $event->sender;

    if ($response->format == \yii\web\Response::FORMAT_JSON) {
        if ($response->isSuccessful) {
            $data['success'] = "1";
            $data['data'] = $response->data;
        } else {
            $data['success'] = "0";

            if (!empty($response->data['message'])) {
                $data['errmsg'] = $response->data["message"];
            } else {
                $data['errmsg'] = $response->data["name"];
            }
        }

        $data['status_code'] = $response->statusCode;
        $response->data = $data;
        $response->statusCode = 200;
    }
};
