<?php

namespace Mapt\Beejeetest;

use Mapt\Beejeetest\Interfaces\Instanceable;
use Mapt\Beejeetest\Models\User;

class Appuser extends User
    implements Instanceable
{
    const SESSION_USERID_VARIABLE = "USER_ID";

    /**
     * @var static
     */
    private static $instance = null;

    /**
     * @return static|bool
     */
    final public static function instance()
    {
        if (self::$instance === null) {
            if (@$_SESSION[self::SESSION_USERID_VARIABLE]) {
                $user = static::selectOne([
                    "filter" => [
                        "id" => $_SESSION[self::SESSION_USERID_VARIABLE]
                    ]
                ]);

                return $user;
            } else {
                return new static();
            }
        }
    }

    /**
     * @param string $login
     * @param string $password
     *
     * @return bool
     */
    public function auth(string $login, string $password)
    {
        $user = static::selectOne([
            "filter" => [
                "login" => $login,
                "hash"  => md5($password)
            ]
        ]);
        if ($user) {
            $_SESSION[self::SESSION_USERID_VARIABLE] = $user->id;
            self::$instance                          = $user;

            return true;
        } else {
            return false;
        }
    }

    /**
     * @return bool
     */
    public function isAuthorized()
    {
        return
            is_numeric($this->id)
            &&
            $this->id > 0
            &&
            isset($_SESSION[self::SESSION_USERID_VARIABLE])
            &&
            $this->id == $_SESSION[self::SESSION_USERID_VARIABLE];
    }

    public function logout()
    {
        if (!$this->isAuthorized()) {
            return;
        }
        unset($_SESSION[self::SESSION_USERID_VARIABLE]);
    }
}