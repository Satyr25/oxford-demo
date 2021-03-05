<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
?>

<div class="search-bar-institutes col-md-6" id="search-block">
     <input class="typeahead institutes campo-busqueda form-control" type="text" placeholder="Search institute or student">
    <!-- <div>
        <?php
        // $form = ActiveForm::begin([
        //     'action' => ['index'],
        //     'method' => 'get',
        // ]);
        ?>

        <?php // echo $form->field($filtro, 'nombre')->textInput(['placeholder'=>'Search', 'class'=>'form-control campo-busqueda'])->label(false) ?>

        <div class="form-group">
            <?php // echo Html::submitButton('Buscar', ['class' => 'btn-buscar']) ?>
        </div>

        <?php // ActiveForm::end(); ?>
    </div> -->
    <div class="row">
        <div class="col-md-12">
            <div class="botones-busqueda">
                <a href="<?= Url::to(['institutes/institutes-export', 'ciclo_escolar' => $filtro->ciclo_escolar]) ?>" class="acciones-busqueda" id="link-export">Export</a>
                <a href="<?= Url::to(['institutes/inactive-institutes']) ?>" class="acciones-busqueda">Inactive Institutes</a>
                <a href="<?= Url::to(['summary/index']) ?>" class="acciones-busqueda" id="link-summary">Summary</a>
                <a href="javascript:;" class="acciones-busqueda" id="delete-institutes">Delete</a>
                <a href="javascript:;" class="acciones-busqueda" id="cancel-institutes">Cancel</a>
            </div>
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
                        <?= $form->field($filtro, 'ciclo_escolar')->dropDownList($ciclos, ['class' => 'select-cycle'])->label(false); ?>
                    </div>
                    <div class="col-md-6">
                        <button type="submit" class="btn-filter">Filter</button>
                    </div>
                </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
