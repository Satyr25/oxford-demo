<?php
use yii\helpers\Url;
?>
<div id="update-reading" class="formulario">
    <h2>Update Audio</h2>
    <div class="form-group">
        <label class="control-label">Title</label>
        <input type="text" id="audio-title" value="<?= $audio->nombre ?>" />
    </div>
    <audio controls>
        <source src="<?php echo Url::to('@web/' . $audio->audio) ?>" type="audio/mpeg">
    </audio>

    <div class="form-group">
        <label>New Audio</label>
        <input type="file" id="nuevo-audio" accept="audio/*">
     </div>

    <input type="hidden" id="audio-id" value="<?= $audio->id ?>" />
    <a href="javascript:;" id="update-audio-btn" class="btn-oxford boton-peque">Update</a>
    <div class="spinner sk-three-bounce" style="display:none;">
        <div class="sk-child sk-bounce1"></div>
        <div class="sk-child sk-bounce2"></div>
        <div class="sk-child sk-bounce3"></div>
    </div>
    <div id="mensaje-edicion"></div>
</div>
