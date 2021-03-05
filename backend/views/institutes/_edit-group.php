<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;
?>

<div class='edit-info oculto'>
    <div>
        <?php $form = ActiveForm::begin(['id' => 'edit-grupo-form', 'action' => 'edit-group']) ?>
        <?= Html::hiddenInput('GrupoForm[id]', $grupo->id); ?>
        <div class="half-div tabla-datos">
            <div class="separa-datos">
                <p class="nombre-dato">Institute Name:</p><p><?php echo $grupo->instituto->nombre ?></p>
            </div>
            <div class="separa-datos">
                <p class="nombre-dato">Group:</p><?php echo $form->field($grupoForm, 'nombre')->textInput(['required' => 'true'])->label(false); ?>
        </div>
        <div class="separa-datos">
            <p class="nombre-dato">Level:</p><?php echo $form->field($grupoForm, 'nivel')->dropDownList($niveles, ['prompt' => 'Select'])->label(false); ?>
        </div>
        </div><!--
        --><div class="half-div tabla-datos">
            <div class="separa-datos">
                <p class="nombre-dato">Students No.:</p><p><?php echo count($grupo->alumnos) ?></p>
            </div>
            <div class="separa-datos">
                <p class="nombre-dato">Ciclo escolar:</p><p><?= $form->field($grupoForm, 'ciclo_escolar')->dropDownList($ciclos)->label(false); ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-12 row" id="div-guardar">
        <?= Html::submitButton('Save', ['class' => 'btn-oxford boton-peque']) ?>
        <?php ActiveForm::end() ?>
    </div>
</div>
