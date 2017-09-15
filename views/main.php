<?php

use Mapt\Beejeetest\PageController;
use Mapt\Beejeetest\Widgets\Menu;

/** @var $controller PageController */

$menu = [
    ["href" => "/", "name" => "Home"],
    ["href" => "/tasks/add/", "name" => "Add task"]
];

if (user()->isAuthorized()) {
    $menu[] = ["href" => "/login/exit/", "name" => "Exit (".user()->login.")"];
} else {
    $menu[] = ["href" => "/login/", "name" => "Login"];
}

?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <title><?=$controller->showTitle()?></title>
    <link href="/assets/css/styles.css" rel="stylesheet"/>
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
    <?=$controller->showHead()?>
</head>
<body>
<div class="wrapper container">
    <?=Menu::widget($menu)->fetch()?>
    <div class="heading">
        <h1><?=$controller->showTitle()?></h1>
    </div>
    <div class="row">
        <?=$content?>
    </div>
</div>
<footer class="navbar-fixed-bottom">
    Created by Malaholv Artem
</footer>
</body>
</html>