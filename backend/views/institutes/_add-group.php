<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
?>

<div class="formulario">

    <h2>Group Information</h2>

    <?php $form = ActiveForm::begin(['id'=>'grupo-add-form']); ?>
    <div class="instituto-fields">
        <?= Html::hiddenInput('GrupoForm[id]', $id); ?>
        <div class="col-md-12">
            <?= $form-> field($grupoForm,'nombre')->textInput(['required'=>'true'])?>
        </div>
        <div class="col-md-12">
            <?= $form-> field($grupoForm,'nivel')->dropDownList($niveles, ['prompt'=>'Select'])?>
        </div>
        <!-- <div class="col-md-12">
            <?php // echo $form-> field($grupoForm,'num_estudiantes')->textInput(['required'=>'true'])?>
        </div> -->
        <div class="col-md-12">
            <?= $form->field($grupoForm,'status') ->dropDownList(['0' => 'Inactivo', '1' => 'Activo'] , ['prompt'=>'Select']);?>
        </div>
        <div class="form-group col-md-12" id="div-guardar">
            <?= Html::a('Save','javascript:;' ,['class' => 'btn-oxford','id' => 'boton-guardar-grupo']) ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>
