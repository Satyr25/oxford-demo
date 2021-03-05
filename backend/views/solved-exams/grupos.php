<?php
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\widgets\Pjax;
use yii\helpers\Html;

$this->title = 'Solved Exams';
?>

<div class="grupos-solved-exams">
    <section id="inicio-colegio" class="inicio">
        <div class="container">
            <h2>Solved Exams</h2>
        </div>
    </section>

    <section id="tabla-grupos-solved-exams" class="tabla">
        <div class="container">
            <?php Pjax::begin(['id' => 'pjax-grid-grupos']); ?>
            <?php
            echo GridView::widget([
                'dataProvider' => $dataProvider,
                'columns' => [
                    [
                        'class' => 'yii\grid\SerialColumn',
                    ],
                    [
                        'attribute' => 'grupo',
                        'contentOptions' => function ($model) {
                            return ['class' => 'view-alumnos-solved-exams', 'id' => $model->id];
                        }
                    ],
                    [
                        'attribute' => 'nivel.nombre',
                        'contentOptions' => function ($model) {
                            return ['class' => 'view-alumnos-solved-exams', 'id' => $model->id];
                        }
                    ],
                    [
                        'attribute' => 'created_at',
                        'contentOptions' => function ($model) {
                            return ['class' => 'view-alumnos-solved-exams', 'id' => $model->id];
                        },
                        'format' => 'date'
                    ],
                    [
                        'attribute' => 'updated_at',
                        'contentOptions' => function ($model) {
                            return ['class' => 'view-alumnos-solved-exams', 'id' => $model->id];
                        },
                        'format' => 'date'
                    ],
                ],
            ]);
            ?>
        <?php Pjax::end(); ?>
        </div>
    </section>
</div>
