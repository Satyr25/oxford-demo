<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use backend\models\forms\WritingScoreForm;

$writingScoreForm = new WritingScoreForm();
?>

<div class="form-save-points">
    <?php $form = ActiveForm::begin(['id' => 'save-writing-points-form', 'action'=>'grade-exam']); ?>
    <div class="row">
        <div class="col-md-3">
            <?= Html::hiddenInput('WritingScoreForm[id]', $id); ?>
            <div id="bloque-calificacion">
                <?= $form->field($writingScoreForm, 'puntos')->textInput(['required' => 'true'])->label(false) ?>
                <div id="limite"> / 100</div>
                <div class="clear"></div>
            </div>
        </div>
         <div class="col-md-3">
            <?php echo Html::a('Grade Exam', 'javascript:;', ['class' => 'btn-oxford boton-peque', 'id' => 'boton-grade']) ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>
