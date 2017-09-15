<?php

namespace Mapt\Beejeetest\Exceptions;

use Exception;

class ViewNotFound extends Exception
{
    private $viewName;

    public function __construct(string $viewName)
    {
        $this->viewName = $viewName;
        parent::__construct("View ".$this->viewName." not found");
    }
}