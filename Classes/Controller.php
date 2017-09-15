<?php

namespace Mapt\Beejeetest;

abstract class Controller
{
    /**
     * @param string $route
     *
     * @return string
     */
    abstract public function fetch(string $route = "");
}