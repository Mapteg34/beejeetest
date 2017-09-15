<?php

namespace Mapt\Beejeetest\Controllers;

use Mapt\Beejeetest\PageController;

class HttpExceptionPage extends PageController
{
    /**
     * @var int
     */
    private $http_code;

    /**
     * @var string
     */
    private $message;

    /**
     * HttpExceptionPage constructor.
     *
     * @param int $http_code
     * @param string $message
     */
    public function __construct(int $http_code, string $message)
    {
        $this->http_code = $http_code;
        $this->message   = $message;
    }

    /**
     * @return string
     */
    public function actionIndex()
    {
        $this->setTitle($this->http_code." ".$this->message);

        if ($this->http_code == 404) {
            header("HTTP/1.0 404 Not Found");
        } elseif ($this->http_code == 403) {
            header('HTTP/1.0 403 Forbidden');
        }

        return app()->includeView("httpexception", [
            "http_code" => $this->http_code,
            "message"   => $this->message
        ]);
    }

    /**
     * @param string $route
     *
     * @return string
     */
    public function getRouteAction(string $route)
    {
        return "index";
    }
}