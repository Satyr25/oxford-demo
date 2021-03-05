<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>

<div class="alumno-search col-md-6" id="search-block">
    <div class="col-md-12">
        <?php $form = ActiveForm::begin([
            'method' => 'get',
            'class' => 'col-md-12'
        ]); ?>
        <div class="col-md-6">
            <?= $form->field($filtro, 'examenes')->dropDownList($examen_nivel,['prompt' => 'Exam'])->label(false) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($filtro, 'nombre')->textInput(['placeholder'=>'Search', 'class'=>'form-control campo-busqueda'])->label(false) ?>
        </div>
        <div class="clear"></div>
        <div class="form-group" style="text-align:right;">
            <?= Html::submitButton('Search', ['class' => 'btn-oxford']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>
