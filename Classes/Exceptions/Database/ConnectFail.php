<?php

namespace Mapt\Beejeetest\Exceptions\Database;

class ConnectFail extends \Exception
{
    public function __construct()
    {
        parent::__construct("Database connect failed");
    }

}