<?php
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\widgets\Pjax;
use yii\helpers\Html;

$this->title = 'Score Exam';
?>

<div class="score-exam">
    <section id="inicio-score-exam" class="inicio">
        <div class="container">
            <h2>Writing</h2>
        </div>
    </section>

    <section id="add-search-user" class="busqueda">
        <div class="container">
            <div class="col-md-8"></div>

            <?php echo $this->render('_search-exams', ['filtro' => $searchModel]); ?>
        </div>
    </section>

    <section id="tabla-writing" class="tabla">
        <div class="container">
            <?php // Pjax::begin(['id' => 'pjax-grid-institutos']); ?>
            <?php
            echo GridView::widget([
                'dataProvider' => $dataProvider,
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    [
                        'label' => 'Level',
                        'attribute' => 'alumnoExamen.examen.nivelAlumno.nombre',
                        'contentOptions' => function ($model) {
                            return ['class' => 'view-writing-question-v2', 'id' => $model->id];
                        }
                    ],
                    [
                        'label' => 'Exam',
                        'attribute' => 'alumnoExamen.examen.tipoExamen.nombre',
                        'contentOptions' => function ($model) {
                            return ['class' => 'view-writing-question-v2', 'id' => $model->id];
                        },
                        'value' => function($model){
                            $tipo_examen = $model->alumnoExamen->examen->tipoExamen->nombre;
                            if($model->alumnoExamen->examen->tipoExamen->clave == 'DIA' && $model->alumnoExamen->examen->diagnostic_v2 == 1){
                                $tipo_examen .= ' V2';
                            }else if($model->alumnoExamen->examen->tipoExamen->clave == 'DIA' && $model->alumnoExamen->examen->diagnostic_v3 == 1){
                                $tipo_examen .= ' V3';
                            }else if($model->alumnoExamen->examen->tipoExamen->clave == 'CER' && $model->alumnoExamen->examen->certificate_v2 == 1){
                                $tipo_examen .= ' V2';
                            }
                            return $tipo_examen;
                        }
                    ],
                    [
                        'label' => 'Title',
                        'attribute' => 'reactivo.pregunta',
                        'contentOptions' => function ($model) {
                            return ['class' => 'view-writing-question-v2', 'id' => $model->id];
                        }
                    ],
                    [
                        'label' => 'Points',
                        'attribute' => 'reactivo.puntos',
                        'contentOptions' => function ($model) {
                            return ['class' => 'view-writing-question-v2', 'id' => $model->id];
                        }
                    ],
                    [
                        'label' => 'Student Code',
                        'value' => function ($model) {
                            $user = $model->alumnoExamen->alumno->users[0];
                            return $user->codigo;
                        },
                        'contentOptions' => function ($model) {
                            return ['class' => 'view-writing-question-v2', 'id' => $model->id];
                        }
                    ],
                    [
                        'label' => 'Date',
                        'attribute' => 'alumnoExamen.fecha_realizacion',
                        'format' => 'date'
                    ]
                ],
            ]);
            ?>
        <?php // Pjax::end(); ?>
        </div>
    </section>


</div>
