<?php

namespace Tonysm\Phredis;

use Exception;

class UnknownDataType extends Exception
{
    public function __construct()
    {
        parent::__construct('Unknown response type');
    }
}