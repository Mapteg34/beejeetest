<?php

namespace Mapt\Beejeetest\Interfaces;

interface Instanceable
{
    /**
     * @return static
     */
    public static function instance();
}