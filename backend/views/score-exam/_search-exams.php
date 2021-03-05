<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
?>

<div class="instituto-search col-md-4" id="search-block">
    <div>
        <?php $form = ActiveForm::begin([
            // 'action' => ['index'],
            'method' => 'get',
        ]); ?>

        <?= $form->field($filtro, 'nombre')->textInput(['placeholder' => 'Search', 'class' => 'form-control campo-busqueda'])->label(false) ?>
        <?= $form->field($filtro, 'fecha')->input('date')->label(false) ?>

        <div class="form-group">
            <?= Html::submitButton('Buscar', ['class' => 'btn-buscar']) ?>
        </div>

        <?php ActiveForm::end(); ?>
        <div class="botones-busqueda">
            <?php if ($this->context->action->id == 'index-v2'){ ?> 
                <a href="<?= Url::to(['score-exam/history-v2'])?>" class="acciones-busqueda">Graded Exams</a>
            <?php } else { ?> 
                <a href="<?= Url::to(['score-exam/history'])?>" class="acciones-busqueda">Graded Exams</a>
            <?php } ?>
        </div>
    </div>
</div>
