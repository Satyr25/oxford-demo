<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
?>

<div class="instituto-search col-md-4" id="search-block">
    <div>
        <?php $form = ActiveForm::begin([
            'method' => 'get',
            'action' => Url::to(['score-exam/speaking'])
        ]); ?>

        <?= $form->field($filtro, 'nombre')->textInput(['placeholder' => 'Search', 'class' => 'form-control campo-busqueda'])->label(false) ?>
        <?= $form->field($filtro, 'fecha')->input('date')->label(false) ?>

        <div class="form-group">
            <?= Html::submitButton('Buscar', ['class' => 'btn-buscar']) ?>
        </div>

        <?php ActiveForm::end(); ?>
        <div class="botones-busqueda">
            <a href="<?= Url::to(['score-exam/speaking-history'])?>" class="acciones-busqueda">Graded Exams</a>
        </div>
    </div>
</div>
