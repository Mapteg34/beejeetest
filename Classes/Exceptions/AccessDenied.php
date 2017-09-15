<?php

namespace Mapt\Beejeetest\Exceptions;

class AccessDenied extends HttpException
{
    public function __construct()
    {
        parent::__construct(403, "Access denied");
    }
}