<?php

/** @var string|null $error */
/** @var bool|null $saved */
/** @var string $maxsize */

?>
<form method="POST" class="editTaskForm" enctype="multipart/form-data">
    <input type="hidden" name="formID" value="addForm"/>
    <div class="form-group">
        <label for="text" class="sr-only">Text</label>
        <textarea class="form-control" name="text" id="text"></textarea>
    </div>
    <div class="form-group">
        <label>
            Изображение (JPG/GIF/PNG, <?=$maxsize?>)
            <input type="file" name="image"/>
        </label>
    </div>

    <? if (@$error): ?>
        <p class="error text-danger">Error: <?=$error?></p>
    <? elseif (@$saved === true): ?>
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