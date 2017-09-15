<?php

/** @var string $error */

?>
<div class="form-signin">
    <form method="POST">
        <input type="hidden" name="formID" value="loginForm"/>
        <h2 class="form-signin-heading">Please sign in</h2>
        <div class="form-group">
            <label for="inputLogin" class="sr-only">Login</label>
            <input type="text" id="inputLogin" class="form-control" placeholder="Login" name="login" required autofocus
                   <? if (isset($_POST["login"])): ?>value="<?=htmlspecialchars($_POST["login"], ENT_QUOTES)?>"<? endif ?>
                   <? if (isset($_POST["login"])): ?>value="<?=htmlspecialchars($_POST["login"], ENT_QUOTES)?>"<? endif ?>/>
        </div>
        <div class="form-group">
            <label for="inputPassword" class="sr-only">Password</label>
            <input type="password" id="inputPassword" class="form-control" placeholder="Password" name="password"
                   required
                   <? if (isset($_POST["password"])): ?>value="<?=htmlspecialchars($_POST["password"], ENT_QUOTES)?>"<? endif ?> />
        </div>
        <? if (@$error): ?>
            <p class="error text-danger">Error: <?=$error?></p>
        <? endif ?>
        <button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>
    </form>
</div>