<?php

namespace app\components;

use yii\web\HttpException;

class DataValidationFailedException extends HttpException
{
    /**
     * Constructor.
     *
     * @param string $message error message
     * @param integer $code error code
     * @param \Exception $previous The previous exception used for the exception chaining.
     */
    public function __construct($message = null, $code = 0, \Exception $previous = null)
    {
        parent::__construct(422, $message, $code, $previous);
    }
}