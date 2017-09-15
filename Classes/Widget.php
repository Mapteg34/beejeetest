<?php

namespace Mapt\Beejeetest;

abstract class Widget
{
    /**
     * @param array ...$params
     *
     * @return static
     */
    static public function widget(...$params)
    {
        return new static(...$params);
    }

    /**
     * @return string
     */
    abstract public function fetch();
}