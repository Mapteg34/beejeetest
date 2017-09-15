<?php

namespace Mapt\Beejeetest;

class Config
{
    /**
     * @var array
     */
    private $arConfig;

    /**
     * Config constructor.
     *
     * @param string $configFileName
     */
    public function __construct(string $configFileName)
    {
        $this->arConfig = include $configFileName;
    }

    /**
     * @param string $name
     *
     * @return mixed
     */
    public function __get(string $name)
    {
        return $this->arConfig[$name];
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function __isset(string $name)
    {
        return isset($this->arConfig[$name]);
    }
}