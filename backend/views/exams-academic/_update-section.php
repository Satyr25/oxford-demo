<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>

<div>
    <h2 class="title text-center">Edit Section</h2>
    <?php $form = ActiveForm::begin([
        'action' => ['exams-academic/update-section'],
    ]); ?>
    <div class="row">
        <div class="col-md-12">
            <?= $form->field($model, 'id')->hiddenInput()->label(false) ?>
            <?= $form->field($model, 'instrucciones_generales')->textarea() ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <button type="submit" class="btn-oxford boton-peque center-block">Update</button>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>