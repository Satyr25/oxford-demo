<?php

use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\widgets\Pjax;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$this->title = 'Impresión';
?>
<div class="impresion">
    <section class="inicio">
        <div class="container">
            <h2>Impresiones</h2>
        </div>
    </section>

    <div class="busqueda">
        <div class="container">
            <div class="col-md-6">
            </div>
            <div class="search-bar-institutes col-md-6" id="search-block">
                <div class="row">
                    <div class="col-md-12">
                        <input class="typeahead busqueda-impresion campo-busqueda form-control" type="text" placeholder="Buscar" />
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
                                <?= $form->field($searchModel, 'ciclo_escolar')->dropDownList($ciclos, ['class' => 'select-cycle form-control'])->label(false); ?>
                            </div>
                            <div class="col-md-6">
                                <button type="submit" class="btn-filter">Filter</button>
                            </div>
                        </div>
                        <?php ActiveForm::end(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <section class="tabla tabla-impresion">
        <div class="container">
            <?php Pjax::begin(['id' => 'pjax-grid-institutos']); ?>
            <?php
            echo GridView::widget([
                'dataProvider' => $dataProvider,
                'columns' => [
                    [
                        'attribute' => 'nombre',
                        'value' => function ($model) use ($searchModel) {
                            return HTML::a($model->nombre, Url::to(['impresion/listado', 'entidad' => 'INS', 'id' => $model->id, 'ciclo' => $searchModel->ciclo_escolar]));
                        },
                        'format' => 'raw'
                    ]
                ],
            ]);
            ?>
            <?php echo Html::endForm(); ?>
        </div>
    </section>
</div>
<input type="hidden" class="value-ciclo" value="<?= $searchModel->ciclo_escolar ?>">