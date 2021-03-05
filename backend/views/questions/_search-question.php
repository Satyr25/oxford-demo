<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>

<div class="question-search col-md-4" id="search-block">
    <div>
        <?php $form = ActiveForm::begin([
            'action' => ['index'],
            'method' => 'get',
        ]); ?>

        <?= $form->field($filtro, 'nombre')->textInput(['placeholder' => 'Search', 'class' => 'form-control campo-busqueda'])->label(false) ?>
        <?= $form->field($filtro, 'section')->dropDownList($secciones, ['prompt' => 'Section'])->label(false) ?>
        <?= $form->field($filtro, 'level')->dropDownList($level, ['prompt' => 'Level'])->label(false) ?>
        <?= $form->field($filtro, 'version')->dropDownList($version, ['prompt' => 'Version'])->label(false) ?>
        <?= $form->field($filtro, 'exam_type')->dropDownList($exam_type, ['prompt' => 'Exam'])->label(false) ?>

        <div class="form-group">
            <?= Html::submitButton('Buscar', ['class' => 'btn-buscar']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>

    <div class="botones-busqueda">
        <a href="javascript:;" class="acciones-busqueda">Export</a>
        <a href="javascript:;" class="acciones-busqueda" id="delete-multiple-questions">Delete</a>
    </div>
</div>
