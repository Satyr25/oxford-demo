<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>

<div class="examen-search col-md-4" id="search-block">
    <div>
        <?php $form = ActiveForm::begin([
            'action' => ['index'],
            'method' => 'get',
        ]); ?>

        <?= $form->field($filtro, 'nombre')->textInput(['placeholder'=>'Search', 'class'=>'form-control campo-busqueda'])->label(false) ?>

        <div class="form-group">
            <?= Html::submitButton('Buscar', ['class' => 'btn-buscar']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>

    <div class="botones-busqueda">
        <a href="javascript:;" class="acciones-busqueda">Export</a>
        <a href="javascript:;" id="borrar-multiple-examen" class="acciones-busqueda">Delete</a>
    </div>
</div>
