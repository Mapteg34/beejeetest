<?php

namespace Mapt\Beejeetest\Exceptions;

class NotFound extends HttpException
{
    public function __construct()
    {
        parent::__construct(404, "Page not found");
    }

}