<?php
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\widgets\Pjax;
use yii\helpers\Html;

$this->title = 'Institutes';
echo Html::hiddenInput('name', Yii::$app->controller->id, ['class' => 'controller']);
?>

<div class="alumno">
    <section id="inicio-alumno" class="inicio">
        <div class="container">
            <div class="half-div">
                <h2>General Information</h2>
            </div>

            <div class="info-alumno">
                <p><span>Name:</span> <?= $alumno->nombre . ' ' . $alumno->apellidos ?>
                <?php /* ?>
                &emsp;<span>Level:</span> <?= $alumno->nivelAlumno->nombre ?>
                &emsp;<span>User:</span> <?= 432798437 ?>
                */ ?>
                </p>
            </div>
        </div>
    </section>

    <section id="graficas-alumno">
        <div class="container">
            <div>
                <h2>Results</h2>
                <!-- <div class="change-block">
                    <span>Level</span>
                    <?php //echo Html::dropDownList('examen', '', $examenes, ['prompt' => 'Select', 'class' => 'dropdown-examen-alumno']) ?>
                </div> -->
            </div>
            <div class="graficas-container">
                <div class="tipo-examen">
                    <h2>Diagnostic</h2>
                    <?php
                    foreach($alumno->alumnoExamens as $alumnoExamen){
                        if($alumnoExamen->tipo_examen_id == $diagnosticType){
                            foreach($alumnoExamen->aluexaReactivos as $reactivo){
                                if(($reactivo->respuestaWriting || $reactivo->respuestaWriting == "") && $reactivo->calificado == 1){
                                    foreach ($alumnoExamen->examen->seccions as $seccion) {
                                        $tipo = $seccion->tipoSeccion->clave;
                                        switch ($tipo) {
                                            case "USE":
                                                $calificacionUse = ($alumnoExamen->calificaciones->calificacionUse * 100) / $seccion->puntos_seccion;
                                                break;
                                            case 'REA':
                                                $calificacionRea = ($alumnoExamen->calificaciones->calificacionReading * 100) / $seccion->puntos_seccion;
                                                break;
                                            case 'LIS':
                                                $calificacionLis = ($alumnoExamen->calificaciones->calificacionListening * 100) / $seccion->puntos_seccion;
                                                break;
                                            case 'WRI':
                                                $calificacionWri = ($alumnoExamen->calificaciones->calificacionWriting * 100) / $seccion->puntos_seccion;
                                                break;
                                        }
                                    }
                                    $promedio = ($calificacionUse + $calificacionRea  + $calificacionLis + $calificacionWri) / 4;
                                }
                            }
                        }
                    }
                    ?>
                    <div class="graficas">
                        <div class="col-md-2"><div class="graf-listening" data-percent="<?php echo (isset($calificacionLis) ? $calificacionLis : 0) ?>"></div></div>
                        <div class="col-md-2"><div class="graf-reading" data-percent="<?php echo (isset($calificacionRea) ? $calificacionRea : 0) ?>"></div></div>
                        <div class="col-md-2"><div class="graf-use" data-percent="<?php echo (isset($calificacionUse) ? $calificacionUse : 0) ?>"></div></div>
                        <div class="col-md-2"><div class="graf-writing" data-percent="<?php echo (isset($calificacionWri) ? $calificacionWri : 0) ?>"></div></div>
                        <div class="col-md-2"></div>
                        <div class="col-md-2"><div class="graf-percentage" data-percent="<?php echo (isset($promedio) ? $promedio : 0) ?>"></div></div>
                    </div>
                </div>
                <div class="tipo-examen">
                    <h2>Mock</h2>
                    <?php
                    $helperMock = 0;
                    foreach($alumno->alumnoExamens as $alumnoExamen){
                        if($alumnoExamen->tipo_examen_id == $mockType && $alumnoExamen->calificaciones_id){
                            $helperMock++;
                            if($alumnoExamen->examen->nivelAlumno->nombre == 'A1' || $alumnoExamen->examen->nivelAlumno->nombre == 'A2' || $alumnoExamen->examen->nivelAlumno->nombre == 'N/A'){
                                $calificacionUse = ($alumnoExamen->calificaciones->calificacionUse * 100) / 12;
                                $calificacionRea = ($alumnoExamen->calificaciones->calificacionReading * 100) / 24;
                                $calificacionLis = ($alumnoExamen->calificaciones->calificacionListening * 100) / 24;
                            } else {
                                $calificacionUse = ($alumnoExamen->calificaciones->calificacionUse * 100) / 15;
                                $calificacionRea = ($alumnoExamen->calificaciones->calificacionReading * 100) / 32;
                                $calificacionLis = ($alumnoExamen->calificaciones->calificacionListening * 100) / 32;
                            }
                            $promedio = ($calificacionLis * .35) + ($calificacionRea * .35) + ($calificacionUse * .30);
                    ?>
                    <div class="graficas">
                        <div class="col-md-2"><div class="graf-listening" data-percent="<?php echo $calificacionLis ?>"></div></div>
                        <div class="col-md-2"><div class="graf-reading" data-percent="<?php echo $calificacionRea ?>"></div></div>
                        <div class="col-md-2"><div class="graf-use" data-percent="<?php echo $calificacionUse ?>"></div></div>
                        <div class="col-md-2"></div>
                        <div class="col-md-2"></div>
                        <div class="col-md-2"><div class="graf-percentage" data-percent="<?php echo $promedio ?>"></div></div>
                    </div>
                    <?php
                        }
                    }
                    if($helperMock == 0){
                    ?>
                    <div class="graficas">
                        <div class="col-md-2"><div class="graf-listening" data-percent="0"></div></div>
                        <div class="col-md-2"><div class="graf-reading" data-percent="0"></div></div>
                        <div class="col-md-2"><div class="graf-use" data-percent="0"></div></div>
                        <div class="col-md-2"></div>
                        <div class="col-md-2"></div>
                        <div class="col-md-2"><div class="graf-percentage" data-percent="0"></div></div>
                    </div>
                    <?php
                    }
                    ?>
                </div>
            </div>
        </div>
    </section>
</div>
