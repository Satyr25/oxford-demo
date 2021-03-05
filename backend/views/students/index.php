<?php
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use backend\assets\VideoAsset;

VideoAsset::register($this);
$this->title = 'Students';
?>

<div class="exams-students container">
   <section id="inicio-exam-students" class="inicio">
        <div class="container">
            <h2>General Information</h2>
             <div class="info-alumno">
                <p>
                    <span>Name:</span> <?= "{$alumno->nombre} {$alumno->apellidos}" ?>
                </p>
            </div>
        </div>
    </section>
    <section id="tabla-examenes-students" class="tabla">
        <div>
            <h2>Pending Tests</h2>
            <?php
            echo GridView::widget([
                'dataProvider' => $dataProvider,
                'columns' => [
                    [
                        'class' => 'yii\grid\SerialColumn',
                    ],
                    [
                        'format' => 'html',
                        'value' => function ($model) {
                            return $model->tipoExamen->nombre;
                        },
                    ],
                    [
                        'format' => 'raw',
                        'value' => function ($model) {
                             return Html::button('Start',
                                 [
                                     'class' => 'btn-oxford boton-peque btn-start-exam',
                                     'data' => [
                                         'url' => Url::to(['students/solve-exam', 'id' => $model->id]),
                                         'exam-id' => $model->tipo_examen_id
                                     ]
                                 ]
                             );
//                            return Html::a('Start',
//                                ['students/solve-exam', 'id' => $model->id],
//                                [
//                                    'class' => 'btn-oxford boton-peque',
//                                    'data' => [
//                                        'url' => Url::to(['students/solve-exam', 'id' => $model->id])
//                                    ]
//                                ]
//                            );
                        },
                    ],
                ],
                'options' => ['class' => 'tests-student'],
            ]);
            ?>
        </div>
    </section>

    <section id="tabla-examenes-students-pending-done" class="tabla">
        <div class="">
            <h2>Completed Tests</h2>
            <?= GridView::widget([
                'dataProvider' => $dataProviderDone,
                'columns' => [
                    [
                        'class' => 'yii\grid\SerialColumn',
                    ],
                    [
                        'label' => 'Exam',
                        'attribute' => 'tipoExamen.nombre',
                        'contentOptions' => function ($model) {
                            return ['class' => 'view-student-exam', 'id' => $model->id];
                        }
                    ],
                    [
                        'label' => 'Date',
                        'contentOptions' => function ($model) {
                            return ['class' => 'view-student-exam', 'id' => $model->id];
                        },
                        'value' => function ($model) {
                            if(!$model->fecha_realizacion)
                            {
                                return 'Pending to do';
                            }
                            else{
                                return date('d/m/Y' ,$model->fecha_realizacion);
                            }
                        },
                    ],
                ],
                'options' => ['class' => 'tests-student'],
            ]);
            ?>
        </div>
    </section>
    <div class="clear"></div>
</div>
<div class="mfp-hide" id="student-tutorial-popup">
    <div class="video-language-selection">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12 text-center">
                    <h2>You are about to watch a video showing you how to use the platform. Select the language for the video:</h2>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <button type="button" class="video-select center-block btn-oxford" data-language="es">Spanish</button>
                </div>
                <div class="col-md-6">
                    <button type="button" class="video-select center-block btn-oxford" data-language="en">English</button>
                </div>
            </div>
        </div>
    </div>
    <div class="video-container es hidden">
        <video
        id="tutorial-en"
        class="video-js vjs-fluid"
        controls
        preload="auto"
        >
            <source src="<?= Url::to('@web/video/exam_tut_es.mp4') ?>" type="video/mp4">
        </video>
    </div>
    <div class="video-container en hidden">
        <video
        id="tutorial-es"
        class="video-js vjs-fluid"
        controls
        preload="auto"
        >
            <source src="<?= Url::to('@web/video/exam_tut_en.mp4') ?>" type="video/mp4">
        </video>
    </div>
    <div class="exam-link-container text-center hidden">
        <?php if ($alumno->certificate_v2){ ?>
           <p>This exam will have two Listening exercises, two Reading exercises, 20 Use of English questions and two Writing exercises. The timer shows how long you have left per exercise.</p>
        <?php } ?>
        <a href="" id="exam-link" class="btn-oxford">Start</a>
    </div>
</div>
