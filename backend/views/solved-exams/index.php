<?php

use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\widgets\Pjax;
use yii\helpers\Html;
use app\models\Paises;

$this->title = 'Solved Exams';
?>

<div class="institutes">
    <section id="inicio-institutes" class="inicio">
        <div class="container">
            <h2>Solved Exams</h2>
        </div>
    </section>

    <section id="add-search-user" class="busqueda">
        <div class="container">
            <div class="col-md-6">
            </div>

            <?php echo $this->render('_search-institute', ['filtro' => $searchModel, 'ciclos' => $ciclos]); ?>
        </div>
    </section>

    <section id="tabla-ins-solved-exams" class="tabla">
        <div class="container">
            <?php Pjax::begin(['id' => 'pjax-grid-institutos']); ?>
            <?php
            echo GridView::widget([
                'dataProvider' => $dataProvider,
                'columns' => [
                    [
                        'class' => 'yii\grid\SerialColumn',
                    ],
                    [
                        'attribute' => 'nombre',
                        'contentOptions' => function ($model) use ($searchModel) {
                            return ['class' => 'view-grupos-solved-exams', 'id' => $model->id, 'data' => ['ciclo' => $searchModel->ciclo_escolar]];
                        }
                    ],
                    [
                        'attribute' => 'email',
                        'contentOptions' => function ($model) use ($searchModel) {
                            return ['class' => 'view-grupos-solved-exams', 'id' => $model->id, 'data' => ['ciclo' => $searchModel->ciclo_escolar]];
                        }
                    ],
                    [
                        'attribute' => 'telefono',
                        'contentOptions' => function ($model) use ($searchModel) {
                            return ['class' => 'view-grupos-solved-exams', 'id' => $model->id, 'data' => ['ciclo' => $searchModel->ciclo_escolar]];
                        }
                    ],
                ],
            ]);
            ?>
            <?php echo Html::endForm(); ?>
        </div>
    </section>
</div>
<input type="hidden" class="value-ciclo" value="<?= $searchModel->ciclo_escolar ?>">