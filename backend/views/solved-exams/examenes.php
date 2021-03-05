<?php
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\widgets\Pjax;
use yii\helpers\Html;

$this->title = 'Solved Exams';
?>

<div class="exams-alumno-solved-exams">
   <section id="inicio-exam-students" class="inicio">
        <div class="container">
            <h2>
                Solved Exams
            </h2>

             <div class="info-alumno">
                <p><span>Name:</span> <?= $alumno->nombre . ' ' . $alumno->apellidos ?>
                <?php  /*?>
                &emsp;<span>Level:</span> <?= $alumno->nivelAlumno->nombre ?>
                 */ ?>
                </p>
            </div>
        </div>
    </section>

    <section id="tabla-examenes-alumno-solved-exams" class="tabla">
        <div class="container">
            <?php // Pjax::begin(['id' => 'pjax-grid-examenes-acad']); ?>
            <?php
            echo GridView::widget([
                'dataProvider' => $dataProvider,
                'columns' => [
                    [
                        'class' => 'yii\grid\SerialColumn',
                    ],
                    [
                        'label' => 'Exam',
                        'attribute' => 'tipoExamen.nombre',
                        'contentOptions' => function ($model) {
                            return ['class' => 'view-student-exam-solved', 'id' => $model->id];
                        }
                    ],
                    [
                        'label' => 'Level',
                        'attribute' => 'examen.nivelAlumno.nombre',
                        'contentOptions' => function ($model) {
                            return ['class' => 'view-student-exam-solved', 'id' => $model->id];
                        }
                    ],
                    [
                        'label' => 'Date',
                        'contentOptions' => function ($model) {
                            return ['class' => 'view-student-exam-solved', 'id' => $model->id];
                        },
                        'value' => function ($model) {
                            if (!$model->fecha_realizacion) {
                                return 'Pending to do';
                            } else {
                                return date('d/m/Y', $model->fecha_realizacion);
                            }
                        },
                    ],
                ],
            ]);
            ?>
        <?php // Pjax::end(); ?>
        </div>
    </section>
</div>
