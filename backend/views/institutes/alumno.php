<?php
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\widgets\Pjax;
use yii\helpers\Html;
use yii\helpers\Url;
use app\models\Examen;
use app\models\Calificaciones;
use app\models\Seccion;
use app\models\AlumnoExamen;
use app\models\Programa;

$this->title = 'Institutes';
echo Html::hiddenInput('name', Yii::$app->controller->id, ['class'=>'controller']);
?>

<div class="alumno">
    <section id="inicio-alumno" class="inicio">
        <div class="container">
            <a class="back-button" href="<?php echo Url::toRoute(['institutes/grupo', 'id'=>$alumno->grupo->id]) ?>"></a>
            <div class="half-div">
                <h2>General Information</h2>
            </div><!--
         --><div class="half-div alinea-der">
                <?php if(in_array(Yii::$app->user->identity->id, [1,4391,16,4392])){ ?>
                    <a href="<?php echo Url::to(['institutes/logout-student', 'id'=>$alumno->id]) ?>" class="btn-oxford boton-peque">Logout</a>
                <?php } ?>
                <a href="javascript:;" class="btn-oxford boton-peque" id='edit-student-button'>Edit</a>
                <a href="<?php echo Url::to(['solved-exams/exams', 'id'=>$alumno->id]) ?>" class="btn-oxford boton-peque">See exams</a>
            </div>

            <?php
                $nivel_final = "";
                if($alumno->nivel_certificate_id){
                    $nivel_final = 'Certificate '.$alumno->nivelCertificate->nombre;
                }else if($alumno->nivel_mock_id && $alumno->nivelMock->clave != 'NO'){
                    $nivel_final = 'Mock '.$alumno->nivelMock->nombre;
                }else{
                    $nivel_final = 'Diagnostic'.($alumno->diagnostic_v2 == 1 ? ' V2 ' : ' ').($alumno->diagnostic_v3 == 1 ? ' V3 ' : ' ').$alumno->nivelAlumno->nombre;
                }
            ?>

            <div class="info-alumno">
                <p><span>Name:</span> <?= $alumno->nombre.' '.$alumno->apellidos ?>
                &emsp;<span>Final Level: <?= $nivel_final ?></span>
                &emsp;<span>Status:</span>
                <?php if($alumno->status)
                {
                    echo "Active";
                }
                else{
                    echo "Inactive";
                }
                ?>
                </p>
            </div>

            <div class="edit-info-alumno oculto">
                <?php echo $this->render('_edit-student', [
                    'alumno' => $alumno,
                    'studentForm' => $studentForm
                ]) ?>
            </div>
        </div>
    </section>

    <section id="graficas-alumno">
        <div class="container">
            <div>
                <h2>Results</h2>
            </div>
            <div class="graficas-container">
                <div class="tipo-examen">
                    <?php
                    $calificaciones = new Calificaciones();
                    $calificaciones = $calificaciones->nivelDiagnostic($alumno->id);
                    if ($calificaciones) {
                        $calificacionUse = (isset($calificaciones->promedio_use) ? $calificaciones->promedio_use : 0);
                        $calificacionRea = (isset($calificaciones->promedio_reading) ? $calificaciones->promedio_reading : 0);
                        $calificacionLis = (isset($calificaciones->promedio_listening) ? $calificaciones->promedio_listening : 0);
                        $calificacionWri = (isset($calificaciones->promedio_writing) ? $calificaciones->promedio_writing : 0);
                        $promedio = (isset($calificaciones->promedio) ? $calificaciones->promedio : 0);
                        $fecha = $calificaciones->fecha;
                    }
                    ?>
                    <h2 class="exam-results">
                        <span>Diagnostic</span>
                        <span>Date: <?= $fecha ? date( "d/m/y", $fecha ) : 'N/A' ?></span>
                        <span>Final Level: <?= $fecha ? $alumno->nivelAlumno->nombre : 'N/A' ?></span>
                    </h2>
                    <div class="graficas">
                        <div class="col-md-2"><div class="graf-listening" data-percent="<?= (isset($calificacionLis) && isset($calificaciones->promedio_writing) == "FIN" ? $calificacionLis : 0) ?>"></div></div>
                        <div class="col-md-2"><div class="graf-reading" data-percent="<?= (isset($calificacionRea) && isset($calificaciones->promedio_writing) == "FIN" ? $calificacionRea : 0) ?>"></div></div>
                        <div class="col-md-2"><div class="graf-use" data-percent="<?= (isset($calificacionUse) && isset($calificaciones->promedio_writing) == "FIN" ? $calificacionUse : 0) ?>"></div></div>
                        <div class="col-md-2"><div class="graf-writing" data-percent="<?= (isset($calificacionWri) && isset($calificaciones->promedio_writing) == "FIN" ? $calificacionWri : 0) ?>"></div></div>
                        <div class="col-md-2"></div>
                        <div class="col-md-2"><div class="graf-percentage" data-percent="<?= (isset($promedio) && isset($calificaciones->promedio_writing) == "FIN" ? $promedio : 0) ?>"></div></div>
                    </div>
                </div>
                <?php
                $helperMock = 0;
                $fecha = null;
                foreach($alumno->alumnoExamens as $alumnoExamen){
                    if($alumnoExamen->tipo_examen_id == $mockType && $alumnoExamen->calificaciones_id){
                        $helperMock++;
                        $calificacionUse = $alumnoExamen->calificaciones->promedio_use;
                        $calificacionRea = $alumnoExamen->calificaciones->promedio_reading;
                        $calificacionLis = $alumnoExamen->calificaciones->promedio_listening;
                        $promedio = $alumnoExamen->calificaciones->promedio;
                        $nivel_examen = $alumnoExamen->examen->nivelAlumno->nombre;
                        $fecha = $alumnoExamen->fecha_realizacion;
                    }
                } ?>
                <div class="tipo-examen" style="display:<?= $helperMock == 0 ? 'none' : 'block' ?>;">
                    <h2 class="exam-results">
                        <span>Mock</span>
                        <span>Level Exam: <?= $alumno->nivel_inicio_mock_id ? $alumno->nivelMockInicial->nombre : $alumno->nivelMock->nombre ?></span>
                        <span>Date: <?= date( "d/m/y", $fecha ) ?></span>
                        <span>Final Level: <?= $alumno->nivelMock->nombre ?></span>
                    </h2>
                    <?php if($helperMock == 0){ ?>
                        <div class="graficas">
                            <div class="col-md-2"><div class="graf-listening" data-percent="0"></div></div>
                            <div class="col-md-2"><div class="graf-reading" data-percent="0"></div></div>
                            <div class="col-md-2"><div class="graf-use" data-percent="0"></div></div>
                            <div class="col-md-2"></div>
                            <div class="col-md-2"></div>
                            <div class="col-md-2"><div class="graf-percentage" data-percent="0"></div></div>
                        </div>
                    <?php }else{ ?>
                        <div class="graficas">
                            <div class="col-md-2"><div class="graf-listening" data-percent="<?= $calificacionLis ?>"></div></div>
                            <div class="col-md-2"><div class="graf-reading" data-percent="<?= $calificacionRea ?>"></div></div>
                            <div class="col-md-2"><div class="graf-use" data-percent="<?= $calificacionUse ?>"></div></div>
                            <div class="col-md-2"></div>
                            <div class="col-md-2"></div>
                            <div class="col-md-2"><div class="graf-percentage" data-percent="<?= $promedio ?>"></div></div>
                        </div>
                    <?php } ?>
                </div>
                <?php
                $alumno_examen = AlumnoExamen::find()
                    ->leftJoin('tipo_examen', 'tipo_examen.id = alumno_examen.tipo_examen_id')
                    ->leftJoin('calificaciones', 'calificaciones.id = alumno_examen.calificaciones_id')
                    ->where([
                        'and',
                        ['is not','calificaciones_id', null],
                        ['tipo_examen.clave' => 'CER'],
                        ['is not', 'calificaciones.calificacionWriting', null],
                        ['is not', 'fecha_realizacion', null],
                        ['alumno_id' => $alumno->id]
                    ])
                    ->one();
                if (isset($alumno_examen)) {
                    $calificacionUse = (isset($alumno_examen->calificaciones->promedio_use) ? $alumno_examen->calificaciones->promedio_use : 0);
                    $calificacionRea = (isset($alumno_examen->calificaciones->promedio_reading) ? $alumno_examen->calificaciones->promedio_reading : 0);
                    $calificacionLis = (isset($alumno_examen->calificaciones->promedio_listening) ? $alumno_examen->calificaciones->promedio_listening : 0);
                    $calificacionWri = (isset($alumno_examen->calificaciones->promedio_writing) ? $alumno_examen->calificaciones->promedio_writing : 0);
                    $calificacionSpeaking = (isset($alumno_examen->calificaciones->calificacionSpeaking) ? $alumno_examen->calificaciones->calificacionSpeaking : 0);
                    $promedio = (isset($alumno_examen->calificaciones->promedio) ? $alumno_examen->calificaciones->promedio : 0);
                    $fecha = $alumno_examen->fecha_realizacion;
                    $programa = new Programa();
                    $programa = $programa->porGrupo($alumno->grupo_id);
                    $visible = false;
                    if(($programa->clave != 'CLI' && $calificacionWri !== null) || (($calificacionSpeaking !== null && $calificacionWri !== null) && $programa->clave == 'CLI')){
                        $visible = true;
                    }
                } else {
                    $visible = false;
                }
                ?>
                <div class="tipo-examen" style="display:<?= $visible ? 'block' : 'none' ?>;">
                    <h2 class="exam-results">
                        <span>Certificate</span>
                        <span>Level Exam: <?= $alumno->nivel_inicio_certificate_id ? $alumno->nivelCertificateInicial->nombre : $alumno->nivelCertificate->nombre ?></span>
                        <span>Date: <?= date( "d/m/y", $fecha ) ?></span>
                        <span>Final Level: <?= $alumno->nivelCertificate->nombre ?></span>
                    </h2>
                    <?php if(($programa->clave != 'CLI' && isset($alumno_examen->calificaciones->promedio_writing)) || ((isset($alumno_examen->calificaciones->calificacionSpeaking) && isset($alumno_examen->calificaciones->promedio_writing) !== null) && $programa->clave == 'CLI')){ ?>
                        <div class="graficas">
                            <div class="col-md-2">
                                <div class="graf-listening" data-percent="<?= (isset($calificacionLis) ? $calificacionLis : 0) ?>"></div>
                            </div>
                            <div class="col-md-2">
                                <div class="graf-reading" data-percent="<?= (isset($calificacionRea) ? $calificacionRea : 0) ?>"></div>
                            </div>
                            <div class="col-md-2">
                                <div class="graf-use" data-percent="<?= (isset($calificacionUse) ? $calificacionUse : 0) ?>"></div>
                            </div>
                            <div class="col-md-2">
                                <div class="graf-writing" data-percent="<?= (isset($calificacionWri) ? $calificacionWri : 0) ?>"></div>
                            </div>
                            <?php if($programa->clave == 'CLI'){ ?>
                                <div class="col-md-2">
                                    <!-- <div class="graf-speaking" data-percent="<?= (isset($calificacionSpeaking) && $calificacionWri !== 0 ? $calificacionSpeaking : 0) ?>"></div> -->
                                    <div class="graf-speaking" data-percent="<?= (isset($calificacionSpeaking) ? $calificacionSpeaking : 0) ?>"></div>
                                </div>
                            <?php }else{ ?>
                                <div class="col-md-2"></div>
                            <?php } ?>
                            <div class="col-md-2">
                                <div class="graf-percentage" data-percent="<?= (isset($promedio) ? $promedio : 0) ?>"></div>
                            </div>
                        </div>
                    <?php }else{ ?>
                        <div class="graficas">
                            <div class="col-md-2"><div class="graf-listening" data-percent="0"></div></div>
                            <div class="col-md-2"><div class="graf-reading" data-percent="0"></div></div>
                            <div class="col-md-2"><div class="graf-use" data-percent="0"></div></div>
                            <div class="col-md-2"><div class="graf-writing" data-percent="0"></div></div>
                            <?php if($programa->clave == 'CLI'){ ?>
                                <div class="col-md-2"><div class="graf-speaking" data-percent="0"></div>
                            <?php } ?>
                            <div class="col-md-2"><div class="graf-percentage" data-percent="0"></div></div>

                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </section>

    <section class="info-alumno">
        <div class="container">
            <p>Code: <span class="another-font"><?= $alumno->users[0]->codigo ?></span></p>
            <p>Password: <span class="another-font"><?= $alumno->users[0]->accesoDec ?></span></p>
        </div>
    </section>
</div>
