<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
?>
<?php $form = ActiveForm::begin([]); ?>

    <div class="row">
        <div>
            <?= $form->field($reactivoForm, 'instrucciones')->textArea() ?>
        </div>
        <div>
            <?= $form->field($reactivoForm, 'pregunta')->textArea() ?>
        </div>
    </div>

    <div class="form-group col-md-12" id="div-guardar">
        <?= Html::submitButton('Save', ['class' => 'btn-oxford boton-peque']) ?>
    </div>

<?php ActiveForm::end(); ?>
