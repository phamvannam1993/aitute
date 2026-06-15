<?php

namespace App\Exceptions;

use Exception;

class UsageLimitExceededException extends Exception
{
    public function __construct($message = "Bạn đã sử dụng hết số lần dùng thử. Vui lòng nâng cấp tài khoản.")
    {
        parent::__construct($message);
    }
}
