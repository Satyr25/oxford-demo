    <?php
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\widgets\Pjax;
use yii\helpers\Html;
use yii\helpers\Url;
use app\models\Paises;

$this->title = 'Exams';
?>

<div class="see-exams">
   <section id="inicio-exam" class="inicio">
        <div class="container">
            <h2>
                <?php
                switch ($tipo) {
                    case 'DIA':
                        echo "Diagnostic";
                        break;
                    case 'MOC':
                        echo 'Mock';
                        break;
                    case 'CER':
                        echo 'Certificate';
                        break;
                    default:
                        echo 'Exams';
                        break;
                }
                ?>
            </h2>
        </div>
    </section>

    <section id="search-exam" class="busqueda">
        <div class="container">
            <div>
                <a href="add-exam-form" class="boton-add exam">+ Add exam</a>
                /
                <div class="dropdown" style="display:inline;">
                    <a href="#" class="dropdown-toggle acciones-busqueda boton-add" data-toggle="dropdown">Reports</a>
                    <ul class="dropdown-menu">
                        <li>
                            <a class="dropdown-item" href="<?php echo Url::to(['academic-reports/index', 'tipo' => 'DIA']); ?>">
                                Diagnostic
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="<?php echo Url::to(['academic-reports/index', 'tipo' => 'DIAV2']); ?>">
                                Diagnostic V2
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="<?php echo Url::to(['academic-reports/index', 'tipo' => 'DIAV3']); ?>">
                                Diagnostic V3
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="<?php echo Url::to(['academic-reports/index', 'tipo' => 'MOC']); ?>">
                                Mock
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="<?php echo Url::to(['academic-reports/index', 'tipo' => 'CER']); ?>">
                                Certificate
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="<?php echo Url::to(['academic-reports/index', 'tipo' => 'CERV2']); ?>">
                                Certificate V2
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="lds-dual-ring exportar oculto"></div>
        </div>
    </section>

    <section id="tabla-examenes-acad" class="tabla">
        <div class="container">
            <?php echo Html::beginForm(['exams-academic/delete-multiple'],'post');?>
                <?php Pjax::begin(['id' => 'pjax-grid-examenes-acad']); ?>
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
                            'label'=>'Level',
                            'attribute'=>'nivelAlumno.nombre',
                            'contentOptions' => function ($model) {
                                return ['class' => 'view-acad-exam', 'id' => $model->id];
                            }
                        ],
                        [
                            'label'=>'Exam',
                            'attribute'=>'tipoExamen.nombre',
                            'contentOptions' => function ($model) {
                                return ['class' => 'view-acad-exam', 'id' => $model->id];
                            },
                            'value' => function($model){
                                $tipo = $model->tipoExamen->nombre;
                                if($model->tipoExamen->clave == 'DIA' && $model->diagnostic_v2 == 1) {
                                    $tipo .= ' V2';
                                } else if($model->tipoExamen->clave == 'DIA' && $model->diagnostic_v3 == 1) {
                                    $tipo .= ' V3';
                                }
                                if($model->tipoExamen->clave == 'CER' && $model->certificate_v2 == 1) {
                                    $tipo .= ' V2';
                                }
                                return $tipo;
                            }
                        ],
                        [
                            'label'=>'Version',
                            'attribute'=>'variante.nombre',
                            'contentOptions' => function ($model) {
                                return ['class' => 'view-acad-exam', 'id' => $model->id];
                            }
                        ],
                        [
                            'label'=>'Total Points',
                            'attribute'=>'puntos',
                            'contentOptions' => function ($model) {
                                return ['class' => 'view-acad-exam', 'id' => $model->id];
                            }
                        ],
                        [
                            'label' => 'Total Duration',
                            'attribute'=>'duracion',
                            'value' => function($model){
                                $total_duration = $model->english_duration+$model->reading_duration+$model->listening_duration+$model->writing_duration;
                                return $total_duration > 0 ? $total_duration : $model->duracion;
                            },
                            'contentOptions' => function ($model) {
                                return ['class' => 'view-acad-exam', 'id' => $model->id];
                            }
                        ],
                        [
                            'label'=>'Last Modified',
                            'attribute'=>'user.academico.fullName',
                            'contentOptions' => function ($model) {
                                return ['class' => 'view-acad-exam', 'id' => $model->id];
                            }
                        ],
                        [
                            'label'=>'Sections',
                            'contentOptions' => function ($model) {
                                return ['class' => 'view-acad-exam', 'id' => $model->id];
                            },
                            'value' => function($model){
                                return count($model->seccions);
                            }
                        ],
                    ],
                ]);
                ?>
            <?php Pjax::end(); ?>
        <?php echo Html::endForm();?>
        </div>
    </section>
</div>
