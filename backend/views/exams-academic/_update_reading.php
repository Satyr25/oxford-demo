<?php
use yii\helpers\Html;
?>
<div id="update-reading" class="formulario">
    <h2>Update Reading</h2>
    <div class="form-group">
        <label class="control-label">Title</label>
        <input type="text" id="article-title" value="<?= $articulo->titulo ?>" />
    </div>
    <div class="form-group">
        <label class="control-label">Article</label>
        <textarea id="article-text" rows="10"><?= $articulo->texto ?></textarea>
    </div>
    <div class="form-group">
        <label class="control-label">Image</label>
        <?php if($articulo->imagen){ ?>
            <?= Html::img('@web/'.$articulo->imagen,['class' => 'imagen-reading']) ?>
        <?php } ?>
        <input type="file" id="article-image" accept="jpg,jpeg,png" />
    </div>
    <input type="hidden" id="article-id" value="<?= $articulo->id ?>" />
    <a href="javascript:;" id="update-reading-btn" class="btn-oxford boton-peque">Update</a>
    <div class="spinner sk-three-bounce" style="display:none;">
        <div class="sk-child sk-bounce1"></div>
        <div class="sk-child sk-bounce2"></div>
        <div class="sk-child sk-bounce3"></div>
    </div>
    <div id="mensaje-edicion"></div>
</div>
