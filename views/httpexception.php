<?php

/** @var int $http_code */
/** @var string $message */

?>
<div class="error-template">
    <h1>
        Oops!</h1>
    <h2>Error: <?=$http_code?></h2>
    <div class="error-details">
        <?=$message?>
    </div>
    <div class="error-actions">
        <a href="/" class="btn btn-primary btn-lg">
            <span class="glyphicon glyphicon-home"></span>
            Take Me Home
        </a>
        <a href="mailto:mapt@ibs1c.ru" class="btn btn-default btn-lg">
            <span class="glyphicon glyphicon-envelope"></span>
            Contact Support
        </a>
    </div>
</div>