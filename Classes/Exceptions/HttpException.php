<?php

namespace Mapt\Beejeetest\Exceptions;

class HttpException extends \Exception
{
    /**
     * @var int
     */
    private $http_code;

    /**
     * HttpException constructor.
     *
     * @param int $http_code
     * @param string $message
     */
    public function __construct(int $http_code, string $message)
    {
        $this->http_code = $http_code;
        parent::__construct($message);
    }

    /**
     * @return int
     */
    public function getHttpCode()
    {
        return $this->http_code;
    }
}