<?php

namespace Mapt\Beejeetest\Controllers;

use Mapt\Beejeetest\Controller;

class ErrorPage extends Controller
{
    /**
     * @var string
     */
    private $error;

    /**
     * ErrorPage constructor.
     *
     * @param string $error
     */
    public function __construct(string $error)
    {
        $this->error = $error;
    }

    /**
     * @param string $route
     *
     * @return string
     */
    public function fetch(string $route = "")
    {
        return app()->includeView("error", [
            "error" => $this->error
        ]);
    }
}