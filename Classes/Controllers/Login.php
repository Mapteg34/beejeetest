<?php

namespace Mapt\Beejeetest\Controllers;

use Mapt\Beejeetest\PageController;

class Login extends PageController
{
    /**
     * @return string
     */
    public function actionIndex()
    {
        $error = false;
        if ($_SERVER["REQUEST_METHOD"] == "POST" && @$_POST["formID"] == "loginForm") {
            if (!user()->auth(@$_POST["login"], @$_POST["password"])) {
                $error = "Auth fail";
            } else {
                app()->redirect("/");
            }
        }

        $this->setTitle("Login");

        return app()->includeView("login", [
            "error" => $error
        ]);
    }

    public function actionExit()
    {
        user()->logout();
        app()->redirect("/login/");
    }
}