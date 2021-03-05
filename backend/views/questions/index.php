<?php
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\widgets\Pjax;
use yii\helpers\Html;

$this->title = 'Questions';
?>

<div class="questions">
    <section id="inicio-question" class="inicio">
        <div class="container">
            <h2>Questions</h2>
        </div>
    </section>

    <section id="search-questions" class="busqueda">
        <div class="container">
            <div class="col-md-8">
                <a href="javascript:;" class="boton-add question">+ Add Question</a>
            </div>

            <?php echo $this->render('_search-question', [
                'filtro' => $searchModel,
                'secciones' => $secciones,
                'level' => $level,
                'version' => $version,
                'exam_type' => $exam_type
            ]); ?>
        </div>
    </section>

    <section id="tabla-questions" class="tabla">
        <div class="container">
            <?php echo Html::beginForm(['questions/delete-multiple'], 'post', ['id' => 'preguntas-tabla-form']); ?>
            <?php // Pjax::begin(['id' => 'pjax-grid-institutos']); ?>
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
                    ['class' => 'yii\grid\SerialColumn'],
                    [
                        'label' => 'Level',
                        'attribute' => 'seccion.examen.nivelAlumno.nombre',
                        'contentOptions' => function ($model) {
                            return ['class' => 'view-reactivo', 'id' => $model->id];
                        }
                    ],
                    [
                        'label' => 'Exam',
                        'attribute' => 'seccion.examen.tipoExamen.nombre',
                        'contentOptions' => function ($model) {
                            return ['class' => 'view-reactivo', 'id' => $model->id];
                        },
                        'value' => function($model) {
                            $tipo = $model->seccion->examen->tipoExamen->nombre;
                            if($model->seccion->examen->tipoExamen->clave == 'DIA' && $model->seccion->examen->diagnostic_v2 == 1) {
                                $tipo .= ' V2';
                            }else if($model->seccion->examen->tipoExamen->clave == 'DIA' && $model->seccion->examen->diagnostic_v3 == 1) {
                                $tipo .= ' V3';
                            }else if($model->seccion->examen->tipoExamen->clave == 'CER' && $model->seccion->examen->certificate_v2 == 1) {
                                $tipo .= ' V2';
                            }
                            return $tipo;
                        }
                    ],
                    [
                        'label' => 'Version',
                        'attribute' => 'seccion.examen.variante.nombre',
                        'contentOptions' => function ($model) {
                            return ['class' => 'view-reactivo', 'id' => $model->id];
                        }
                    ],
                    [
                        'label' => 'Section',
                        'attribute' => 'seccion.tipoSeccion.nombre',
                        'contentOptions' => function ($model) {
                            return ['class' => 'view-reactivo', 'id' => $model->id];
                        }
                    ],
                    [
                        'label' => 'Question',
                        'attribute' => 'pregunta',
                        'contentOptions' => function ($model) {
                            return ['class' => 'view-reactivo', 'id' => $model->id];
                        }
                    ],
                    [
                        'label' => 'Type of Question',
                        'attribute' => 'tipoReactivo.nombre',
                        'contentOptions' => function ($model) {
                            return ['class' => 'view-reactivo', 'id' => $model->id];
                        }
                    ],
                    [
                        'label' => "Points",
                        'attribute' => 'puntos',
                        'contentOptions' => function ($model) {
                            return ['class' => 'view-reactivo', 'id' => $model->id];
                        }
                    ],
                    [
                        'label' => 'Author',
                        'attribute' => 'user.academico.nombre',
                        'contentOptions' => function ($model) {
                            return ['class' => 'view-reactivo', 'id' => $model->id];
                        }
                    ],
                ],
            ]);
            ?>
        <?php // Pjax::end(); ?>
        <?php echo Html::endForm(); ?>
        </div>
    </section>

    <div id="popup-question" class="white-popup mfp-hide">
        <div class="contenido"></div>
    </div>

</div>
