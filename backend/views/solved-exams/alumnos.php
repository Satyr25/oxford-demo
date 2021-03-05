<?php
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\widgets\Pjax;
use yii\helpers\Html;
use app\models\Examen;

$this->title = 'Solved Exams';
?>

<div class="alumnos-solved-exams">
    <section id="inicio-grupo" class="inicio">
        <div class="container">
            <h2>Solved Exams</h2>
        </div>
    </section>

    <section id="tabla-alumnos-solved-exams" class="tabla">
        <div class="container">
                <?php Pjax::begin(['id' => 'pjax-grid-alumno']); ?>
                <?php
                echo GridView::widget([
                    'dataProvider' => $dataProvider,
                    'columns' => [
                        ['class' => 'yii\grid\SerialColumn'],
                        [
                            'attribute' => 'nombre',
                            'contentOptions' => function ($model) {
                                return ['class' => 'view-exams-solved-exams', 'id' => $model->id];
                            }
                        ],
                        [
                            'attribute' => 'apellidos',
                            'contentOptions' => function ($model) {
                                return ['class' => 'view-exams-solved-exams', 'id' => $model->id];
                            }
                        ],
                        [
                            'label' => 'Level',
                            'attribute' => 'nivelAlumno.nombre',
                            'contentOptions' => function ($model) {
                                return ['class' => 'view-exams-solved-exams', 'id' => $model->id];
                            }
                        ],
                    ],
                ]);
                ?>
            <?php Pjax::end(); ?>
        </div>
    </section>
</div>
