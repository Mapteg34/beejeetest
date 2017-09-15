<?php

/** @var array $items */

?>
<? if (count($items) > 0): ?>
    <nav class="navbar navbar-default">
        <ul class="nav navbar-nav">
            <? foreach ($items as $item): ?>
                <li <? if (@$item["active"]): ?>class="active"<? endif ?>>
                    <a href="<?=htmlspecialchars($item["href"], ENT_QUOTES)?>">
                        <?=htmlspecialchars($item["name"])?>
                    </a>
                </li>
            <? endforeach ?>
        </ul>
    </nav>
<? endif ?>