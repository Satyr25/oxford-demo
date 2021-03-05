<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
?>

<div class="search-bar-institutes col-md-6" id="search-block">
    <div class="row">
        <div class="col-md-12">
            <input class="typeahead institutes_exam campo-busqueda form-control" type="text" placeholder="Search institute or student" />
        </div>
    </div>
    <div class="row select-year-row">
        <div class="col-md-offset-6 col-md-6">
            <?php $form = ActiveForm::begin([
                'method' => 'get',
                'id' => 'filter-year-form'
            ]); ?>
            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($filtro, 'ciclo_escolar')->dropDownList($ciclos, ['class' => 'select-cycle form-control'])->label(false); ?>
                </div>
                <div class="col-md-6">
                    <button type="submit" class="btn-filter">Filter</button>
                </div>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
