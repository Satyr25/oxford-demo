<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\depdrop\DepDrop;
use yii\helpers\Url;
?>

<div class="formulario">

    <h2>Institute Information</h2>

    <?php $form = ActiveForm::begin(['id'=>'direccion-edit-form', 'action'=>'save-institute']); ?>

    <div class="instituto-fields">
        <div class="col-md-12">
            <?= $form-> field($institutoForm,'nombre')->textInput(['required'=>'true'])?>
        </div>
        <div class="col-md-12">
            <?= $form-> field($institutoForm,'email')->textInput(['type'=>'email','required'=>'true', 'autocomplete'=>false])?>
        </div>
        <div class="col-md-12">
            <?= $form-> field($institutoForm,'password')->textInput(['type'=>'password','required'=>'true'])?>
        </div>
        <div class="col-md-12">
            <?= $form-> field($institutoForm,'telefono')->textInput(['required'=>'true'])?>
        </div>
        <div class="col-md-12">
            <?= $form-> field($institutoForm,'calle')->textInput()?>
        </div>
        <div class="col-md-12">
            <?= $form-> field($institutoForm,'numero_int')->textInput()?>
        </div>
        <div class="col-md-12">
            <?= $form-> field($institutoForm,'numero_ext')->textInput()?>
        </div>
        <div class="col-md-12">
            <?= $form-> field($institutoForm,'colonia')->textInput()?>
        </div>
        <div class="col-md-12">
            <?= $form-> field($institutoForm,'municipio')->textInput()?>
        </div>
        <div class="col-md-12">
            <?= $form-> field($institutoForm,'ciudad')->textInput()?>
        </div>
        <div class="col-md-6">
            <?= $form-> field($institutoForm,'pais')->dropDownList($paises, ['prompt'=>'Select', 'id' => 'paises-drop'])?>
        </div>
        <div class="col-md-6">
            <?php echo $form-> field($institutoForm,'estado')->widget(DepDrop::classname(), [
                'pluginOptions'=>[
                    'depends'=>['paises-drop'],
                    'placeholder'=>'Select...',
                    'url'=>Url::to(['/institutes/subpais'])
                ]
            ]) ?>
        </div>
        <div class="col-md-12">
            <?= $form-> field($institutoForm,'codigo_postal')->textInput()?>
        </div>
        <div class="col-md-12">
            <?= $form-> field($institutoForm,'nombre_contacto')->textInput(['required'=>'true'])?>
        </div>
        <div class="col-md-12">
            <?= $form-> field($institutoForm,'email_contacto')->textInput(['type'=>'email','required'=>'true'])?>
        </div>
        <!-- <div class="col-md-12">
            <?php // echo $form->field($institutoForm,'status') ->dropDownList(['0' => 'Inactivo', '1' => 'Activo'] , ['prompt'=>'Select']);?>
        </div> -->
        <div class="col-md-12">
            <?= $form->field($institutoForm, 'programa')->dropDownList($programas, ['prompt' => 'Select']); ?>
        </div>

        <div class="form-group col-md-12" id="div-guardar">
            <?= Html::submitButton('Save', ['class' => 'btn-oxford boton-peque']) ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>
