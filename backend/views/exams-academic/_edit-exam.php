<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
?>

<div class="formulario">
    <?php /* ?>
    <?php $form = ActiveForm::begin(['id'=>'exam-edit-form']); ?>
    <div class="exam-edit-fields">
    <?php //echo Html::hiddenInput('examenForm[id]', $id); ?>
        <div class="container">
            <p class="col-md-6 nombre-campo">Test name:</p>
            <p class="col-md-6"><input type="text" name="" id="" class="form-control"><?php //echo $form-> field($examForm,'nombre')->textInput(['required'=>'true'])?></p>
            <p class="col-md-6 nombre-campo">Description:</p>
            <p class="col-md-6"><input type="text" name="" id="" class="form-control"><?php //echo $form-> field($examForm,'nombre')->textInput(['required'=>'true'])?></p>
            <p class="col-md-6 nombre-campo">Test Passing Criteria Percentage:</p>
            <p class="col-md-6"><input type="text" name="" id="" class="form-control"><?php //echo $form-> field($examForm,'nombre')->textInput(['required'=>'true'])?></p>
            <p class="col-md-6 nombre-campo">Select Section:</p>
            <p class="col-md-6"><input type="text" name="" id="" class="form-control"><?php //echo $form-> field($examForm,'nombre')->textInput(['required'=>'true'])?></p>
            <p class="col-md-6 nombre-campo">Select Duration:</p>
            <p class="col-md-6"><input type="text" name="" id="" class="form-control"><?php //echo $form-> field($examForm,'nombre')->textInput(['required'=>'true'])?></p>
            <p class="col-md-6 nombre-campo">Unique Points Available in Sections:</p>
            <p class="col-md-6"><input type="text" name="" id="" class="form-control"><?php //echo $form-> field($examForm,'nombre')->textInput(['required'=>'true'])?></p>
            <p class="col-md-6 nombre-campo">Total Section Points:</p>
            <p class="col-md-6"><input type="text" name="" id="" class="form-control"><?php //echo $form-> field($examForm,'nombre')->textInput(['required'=>'true'])?></p>
        </div>

        <div class="container horizontal-separator"></div>
        <div class="container boton">
            <div class="col-md-12">
                <a href="javascript:;" class="btn-oxford boton-peque" id="add-section">Add more section</a>
            </div>
        </div>
        <div class="container">
            <div class="add-sections">
                <p class="col-md-6 nombre-campo">How many breaks:</p>
                <p class="col-md-6"><input type="text" name="" id="" class="form-control"><?php //echo $form-> field($examForm,'nombre')->textInput(['required'=>'true'])?></p>
                <p class="col-md-6 nombre-campo">Total Duration:</p>
                <p class="col-md-6"><input type="text" name="" id="" class="form-control"><?php //echo $form-> field($examForm,'nombre')->textInput(['required'=>'true'])?></p>
                <p class="col-md-6 nombre-campo">Total Points:</p>
                <p class="col-md-6"><input type="text" name="" id="" class="form-control"><?php //echo $form-> field($examForm,'nombre')->textInput(['required'=>'true'])?></p>
                <p class="col-md-6 nombre-campo">Price:</p>
                <p class="col-md-6"><input type="text" name="" id="" class="form-control"><?php //echo $form-> field($examForm,'nombre')->textInput(['required'=>'true'])?></p>
                <p class="col-md-6 nombre-campo">Status:</p>
                <p class="col-md-6"><input type="text" name="" id="" class="form-control"><?php //echo $form-> field($examForm,'nombre')->textInput(['required'=>'true'])?></p>
                <div class="col-md-12 horizontal-separator"></div>
            </div>
        </div>

        <div class="form-group">
            <?php echo Html::a('Save','javascript:;' ,['class' => 'btn-oxford','id' => 'boton-guardar-examen']) ?>
        </div>

    </div>
    <?php ActiveForm::end(); ?>
    */ ?>

    <?php $form = ActiveForm::begin([]); ?>
    <div class="instituto-fields">
        <?= $form->field($examForm, 'id')->hiddenInput()->label(false) ?>
        <div class="col-md-12">
            <?= $form->field($examForm, 'tipo')->dropDownList($tipos,['prompt'=>'Select']) ?>
        </div>
        <div class="col-md-12" id="diagnostic-version" style="display:<?= $examForm->tipo == '1' ? 'block' : 'none' ?>;">
            <?= $form->field($examForm, 'diagnostic_v2')->radioList( [0=>'V1', 1 => 'V2', 2 => 'V3'] ) ?>
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
        <div id="bloque-duraciones" class="row">
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
        <div class="col-md-12">
            <?php echo $form->field($examForm, 'status')->dropDownList(['0'=>'Inactive', '1'=>'Active'], ['prompt'=>'Select']) ?>
        </div>
        <div class="form-group col-md-12  row" id="div-guardar">
            <?= Html::submitButton('Save', ['class' => 'btn-oxford boton-peque']) ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>

</div>
