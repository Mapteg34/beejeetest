<?php

namespace Mapt\Beejeetest\Exceptions\Database;

class InvalidQuery extends \Exception
{
    public function __construct()
    {
        parent::__construct("Invalid query");
    }

}