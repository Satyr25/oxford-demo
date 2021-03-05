<?php
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
?>
<div class="row">
    <?php $form = ActiveForm::begin(['id' => 'student-edit-form', 'action' => 'edit-student']); ?>
        <div class="instituto-fields">
            <?= Html::hiddenInput('StudentForm[id]', $alumno->id); ?>
            <div class="col-md-3 alinea-texto-der">
                <span>Name:</span>
            </div>
            <div class="col-md-3">
                <?= $form->field($studentForm, 'nombre')->textInput(['required' => 'true'])->label(false) ?>
            </div>
            <div class="col-md-3">
                <?= $form->field($studentForm, 'apellidos')->textInput(['required' => 'true'])->label(false) ?>
            </div>
            <div class="col-md-3"></div>

            <div class="form-group col-md-12" id="div-guardar">
                <?= Html::submitButton('Save', ['class' => 'btn-oxford boton-peque']) ?>
            </div>
        </div>
    <?php ActiveForm::end(); ?>
</div>