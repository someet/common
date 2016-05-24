<?php
/**
 * Created by PhpStorm.
 * User: maxwelldu
 * Date: 20/5/2016
 * Time: 9:48 AM
 */

namespace someet\common\models;
// extends \yii\db\ActiveRecord
trait ActiveRecord 
{
    /**
    200	OK。一切正常。
    201	响应 POST 请求时成功创建一个资源。Location header 包含的URL指向新创建的资源。
    204	该请求被成功处理，响应不包含正文内容 (类似 DELETE 请求)。
    304	资源没有被修改。可以使用缓存的版本。
    400	错误的请求。可能通过用户方面的多种原因引起的，例如在请求体内有无效的JSON 数据，无效的操作参数，等等。
    401	验证失败。
    403	已经经过身份验证的用户不允许访问指定的 API 末端。
    404	所请求的资源不存在。
    405	不被允许的方法。 请检查 Allow header 允许的HTTP方法。
    415	不支持的媒体类型。 所请求的内容类型或版本号是无效的。
    422	数据验证失败 (例如，响应一个 POST 请求)。 请检查响应体内详细的错误消息。
    429	请求过多。 由于限速请求被拒绝。
    500	内部服务器错误。 这可能是由于内部程序错误引起的。200
    */

    // const CODE_OK           = 200;
    // const CODE_CREATED      = 201;
    // const CODE_NO_CONTENT   = 204;
    // const CODE_NOT_MODIFIED = 304;
    // const CODE_BAD_REQUEST  = 400;
    // const CODE_UNAUTHORIZED = 401;
    // const CODE_FORBIDDEN    = 403;
    // const CODE_NOT_FOUND    = 404;
    // const CODE_METHOD_NOT_ALLOWED       = 405;
    // const CODE_UNSUPPORTED_MEDIA_TYPE   = 415;
    // const CODE_UNPROCESSABLE_ENTITY     = 422;
    // const CODE_RATE_LIMIT   = 429;
    // const CODE_INTERNAL_SERVER_ERROR    = 500;

    private $log_category = 'active.record';

    private $lastError;

    public function getLastError()
    {
        $lastError = $this->lastError;
        $this->lastError = null;
        return $lastError;
    }

    protected function setError($msg, $code = null, $detail = '')
    {
        $this->lastError = compact('msg', 'code', 'detail');
        \Yii::error($msg, $this->log_category);
        // return $msg."222222222";
    }

    public function hasError()
    {
        return $this->lastError != null;
    }
}