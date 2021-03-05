<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\jui\DatePicker;

?>

<div class="instituto-search col-md-4" id="search-block">
    <div>
        <?php $form = ActiveForm::begin([
            'action' => ['history'],
            'method' => 'get',
        ]); ?>

        <?= $form->field($filtro, 'code')->textInput(['placeholder' => 'Search', 'class' => 'form-control campo-busqueda'])->label(false) ?>

        <div class="form-group">
            <?= Html::submitButton('Buscar', ['class' => 'btn-buscar']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>
