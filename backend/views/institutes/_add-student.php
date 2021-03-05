<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
?>

<div class="formulario">

    <h2>Student Information</h2>

    <?php $form = ActiveForm::begin(['id'=>'student-add-form']); ?>
    <div class="instituto-fields">
        <?= Html::hiddenInput('StudentForm[id]', $id); ?>
        <div class="col-md-12">
            <?= $form-> field($studentForm,'nombre')->textInput(['required'=>'true'])?>
        </div>
        <div class="col-md-12">
            <?= $form-> field($studentForm,'apellidos')->textInput(['required'=>'true'])?>
        </div>
        <div class="col-md-12">
            <?= $form-> field($studentForm,'email')->textInput(['required'=>'true'])?>
        </div>
        <div class="form-group col-md-12" id="div-guardar">
            <?= Html::a('Save','javascript:;' ,['class' => 'btn-oxford','id' => 'boton-guardar-alumno']) ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>
