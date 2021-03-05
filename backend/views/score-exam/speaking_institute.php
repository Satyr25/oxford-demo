<?php
use kartik\select2\Select2;
use yii\helpers\Html;
?>

<div class="institute-speaking">
    <div class="container">
        <p class="title">Score Speaking</p>
        <div class="form-institute-speaking">
            <?= Html::beginForm(['score-exam/set-institute-speaking'], 'post') ?>
                <div class="row main-select">
                    <div class="col-md-6">
                        <div class="form-group">
                            <?= Select2::widget([
                                'name' => 'institute',
                                'data' => $institutes,
                                'options' => [
                                    'placeholder' => 'Select institute',
                                ],
                            ]) ?>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                           <?=Html::dropDownList("ciclo","",$ciclos, ['class' => 'select-cycle2'])?>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button class="btn-oxford boton-peque" type="submit">Continue</button>
                    </div>
                </div>
            <?= Html::endForm() ?>
        </div>
    </div>
</div>