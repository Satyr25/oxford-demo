<?php

use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\widgets\Pjax;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Institutes';
?>

<div class="inactive-institutes-pg">
    <section id="inicio-institutes" class="inicio">
        <div class="container">
            <h2>Inactive Institutes</h2>
        </div>
    </section>

    <?php Pjax::begin(['id' => 'pjax-grid-institutos']) ?>
    <section id="tabla-institutos-inactivos" class="tabla container">
        <?=
            GridView::widget([
                'dataProvider' => $dataProviderInactive,
                'columns' => [
                    [
                        'class' => 'yii\grid\SerialColumn',
                    ],
                    [
                        'attribute' => 'nombre',
                        'contentOptions' => function ($model) use ($searchModel) {
                            return ['class' => 'view-colegio', 'data' => ['ciclo' => $searchModel->ciclo_escolar, 'id' => $model->id]];
                        }
                    ],
                    [
                        'attribute' => 'email',
                        'contentOptions' => function ($model) use ($searchModel) {
                            return ['class' => 'view-colegio', 'data' => ['ciclo' => $searchModel->ciclo_escolar, 'id' => $model->id]];
                        }
                    ],
                    [
                        'label' => "Country",
                        'attribute' => 'direccion.pais.nombre',
                        'contentOptions' => function ($model) use ($searchModel) {
                            return ['class' => 'view-colegio', 'data' => ['ciclo' => $searchModel->ciclo_escolar, 'id' => $model->id]];
                        }
                    ],
                    [
                        'label' => 'Program',
                        'attribute' => 'programa.nombre',
                        'contentOptions' => function ($model) use ($searchModel) {
                            return ['class' => 'view-colegio', 'data' => ['ciclo' => $searchModel->ciclo_escolar, 'id' => $model->id]];
                        }
                    ],
                    [
                        'attribute' => 'updated_at',
                        'format' => 'date',
                        'contentOptions' => function ($model) use ($searchModel) {
                            return ['class' => 'view-colegio', 'data' => ['ciclo' => $searchModel->ciclo_escolar, 'id' => $model->id]];
                        },
                    ],
                    [
                        'attribute' => 'status',
                        'value' => function ($model) {
                            return Html::activeDropdownList($model, 'status', ['0' => 'Inactivo', '1' => 'Activo', '2' => 'Cancelado'], ['class' => 'status-dropdown', 'id' => $model->id]);
                        },
                        'format' => 'raw',
                    ],
                ],
            ]);
        ?>
    </section>
    <section class="inicio">
        <div class="container">
            <h2>Cancelled Institutes</h2>
        </div>
    </section>
    <section id="tabla-institutos-cancelados" class="tabla container">
        <?=
            GridView::widget([
                'dataProvider' => $dataProviderCancelled,
                'columns' => [
                    [
                        'class' => 'yii\grid\SerialColumn',
                    ],
                    [
                        'attribute' => 'nombre',
                        'contentOptions' => function ($model) use ($searchModel) {
                            return ['class' => 'view-colegio', 'data' => ['ciclo' => $searchModel->ciclo_escolar, 'id' => $model->id]];
                        }
                    ],
                    [
                        'attribute' => 'email',
                        'contentOptions' => function ($model) use ($searchModel) {
                            return ['class' => 'view-colegio', 'data' => ['ciclo' => $searchModel->ciclo_escolar, 'id' => $model->id]];
                        }
                    ],
                    [
                        'label' => "Country",
                        'attribute' => 'direccion.pais.nombre',
                        'contentOptions' => function ($model) use ($searchModel) {
                            return ['class' => 'view-colegio', 'data' => ['ciclo' => $searchModel->ciclo_escolar, 'id' => $model->id]];
                        }
                    ],
                    [
                        'label' => 'Program',
                        'attribute' => 'programa.nombre',
                        'contentOptions' => function ($model) use ($searchModel) {
                            return ['class' => 'view-colegio', 'data' => ['ciclo' => $searchModel->ciclo_escolar, 'id' => $model->id]];
                        }
                    ],
                    [
                        'attribute' => 'updated_at',
                        'format' => 'date',
                        'contentOptions' => function ($model) use ($searchModel) {
                            return ['class' => 'view-colegio', 'data' => ['ciclo' => $searchModel->ciclo_escolar, 'id' => $model->id]];
                        },
                    ],
                    [
                        'attribute' => 'status',
                        'value' => function ($model) {
                            return Html::activeDropdownList($model, 'status', ['0' => 'Inactivo', '1' => 'Activo', '2' => 'Cancelado'], ['class' => 'status-dropdown', 'id' => $model->id]);
                        },
                        'format' => 'raw',
                    ],
                ],
            ]);
        ?>
    </section>
    <?php Pjax::end() ?>
</div>