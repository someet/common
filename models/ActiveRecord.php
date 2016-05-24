<?php
/**
 * Created by PhpStorm.
 * User: maxwelldu
 * Date: 20/5/2016
 * Time: 9:48 AM
 */

namespace someet\common\models;
trait ActiveRecord 
{

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
    }

    public function hasError()
    {
        return $this->lastError != null;
    }
}