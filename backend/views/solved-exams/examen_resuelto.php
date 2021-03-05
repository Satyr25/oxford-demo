<?php
use yii\helpers\Html;
use app\models\AluexaReactivos;
use app\models\Respuesta;
use yii\helpers\Url;

$this->title = 'Exam';
$respuestasAlu = $alumnoExamen->aluexaReactivos;
$calificaciones = $alumnoExamen->calificaciones;
$sectionTitlesByLevel = [
    "A1" => ['Interview', 'Find the differences', 'Tell a story'],
    "A2" => ['Interview', 'Q & A', 'Tell a story'],
    "B1" => ['Interview', 'Talk about a picture', 'Candidate interaction'],
    "B2" => ['Interview', 'Compare and contrast', 'Candidate interaction'],
    "C1" => ['Interview', 'Candidate interaction', 'Sustained monologue'],
    "C2" => ['Interview', 'Candidate interaction', 'Sustained monologue'],
];
$fieldsToScoreByLevel = [
    "A1" => [
        ['Socio-linguistic awareness', 'Interaction', 'Speaker understanding', 'Fluency', 'Vocabulary', 'Grammar'],
        ['Task understanding', 'Fluency', 'Vocabulary', 'Grammar'],
        ['Fluency', 'Vocabulary', 'Grammar', 'Pronunciation'],
    ],
    "A2" => [
        ['Socio-linguistic awareness', 'Speaker understanding', 'Interaction', 'Vocabulary', 'Grammar'],
        ['Task understanding', 'Interaction', 'Vocabulary', 'Grammar', 'Pronunciation'],
        ['Fluency', 'Vocabulary', 'Grammar', 'Pronunciation'],
    ],
    "B1" => [
        ['Interaction', 'Speaker understanding', 'Fluency', 'Vocabulary', 'Grammar'],
        ['Fluency', 'Vocabulary', 'Grammar', 'Pronunciation'],
        ['Interaction', 'Fluency', 'Vocabulary', 'Grammar', 'Pronunciation'],
    ],
    "B2" => [
        ['Socio-linguistic awareness', 'Speaker understanding', 'Interaction', 'Fluency', 'Vocabulary', 'Grammar'],
        ['Task understanding', 'Production', 'Fluency', 'Vocabulary', 'Grammar', 'Pronunciation'],
        ['Interaction', 'Production', 'Fluency', 'Vocabulary', 'Grammar', 'Pronunciation'],
    ],
    "C1" => [
        ['Socio-linguistic awareness', 'Interaction', 'Speaker understanding', 'Fluency', 'Accuracy', 'Lexical range'],
        ['Interaction', 'Production', 'Fluency', 'Accuracy', 'Lexical range', 'Pronunciation'],
        ['Production', 'Fluency', 'Accuracy', 'Lexical range', 'Pronunciation'],
    ],
    "C2" => [
        ['Socio-linguistic awareness', 'Interaction', 'Production', 'Fluency', 'Accuracy', 'Lexical range', 'Understanding'],
        ['Socio-linguistic awareness', 'Interaction', 'Production', 'Fluency', 'Accuracy', 'Lexical range'],
        ['Socio-linguistic awareness', 'Production', 'Fluency', 'Accuracy', 'Lexical range', 'Pronunciation'],
    ],
];
$arrayMaxPoints = [
    "A1" => 70,
    "A2" => 70,
    "B1" => 75,
    "B2" => 90,
    "C1" => 85,
    "C2" => 95,
];
?>

<div class="exam-acad">
    <section id="inicio-exam-acad" class="inicio">
        <div class="container">
            <div class="mitad-izq">
                <h2><?= "{$examen->nivelAlumno->nombre} {$examen->tipoExamen->nombre} {$examen->variante->nombre}" ?></h2>
            </div>
        </div>
    </section>
    <section id="view-exam-acad" class="ver">
        <div class="container" id="datos-examen">
            <p class="col-md-6 nombre-campo">Test name:</p><p class="col-md-6"><?= "{$examen->nivelAlumno->nombre} {$examen->tipoExamen->nombre} {$examen->variante->nombre}" ?></p>
            <p class="col-md-6 nombre-campo">Level:</p><p class="col-md-6"><?= $examen->nivelAlumno->nombre ?></p>
            <p class="col-md-6 nombre-campo">Exam Type:</p><p class="col-md-6"><?= $examen->tipoExamen->nombre ?></p>
            <p class="col-md-6 nombre-campo">Version:</p><p class="col-md-6"><?= $examen->variante->nombre ?></p>
            <p class="col-md-6 nombre-campo">Test Passing Criteria Percentage:</p><p class="col-md-6"><?= $examen->porcentaje ?>%</p>
            <p class="col-md-6 nombre-campo">Total Duration:</p><p class="col-md-6"><?= $total ?> minutes</p>
            <p class="col-md-6 nombre-campo">Total Points:</p><p class="col-md-6"><?= $examen->puntos ?></p>
        </div>
        <?php if ($alumnoExamen->inactivity): ?>
        <div id="timed-out" style="margin:0 auto;">
            Closed due to inactivity.
        </div>
        <?php endif; ?>
        <div class="graficas" id="calificaciones-review">
            <div class="col-md-2"><div class="graf-listening" data-percent="<?= isset($promedios['LIS']) ? $promedios['LIS'] : 0 ?>"></div></div>
            <div class="col-md-2"><div class="graf-reading" data-percent="<?= isset($promedios['REA']) ? $promedios['REA'] : 0 ?>"></div></div>
            <div class="col-md-2"><div class="graf-use" data-percent="<?= isset($promedios['USE']) ? $promedios['USE'] : 0 ?>"></div></div>
            <div class="col-md-2"><div class="graf-writing" data-percent="<?= isset($promedios['WRI']) ? $promedios['WRI'] : 0 ?>"></div></div>
            <?php if($examen->tipoExamen->clave == 'CER' && $programa == 'CLI'){ ?>
                <div class="col-md-2"><div class="graf-speaking" data-percent="<?= isset($promedios['SPE']) ? $promedios['SPE'] : 0 ?>"></div></div>
            <?php }else{ ?>
                <div class="col-md-2"></div>
            <?php } ?>
            <div class="col-md-2"><div class="graf-percentage" data-percent="<?= isset($promedios['general']) ? $promedios['general'] : 0 ?>"></div></div>
            <div class="clear"></div>
        </div>
        <?php foreach ($examen->getExamSectionsOrderedToShow() as $seccion) {
            $reactivos = $seccion->reactivosActivos();
            if (count($reactivos) == 0) {
                continue;
            }
        ?>
        <div class="container horizontal-separator"></div>
        <div class="container">
            <div class="datos-seccion">
                <div>
                    <span class="nombre-campo">Section name:</span>
                    <span><?= $seccion->tipoSeccion->nombre ?></span>
                </div>
                <div>
                    <span class="nombre-campo">Points:</span>
                    <span><?= $seccion->puntos_seccion ?></span>
                </div>
                <div>
                    <span class="nombre-campo">Obtained:</span>
                    <?php if ($examen->tipoExamen->clave == "MOC" && $seccion->tipoSeccion->clave != "USE"):
                        $points = 0;
                        $studentAnswers = AluexaReactivos::find()
                            ->leftJoin('reactivo', 'reactivo.id = aluexa_reactivos.reactivo_id')
                            ->where([
                                'aluexa_reactivos.alumno_examen_id' => $alumnoExamen->id,
                                'reactivo.seccion_id' => $seccion->id
                            ])
                            ->all();
                        foreach ($studentAnswers as $studentAnswer) {
                            $answer = Respuesta::findOne($studentAnswer->respuesta_alu);
                            if ($answer->correcto) {
                                $points++;
                            }
                        }
                    ?>
                    <span><?= $points ?></span>
                    <?php else: ?>
                    <span><?= $puntos[$seccion->tipoSeccion->clave] ?></span>
                    <?php endif; ?>
                </div>
                <div>
                    <span class="nombre-campo">Percentage:</span>
                    <?php if ($examen->tipoExamen->clave == "MOC" && $seccion->tipoSeccion->clave != "USE"): ?>
                    <span><?= round(($points * 100) / $seccion->puntos_seccion, null, PHP_ROUND_HALF_DOWN) ?>%</span>
                    <?php else: ?>
                    <span><?= $promedios[$seccion->tipoSeccion->clave] ?>%</span>
                    <?php endif; ?>
                </div>
                <span class="clear"></span>
            </div>
            <?php
            foreach ($reactivos as $i => $reactivo) { ?>
            <div class="detalles-reactivo">
            <?php if ($i == 0 && $reactivo->articulo_id): ?>
                <div class="articulo" id="articulo-<?= $reactivo->articulo_id ?>">
                    <div class="bloque-reading-editar">
                        <a href="<?= Url::to(['exams-academic/update-reading-form', 'id' => $reactivo->articulo_id]) ?>" class="btn-oxford boton-peque update-reading">Edit</a>
                    </div>
                    <div style="clear:both;"></div>
                    <div class="titulo"><?= $reactivo->articulo->titulo ?></div>
                    <div class="article"><?= nl2br($reactivo->articulo->texto) ?></div>
                    <?php if (isset($reactivo->articulo->imagen)): ?>
                    <div class="image"><?= Html::img("@web/{$reactivo->articulo->imagen}", ['class' => 'img-responsive center-block']) ?></div>
                    <?php endif; ?>
                </div>
            <?php endif;
            if ($i == 0 && $reactivo->audio_id): ?>
                <div class="audio" id="audio-<?= $reactivo->audio_id ?>">
                    <div class="bloque-audio-editar">
                        <a href="<?= Url::to(['exams-academic/update-audio-form', 'id' => $reactivo->audio_id]) ?>" class="btn-oxford boton-peque update-audio">Edit</a>
                    </div>
                    <div class="titulo"><?= $reactivo->audio->nombre ?></div>
                    <audio controls>
                        <source src="<?php echo Url::to('@web/' . $reactivo->audio->audio) ?>" type="audio/mpeg">
                    </audio>
                </div>
            <?php endif; ?>
                <p><?= $reactivo->instrucciones ?></p>
                <p class="pregunta"><?= $i + 1 ?>.- Q: <?= $reactivo->pregunta ?></p>
                <?php if ($reactivo->tipoReactivo->clave == 'MUL') {
                $arregloResp = array();
                $respuestas = $reactivo->respuestas;
                $contador_respuesta = 'a';
                foreach ($respuestas as $respuesta) {
                        if($respuesta->respuesta){ ?>
                <p class="<?= $respuesta->correcto == 1 ? 'correcto-resp' : '' ?>">
                    <?= $contador_respuesta . ') ' . $respuesta->respuesta ?>
                </p>
                        <?php }
                        $contador_respuesta++;
                        $arregloResp[$respuesta->respuesta] = $respuesta->id;
                    }
                    foreach($respuestasAlu as $respuesta){
                        if ($respuesta->reactivo_id == $reactivo->id) {
                            $respuesta = array_search($respuesta->respuesta_alu, $arregloResp); ?>
                <p><span class="correcto-resp">Student's answer:</span> <?= $respuesta; ?></p>
                        <?php
                        }
                    }
                } else if ($reactivo->tipoReactivo->clave == 'COM'){ ?>
                <p>
                    <span class="correcto-resp">Correct answers:</span>
                    <?= implode(', ',explode('|',$reactivo->respuestasCompletar[0]->respuesta)) ?>
                </p>
                <p>
                <span class="correcto-resp">Student's answers:</span>
                        <?php foreach($respuestasAlu as $respuesta){ ?>
                            <?php if ($respuesta->reactivo_id == $reactivo->id) { ?>
                                <?= $respuesta->respuesta_completar ?>
                            <?php } ?>
                        <?php } ?>
                </p>
                <?php } else if ($reactivo->tipoReactivo->clave == 'REL') {
                    foreach ($reactivo->enunciadoColumns as $enunciado) { ?>
                <div class="relacionar">
                    <span><?= $enunciado->enunciado ?></span>
                    -
                    <?= $enunciado->respuestaColumn->respuesta ?>
                </div>
                    <?php }
                } else if($reactivo->tipoReactivo->clave == 'WRI') {
                    if($alumnoExamen->alumno->certificate_v2 == 1){
                        $aluexare = AluexaReactivos::find()->where(['alumno_examen_id'=>$alumnoExamen->id, ])->andWhere(['is not', 'respuestaWriting', null])->andWhere(['=', 'reactivo_id', $reactivo->id])->one();
                    }else{
                        $aluexare = AluexaReactivos::find()->where(['alumno_examen_id'=>$alumnoExamen->id, ])->andWhere(['is not', 'respuestaWriting', null])->one();
                    }
                    if($aluexare){
                    ?>
                <p>
                        <?php echo nl2br($aluexare->respuestaWriting);
                        if($timedOut){ ?>
                    <div id="timed-out">
                        Timed Out
                    </div>
                        <?php }
                    } else {
                        echo "Not exist";
                    } ?>
                </p>
                <?php } ?>
            </div>
            <?php } ?>
        </div>
        <?php }
        if (isset($calificaciones->calificacionSpeaking)):?>
        <div class="container horizontal-separator"></div>
        <div class="container">
            <div class="datos-seccion">
                <div>
                    <span class="nombre-campo">Section name:</span>
                    <span>Speaking</span>
                </div>
                <div>
                    <span class="nombre-campo">Points:</span>
                    <span><?= $arrayMaxPoints[$examen->nivelAlumno->nombre] ?></span>
                </div>
                <div>
                    <span class="nombre-campo">Obtained:</span>
                    <span><?= $calificaciones->totalPointsSpeaking ?></span>
                </div>
                <div>
                    <span class="nombre-campo">Percentage:</span>
                    <span><?= $calificaciones->calificacionSpeaking ?></span>
                </div>
                <span class="clear"></span>
            </div>
            <?php if (isset($calificaciones->calificaciones_spe)):
                $speakingScoresArray = explode(',', $calificaciones->calificaciones_spe);
                $counterScores = 0;
            ?>
            <div class="detalles-reactivo">
                <?php foreach ($sectionTitlesByLevel[$examen->nivelAlumno->nombre] as $sectionIndex => $section):
                    $increasedSectionIndex = $sectionIndex + 1 ?>
                    <p><strong><?= "Point {$increasedSectionIndex}: {$section}" ?></strong></p>
                    <?php foreach ($fieldsToScoreByLevel[$examen->nivelAlumno->nombre][$sectionIndex] as $fieldIndex => $field): ?>
                        <div class="row">
                            <div class="col-md-6">
                                <p><?= $field ?>:</p>
                            </div>
                            <div class="col-md-6">
                                <p><?= $speakingScoresArray[$counterScores++] ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endforeach; ?>
                <p><strong>Observaciones:</strong></p>
                <p><?= nl2br($calificaciones->observaciones_spe) ?></p>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </section>
</div>
