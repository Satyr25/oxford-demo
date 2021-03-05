<?php
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\widgets\Pjax;
use yii\helpers\Url;
use yii\helpers\Html;
use app\models\Examen;
use yii\widgets\ActiveForm;
use app\models\AlumnoExamen;

$this->title = 'Institutes';
echo Html::hiddenInput('name', Yii::$app->controller->id, ['class'=>'controller']);
$cookie = Yii::$app->controller->id.'-'.$this->context->action->id;
$seleccionados = isset($_COOKIE[$cookie]) ? explode(',',$_COOKIE[$cookie]) : false;
?>

<div class="grupo">
    <section id="inicio-grupo" class="inicio">
        <div class="container">
            <a class="back-button" href="<?php echo Url::toRoute(['institutes/colegio', 'id'=>$grupo->instituto->id]) ?>"></a>
            <div class="half-div">
                <h2>Institute</h2>
            </div>
        </div>
    </section>

    <div class="grupo-search col-md-4" id="filtro-status">
        <div>
            <?php $form = ActiveForm::begin([
                'method' => 'get',
            ]); ?>
            <?= $form->field($filtro, 'status')->textInput()->label(false) ?>
            <?php ActiveForm::end(); ?>
        </div>
    </div>

    <section id="view-grupo">
        <div class="container">
            <div class="nombre-grupo">
                <h3>
                    <?= Html::a(
                        $grupo->instituto->nombre,
                        Url::to(['institutes/colegio', 'id' => $grupo->instituto->id])
                        ) ?>
                        - Group <?= $grupo->grupo ?></h3>
                <a href="javascript:;" class="btn-oxford editar-user" id="edit-grupo">Editar</a>
            </div>

            <div class="show-info">
                <div class="half-div tabla-datos">
                    <div class="separa-datos">
                        <p class="nombre-dato">Institute Name:</p><p><?= $grupo->instituto->nombre ?></p>
                    </div>
                    <div class="separa-datos">
                        <p class="nombre-dato">Group:</p><p> <?= $grupo->grupo ?></p>
                    </div>
                    <div class="separa-datos">
                        <p class="nombre-dato">Level:</p><p> <?= $grupo->nivel->nombre ?></p>
                    </div>
                </div><!--
             --><div class="half-div tabla-datos">
                    <div class="separa-datos">
                        <p class="nombre-dato">Students No.:</p><p> <?= count($grupo->alumnosActivos) ?></p>
                    </div>
                    <div class="separa-datos">
                        <p class="nombre-dato">Status:</p><p> <?php
                        if($grupo->status){
                            echo "Active";
                        }else{
                            echo "Inactive";
                        }
                        ?>
                    </p>
                    </div>
                </div>
            </div>

            <?php echo $this->render('_edit-group', [
                'grupo' => $grupo,
                'grupoForm' => $grupoForm,
                'niveles' => $nivelesGrupo,
                'ciclos' => $ciclos
            ]); ?>
        </div>
    </section>

    <section id='botones-grupo' class="busqueda">
        <div class="container">
            <div class="col-md-4">
                <a href="add-student?id=<?= $grupo->id ?>" class="boton-add alumno col-md-6">+ Add Student</a>
            </div>
            <div class="col-md-8 botones-busqueda">
                <div class="botones">
                    <div class="dropdown">
                        <input type="hidden" value="<?= Url::base(true); ?>" id="base_url" />
                        <a href="#" class="dropdown-toggle acciones-busqueda" data-toggle="dropdown">Export</a>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                            <li class="dropdown-submenu">
                                <a class="show-submenu" href="#">Diagnostic</a>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a class="export-buttons" href="<?= Url::to(['institutes/export-group', 'id' => $grupo->id, 'type' => $diagnosticType->id, 'file' => 'pdf']) ?>">PDF</a>
                                    </li>
                                    <li>
                                        <a class="export-buttons" href="<?= Url::to(['institutes/export-group', 'id' => $grupo->id, 'type' => $diagnosticType->id, 'file' => 'xls']) ?>">XLS</a>
                                    </li>
                                </ul>
                            </li>
                            <li class="dropdown-submenu">
                                <a class="show-submenu" href="#">Mock</a>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a class="export-buttons" href="<?= Url::to(['institutes/export-group', 'id' => $grupo->id, 'type' => $mockType->id, 'file' => 'pdf']) ?>">PDF</a>
                                    </li>
                                    <li>
                                        <a class="export-buttons" href="<?= Url::to(['institutes/export-group', 'id' => $grupo->id, 'type' => $mockType->id, 'file' => 'xls']) ?>">XLS</a>
                                    </li>
                                </ul>
                            </li>
                            <li class="dropdown-submenu">
                                <a class="show-submenu" href="#">Certificate</a>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a class="export-buttons" href="<?= Url::to(['institutes/export-group', 'id' => $grupo->id, 'type' => $certificateType->id, 'file' => 'pdf']) ?>">PDF</a>
                                    </li>
                                    <li>
                                        <a class="export-buttons" href="<?= Url::to(['institutes/export-group', 'id' => $grupo->id, 'type' => $certificateType->id, 'file' => 'xls']) ?>">XLS</a>
                                    </li>
                                </ul>
                            </li>
                        <ul/>
                    </div>
                    <a href="javascript:;" class="acciones-busqueda" id="btn-display-import">Import</a>
                    <?php if ($filtro->status === null || $filtro->status == 1) { ?>
                    <a href="javascript:;" id="delete-students" class="acciones-busqueda">Delete</a>
                    <a href="javascript:;" id="show-inactive" class="acciones-busqueda">Inactive Students</a>
                    <?php
                    } else if ($filtro->status == '0') { ?>
                    <a href="javascript:;" id="restore-students" class="acciones-busqueda">Restore</a>
                    <a href="javascript:;" id="show-active" class="acciones-busqueda">Active Students</a>
                    <?php
                    } ?>
                    <div class="lds-dual-ring exportar oculto"></div>
                </div>
                 <?php echo $this->render('_add-file', ['model' => $fileModel, 'id' => $grupo->id]); ?>
            </div>
        </div>
    </section>

    <section id="tabla-alumnos" class="tabla">
        <div class="container">
            <?= $this->render('_searchAlumnos', [
                'filtro' => $filtro,
                'niveles' => $niveles,
                'tipos_examen' => $tipos_examen,
                'examen_nivel' => $examen_nivel,
                ]) ?>

            <?php echo Html::beginForm(['update-multiple'],'post',['id'=>'alumnos-table-form']);?>
                <?php echo Html::dropDownList('status','',['INA' => 'Inactivo', 'ACT' => 'Activo'],['id'=>'status-dropdown-general-alumno', 'prompt'=>'Select status','class'=>'drop-general']); ?>
                <?php echo Html::dropDownList('tipo_examen','',$tipos_examen, ['prompt' => 'Select exam type','id'=>'examen-dropdown-general-alumno','class'=>'drop-general']); ?>
                <?php echo Html::dropDownList('nivel','',$niveles, ['prompt' => 'Select level','id'=>'level-dropdown-general-alumno','class'=>'drop-general']); ?>
                <?php echo Html::submitButton('Apply', ['class' => 'btn-oxford']);?>
                <?php echo Html::hiddenInput('grupo-id', $grupo->id); ?>
                <?php //echo Html::dropDownList('action','',[''=>'Mark selected as: ','c'=>'Confirmed','nc'=>'No Confirmed'],['class'=>'dropdown',])?>

                <?php Pjax::begin(['id' => 'pjax-grid-alumno']); ?>
                <?php
                echo GridView::widget([
                'dataProvider' => $dataProvider,
                'columns' => [
                    [
                        'class' => 'yii\grid\CheckboxColumn',
                        'checkboxOptions' => function($model) use ($seleccionados){
                            $checked = false;
                            if($seleccionados && in_array($model->id,$seleccionados)){
                                $checked = true;
                            }
                            return ['value' => $model->id, 'checked' => $checked];
                        },
                    ],
                    ['class' => 'yii\grid\SerialColumn'],
                    [
                        'attribute'=>'nombre',
                        'contentOptions'=>function($model){return ['class'=>'view-alumno','id'=>$model->id];}
                    ],
                    [
                        'attribute'=>'apellidos',
                        'contentOptions'=>function($model){return ['class'=>'view-alumno','id'=>$model->id];}
                    ],
                    [
                        'label' => 'Email',
                        'value' => function($model) {
                            return $model->users[0]->email;
                        },
                        'contentOptions'=>function($model){return ['class'=>'view-alumno','id'=>$model->id];},
                        'visible' => $grupo->instituto->programa->clave == 'IND' ? true : false
                    ],
                    [
                        'label'=> 'Level',
                        'value'=> function($model){
                            if($model->nivel_certificate_id && $model->nivel_certificate_id != 8){
                                return $model->nivelCertificate->nombre;
                            }else if($model->nivel_mock_id && $model->nivel_mock_id != 8){
                                return $model->nivelMock->nombre;
                            }
                            return $model->nivelAlumno->nombre;
                        },
                        'contentOptions'=>function($model){return ['class'=>'view-alumno','id'=>$model->id];}
                    ],
                    [
                        'label' => 'Exam Type',
                        'value' => function($model){
                            if(!$model->alumnoExamens){
                                return 'N/A';
                            }
                            $examenHelper = 'Diagnostic'.' '.($model->diagnostic_v2 == 1 ? 'V2' : '').($model->diagnostic_v3 == 1 ? 'V3' : '');
                            foreach($model->alumnoExamens as $examen){
                                if($examen->tipoExamen->clave == 'MOC'){
                                    $examenHelper = 'Mock';
                                }
                                if($examen->tipoExamen->clave == 'CER'){
                                    $examenHelper = 'Certificate'.($model->certificate_v2 == 1 ? ' V2' : '');
                                }
                            }
                            return $examenHelper;
                        },
                        'contentOptions' => function ($model) {
                            return ['class' => 'view-alumno', 'id' => $model->id];
                        }
                    ],
                    [
                        'label' => 'Date',
                        'value' => function ($model) {
                            $examenes = AlumnoExamen::find()->where(['alumno_id' => $model->id])->orderBy(['tipo_examen_id'=> SORT_DESC])->one();
                            if(isset($examenes->fecha_realizacion)){
                                return date('d M Y', $examenes->fecha_realizacion);
                            }
                            else{
                                return "--";
                            }
                        },
                        'contentOptions' => function ($model) {
                            return ['class' => 'view-alumno', 'id' => $model->id];
                        }
                    ],
                    [
                        'label'=>'Exam Status',
                        'contentOptions' => function ($model) {
                            return ['class' => 'view-alumno', 'id' => $model->id];
                        },
                        'value' => function($model){
                            if($model->status_examen_id){
                                return $model->statusExamen->nombre;
                            }else{
                                return 'N/A';
                            }
                        },
                        'contentOptions' => function ($model) {
                            return ['class' => 'view-alumno', 'id' => $model->id];
                        }
                    ]
                ],
            ]);
            ?>
            <?php Pjax::end(); ?>
        <?php echo Html::endForm();?>

        </div>
    </section>
</div>
