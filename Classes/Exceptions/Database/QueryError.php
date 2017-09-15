<?php

namespace Mapt\Beejeetest\Exceptions\Database;

class QueryError extends \Exception
{
    public function __construct()
    {
        parent::__construct("Error on exec query: ".db()->executedquery());
    }

}