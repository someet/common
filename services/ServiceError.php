<?php

namespace someet\common\services;

trait ServiceError
{

    private $log_category = 'service.error';

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