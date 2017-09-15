<?php

/** @var \Mapt\Beejeetest\Models\Task $task */
/** @var string|null $error */
/** @var bool|null $edited */
/** @var string $maxsize */

?>

<form method="POST" class="editTaskForm" enctype="multipart/form-data">
    <input type="hidden" name="formID" value="editForm"/>
    <div class="form-group">
        <label for="text" class="sr-only">Text</label>
        <textarea class="form-control" name="text" id="text"><?=htmlspecialchars($task->text)?></textarea>
    </div>
    <div class="form-group checkbox">
        <label>
            Completed
            <input type="checkbox" value="Y" name="completed" <? if ($task->completed): ?>checked<? endif ?> />
        </label>
    </div>
    <div class="form-group">
        <label>
            Изображение (JPG/GIF/PNG, <?=$maxsize?>)
            <input type="file" name="image"/>
        </label>
    </div>

    <? if (@$error): ?>
        <p class="error text-danger">Error: <?=$error?></p>
    <? elseif (@$edited === true): ?>
        <p class="text-success">Saved</p>
    <? endif ?>
    <div class="btn-block">
        <button class="btn btn-lg preview-btn" type="button">Preview</button>
        <button class="btn btn-lg btn-primary" onclick="$(this).closest('form').find('.preview-container.').hide()"
                type="submit">Save
        </button>
    </div>
    <div class="preview-container" style="display:none">
        <h2>Preview</h2>
        <div class="body"></div>
    </div>
</form>