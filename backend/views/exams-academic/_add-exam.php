<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
?>

<div class="formulario">

    <h2>Add an Exam</h2>

    <?php $form = ActiveForm::begin(['id' => 'exam-add-form', 'action' => 'add-exam']); ?>
    <div class="instituto-fields">
        <div class="col-md-12">
            <?= $form->field($examForm, 'tipo')->dropDownList($tipos,['prompt'=>'Select']) ?>
        </div>
        <div class="col-md-12" id="diagnostic-version" style="display:none;">
            <?= $form->field($examForm, 'diagnostic_v2')->radioList( [0=>'V1', 1 => 'V2', 2 => 'V3'] ) ?>
        </div>
        <div class="col-md-12" id="certificate-version" style="display:none;">
            <?= $form->field($examForm, 'certificate_v2')->radioList( [0=>'V1', 1 => 'V2'] ) ?>
        </div>
        <div class="col-md-12">
            <?= $form->field($examForm, 'nivel')->dropDownList($niveles, ['prompt'=>'Select']) ?>
        </div>
        <div class="col-md-12">
            <?= $form->field($examForm, 'version')->dropDownList($versiones, ['prompt'=>'Select']) ?>
        </div>
        <div class="col-md-12">
            <?= $form->field($examForm, 'porcentaje')->textInput() ?>
        </div>
        <div class="col-md-12">
            <?= $form->field($examForm, 'puntos')->textInput() ?>
        </div>
        <div id="bloque-duraciones">
            <h2>Durations</h2>
            <div class="col-md-12">
                <?= $form->field($examForm, 'reading_duration')->textInput(['placeholder' => 'Minutes']) ?>
            </div>
            <div class="col-md-12">
                <?= $form->field($examForm, 'writing_duration')->textInput(['placeholder' => 'Minutes']) ?>
            </div>
            <div class="col-md-12">
                <?= $form->field($examForm, 'listening_duration')->textInput(['placeholder' => 'Minutes']) ?>
            </div>
            <div class="col-md-12">
                <?= $form->field($examForm, 'english_duration')->textInput(['placeholder' => 'Minutes']) ?>
            </div>
        </div>
        <div class="form-group col-md-12" id="div-guardar">
            <?= Html::submitButton('Save', ['class' => 'btn-oxford boton-peque']) ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>
