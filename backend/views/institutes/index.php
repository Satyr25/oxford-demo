<?php
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Institutes';
?>

<div class="institutes">
    <section id="inicio-institutes" class="inicio">
        <div class="container">
            <?php if (isset($isSummary) && $isSummary): ?>
            <h2><?= $typeInstitutes ?> Institutes - Summary: <?= "{$examType} - {$schoolYear} - {$programs}" ?></h2>
            <?php else: ?>
            <h2>Institutes</h2>
            <?php endif; ?>
        </div>
    </section>

    <section id="add-search-user" class="busqueda">
        <div class="container">
            <div class="col-md-6">
                <a href="<?= Url::to(['institutes/add-institute']) ?>" class="boton-add institute">+ Add Institute</a>
            </div>

            <?php echo $this->render('_search-institute', ['filtro' => $searchModel, 'ciclos' => $ciclos]); ?>
        </div>
    </section>
    
    <section id="tabla-instituto" class="tabla">
        <div class="container">
            <?php echo Html::beginForm(['institutes/delete-multiple-institutes'], 'post', ['id' => 'institutes-table-form']);
            ?>
                <?php Pjax::begin(['id' => 'pjax-grid-institutos']); ?>
                <?php
                echo GridView::widget([
                'dataProvider' => $dataProvider,
                'columns' => [
                    [
                        'class' => 'yii\grid\CheckboxColumn',
                        'checkboxOptions' => function ($model) {
                            return ['value' => $model->id];
                        },
                    ],
                    [
                        'class' => 'yii\grid\SerialColumn',
                    ],
                    [
                        'attribute'=>'nombre',
                        'contentOptions'=>function($model) use ($searchModel) {
                            return ['class'=>'view-colegio', 'data' => ['ciclo' => $searchModel->ciclo_escolar, 'id' => $model->id]];
                        }
                    ],
                    [
                        'attribute'=>'email',
                        'contentOptions'=>function($model) use ($searchModel) {
                            return ['class'=>'view-colegio', 'data' => ['ciclo' => $searchModel->ciclo_escolar, 'id' => $model->id]];
                        }
                    ],
                    [
                        'label'=>"Country",
                        'attribute'=>'direccion.pais.nombre',
                        'contentOptions' => function ($model) use ($searchModel)  {
                            return ['class' => 'view-colegio', 'data' => ['ciclo' => $searchModel->ciclo_escolar, 'id' => $model->id]];
                        }
                    ],
                    [
                        'label'=>'Program',
                        'attribute'=>'programa.nombre',
                        'contentOptions' => function($model) use ($searchModel) {
                            return ['class'=>'view-colegio', 'data' => ['ciclo' => $searchModel->ciclo_escolar, 'id' => $model->id]];
                        }
                    ],
                    [
                        'label' => 'Round',
                        'attribute'=>'ronda',
                        'contentOptions' => function($model) use ($searchModel) {
                            return ['class'=>'view-colegio', 'data' => ['ciclo' => $searchModel->ciclo_escolar, 'id' => $model->id]];
                        }
                    ],
                    [
                        'label' => 'Total Students',
                        'attribute'=>'alumnos',
                        'value' => function ($model) {
                            return $model->totalAlumnos();
                        },
                    ],
                    [
                        'attribute'=>'updated_at',
                        'format'=> 'date',
                        'contentOptions'=>function($model) use ($searchModel) {
                            return ['class'=>'view-colegio', 'data' => ['ciclo' => $searchModel->ciclo_escolar, 'id' => $model->id]];
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
            <?= Html::hiddenInput('action', null, ['id' => 'table-form-action']) ?>
            <?php Pjax::end(); ?>
        <?php echo Html::endForm(); ?>
        </div>
    </section>
</div>

<input type="hidden" class="value-ciclo" value="<?= $searchModel->ciclo_escolar ?>">
