<?php
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\widgets\Pjax;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use app\models\TipoSeccion;
use app\models\Seccion;

$examenForm->preguntas = [];
$examenForm->respuestasMul = [];
$secciones_reading = 0;
$secciones_listening = 0;
$secciones_use = 0;
$secciones_examen = [
    'REA' => 0,
    'LIS' => 0,
    'USE' => 0
];
?>

<div class="exams-student">
    <section id="inicio-examen-stud" class="inicio">
        <div class="container">
            <h2>
                <?php // echo $examen->nivelAlumno->nombre.' '.$examen->tipoExamen->nombre; ?>
            </h2>
        </div>
    </section>
    <?php $form = ActiveForm::begin(['id' => 'exam-add-form', 'action' => 'add-exam']); ?>
        <?= Html::hiddenInput('ExamenResueltoForm[id]', $idAlumnoExamen) ?>
        <?= Html::hiddenInput('examen', $examen->id) ?>
        <?php foreach($tiempos as $clave_bloque => $tiempo): ?>
        <input type="hidden" id="tiempo-<?= $clave_bloque ?>" value="<?= $tiempo ?>" />
        <?php endforeach; ?>
        <?php foreach($tiemposUsados as $clave_bloque => $tiempo): ?>
        <input type="hidden" id="tiempo-usado-<?= $clave_bloque ?>" value="<?= $tiempo ?>" />
        <?php endforeach; ?>
        <section id="preguntas" class="preguntas">
            <div class="container">
            <?php $wri = TipoSeccion::find()->where(['clave'=>'WRI'])->one();
            if($wri){
                $secciones = Seccion::find()->where('seccion.examen_id =' . $examen->id . ' && seccion.tipo_seccion_id !=' . $wri->id)->all();
            } else {
                $secciones = $examen->seccions;
            }
            $secciones_cargadas = [];
            shuffle($secciones);
            if (isset($examenForm->preguntasGuard)) {
                $sectionsSaved = [];
                foreach ($secciones as $index => $seccion) {
                    $questionCounter = 0;
                    $questions = $seccion->reactivosActivos();
                    foreach ($questions as $question) {
                        if (in_array($question->id, $examenForm->preguntasGuard)) {
                            $questionCounter++;
                        }
                    }
                    if ($questionCounter == count($questions)) {
                        array_push($sectionsSaved, $index);
                    }
                }
                foreach ($sectionsSaved as $section) {
                    unset($secciones[$section]);
                }
                $secciones = array_values($secciones);
            }
            if (empty($secciones)) {
                return Yii::$app->response->redirect(Url::to(['students/calificar', 'id' => $idAlumnoExamen, 'examen' => $examen->id]));
            }
            $indice = 0;
            foreach ($secciones as $consecutivo_seccion => $seccion) {
                if($consecutivo_seccion+1 >= count($secciones)){
                    $siguiente_seccion = 'FIN';
                }else{
                    if(count($secciones_cargadas) < 1){
                        if($secciones[$consecutivo_seccion+1]->tipoSeccion->clave == $seccion->tipoSeccion->clave){
                            $siguiente_seccion = $secciones[$consecutivo_seccion+1]->tipoSeccion->clave.'-'. strval($secciones_examen[$secciones[$consecutivo_seccion+1]->tipoSeccion->clave]+1);
                        }else{
                            $siguiente_seccion = $secciones[$consecutivo_seccion+1]->tipoSeccion->clave;
                        }
                    }else if($secciones_examen[$secciones[$consecutivo_seccion+1]->tipoSeccion->clave] > 0 && $secciones[$consecutivo_seccion+1]->tipoSeccion->clave == $seccion->tipoSeccion->clave){
                            $siguiente_seccion = $secciones[$consecutivo_seccion+1]->tipoSeccion->clave.'-'. strval($secciones_examen[$secciones[$consecutivo_seccion+1]->tipoSeccion->clave]+1);
                    }else if($secciones_examen[$secciones[$consecutivo_seccion+1]->tipoSeccion->clave] > 0 && $secciones[$consecutivo_seccion+1]->tipoSeccion->clave != $seccion->tipoSeccion->clave){
                        $siguiente_seccion = $secciones[$consecutivo_seccion+1]->tipoSeccion->clave.'-'.strval($secciones_examen[$secciones[$consecutivo_seccion+1]->tipoSeccion->clave]);
                    }else if($secciones_examen[$secciones[$consecutivo_seccion+1]->tipoSeccion->clave] == 0 && $secciones[$consecutivo_seccion+1]->tipoSeccion->clave == $seccion->tipoSeccion->clave){
                        $siguiente_seccion = $secciones[$consecutivo_seccion+1]->tipoSeccion->clave.'-'.strval($secciones_examen[$secciones[$consecutivo_seccion+1]->tipoSeccion->clave]+1);
                    }else{
                        $siguiente_seccion = $secciones[$consecutivo_seccion+1]->tipoSeccion->clave;
                    }
                }
            ?>
                <div
                    class="seccion<?= $consecutivo_seccion > 0 ? ' oculto' : ' visible' ?>"
                    id="seccion-<?= $seccion->tipoSeccion->clave.($secciones_examen[$seccion->tipoSeccion->clave] > 0 ? '-'.$secciones_examen[$seccion->tipoSeccion->clave] : '') ?>"
                    data-siguiente="<?= $siguiente_seccion ?>"
                >
                    <div class="titulo-seccion">
                        <?= $seccion->tipoSeccion->nombre ?>
                    </div>
                    <?php if($seccion->tipoSeccion->clave == 'REA'){ ?>
                    <div class="bloque-reading">
                        <div class="titulo-reading">
                            <?php echo $seccion->reactivos[0]->articulo->titulo ?>
                        </div>
                        <div class="cuerpo-reading">
                            <?php echo nl2br($seccion->reactivos[0]->articulo->texto) ?>
                        </div>
                        <?php $imagen = $seccion->reactivos[0]->articulo->imagen ?>
                        <?php if($imagen){ ?>
                            <div class="imagen-reading">
                                <a class="zoom-reading" href="javascript:;" data-section="<?= $seccion->reactivos[0]->id ?>">
                                    <?= Html::img('@web/'.$imagen) ?>
                                </a>
                                <div id="imagen-reading-<?= $seccion->reactivos[0]->id?>" class="mfp-hide">
                                    <?= Html::img('@web/'.$imagen) ?>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                    <?php } else if($seccion->tipoSeccion->clave == 'LIS') {
                        $cadena_separada = explode('.', $seccion->reactivos[0]->audio->audio);
                        $numero_generado = count($cadena_separada);
                    ?>
                    <div class="audio-player-container">
                        <audio class="audio-exam" controlsList="nodownload" ontimeupdate="updateAudioPlayerTimes(this)">
                            <source src="<?= Url::to('@web/' . $seccion->reactivos[0]->audio->audio) ?>"
                                type="<?php switch($cadena_separada[$numero_generado - 1]){
                                case 'mp3':
                                    echo "audio/mpeg";
                                    break;
                                case 'wav':
                                    echo "audio/wav";
                                    break;
                                }
                                ?>"
                            >
                        </audio>
                        <button type="button" class="audio-player-button center-block"><?= Html::img(["@web/images/play_button.png"]) ?></button>
                        <p class="text-center"><span class="remaining-time">--:--</span> / <span class="duration-time">--:--</span></p>
                    </div>
                    <?php } ?>
                    <div class="datos-seccion instructions">
                        <p><?= isset($seccion->instrucciones_generales) ? $seccion->instrucciones_generales : '' ?></p>
                    </div>
                    <?php $reactivos = $seccion->reactivosActivos();
                    foreach ($reactivos as $consecutivo_reactivo => $reactivo) {
                        if(isset($examenForm->preguntasGuard))
                        {
                            $guardado = array_search($reactivo->id, $examenForm->preguntasGuard);
                            if ($guardado !== false) {
                                $examenForm->preguntas[$indice] = $examenForm->preguntasGuard[$guardado];
                                if($examenForm->respuestasMulGuard[$guardado]){
                                    $examenForm->respuestasMul[$indice] = $examenForm->respuestasMulGuard[$guardado];
                                } else {
                                    $examenForm->respuestasMul[$indice] = 0;
                                }
                            }
                        }
                        if(!isset($examenForm->preguntas[$indice])){
                            echo Html::hiddenInput('ExamenResueltoForm[preguntas][]', $reactivo->id);
                        }
                        $siguiente_pregunta = $consecutivo_reactivo+1 >= count($reactivos) ? 'NEXT' : $consecutivo_reactivo+1;
                    ?>
                    <div
                        class="bloque-pregunta<?= ($consecutivo_reactivo > 0 && $seccion->tipoSeccion->clave == 'USE') ? ' oculto' : ' visible' ?>"
                        data-siguiente="<?= $seccion->tipoSeccion->clave.'-'.$siguiente_pregunta ?>"
                        id="<?= $seccion->tipoSeccion->clave.'-'.$consecutivo_reactivo ?>"
                    >
                    <?php if ($reactivo->tipoReactivo->clave == 'MUL') { ?>
                        <div class="instruccion"><?= nl2br($reactivo->instrucciones) ?></div>
                        <div class="pregunta"><?= nl2br($reactivo->pregunta) ?></div>
                        <?php
                        $respuestas = [];
                        foreach ($reactivo->respuestas as $respuesta) {
                            if($respuesta->respuesta){
                                $respuestas[$respuesta->id] = $respuesta->respuesta;
                            }
                        }
                        //randomiza respuestas
                        $llaves = array();
                        $respuestasRand = array();
                        $llaves = array_keys($respuestas);
                        shuffle($llaves);
                        foreach ($llaves as $llave) {
                            $respuestasRand[$llave] = $respuestas[$llave];
                        }
                        if(isset($examenForm->respuestasMul[$indice])){
                            echo $form->field($examenForm, 'respuestasMul[' . $indice . ']')->radioList($respuestasRand, ['class' => 'preguntas-mult', 'item' => function ($index, $label, $name, $checked, $value) {
                                $disabled = true;
                                return Html::radio($name, $checked, [
                                    'value' => $value,
                                    'label' => Html::encode($label),
                                    'disabled' => $disabled,
                                ]);
                            }])->label(false);
                        }else{
                            echo $form->field($examenForm, 'respuestasMul[' . $indice . ']')->radioList($respuestasRand, ['class' => 'preguntas-mult'])->label(false);
                        }
                        $indice++;
                        ?>
                    <?php } else if ($reactivo->tipoReactivo->clave == 'REL') { ?>
                        <div class="instruccion"><?= nl2br($reactivo->instrucciones) ?></div>
                        <div class=pregunta><?= nl2br($reactivo->pregunta) ?></div>
                        <?php
                        $respuestas = $reactivo->respuestaColumns;
                        shuffle($respuestas);
                        $enunciadosSel = [];
                        ?>
                        <?php
                        foreach ($reactivo->enunciadoColumns as $enunciado) { ?>
                            <?php
                            $enunciadosSel[$enunciado->id] = $enunciado->enunciado;
                            ?>
                        <?php } ?>
                        <table class="respuestas-relacionar">
                            <?php
                            foreach ($respuestas as $respuesta) { ?>
                                <tr>
                                    <td><?= $respuesta->respuesta ?></td>
                                    <td>
                                        <?= Html::hiddenInput('ExamenResueltoForm[respuestasCol][]', $respuesta->id); ?>
                                        <?=$form->field($examenForm, 'enunciadosCol[]')->dropDownList($enunciadosSel, ['prompt'=>'Please select', 'class'=>'enuncia-sel'])->label(false);?>
                                    </td>
                                </tr>
                            <?php } ?>
                        </table>
                        <div class="clear"></div>
                    <?php } else if ($reactivo->tipoReactivo->clave == 'COM') { ?>
                        <div class="instruccion"><?= nl2br($reactivo->instrucciones) ?></div>
                        <div class=pregunta><?= nl2br($reactivo->pregunta) ?></div>
                        <?php if(isset($examenForm->respuestasComGuard[$reactivo->id])){ ?>
                            <?php $opciones = ['value' => $examenForm->respuestasComGuard[$reactivo->id], 'disabled' => true] ?>
                        <?php }else{  ?>
                            <?php $opciones = ['placeholder' => 'Answer'] ?>
                        <?php } ?>
                        <?= $form->field($examenForm, 'respuestasCom[]')->textInput($opciones)->label(false); ?>
                    <?php } ?>
                    </div>
                <?php } ?>
                </div>
                <?php $secciones_cargadas[$seccion->tipoSeccion->clave] = true ?>
                <?php $secciones_examen[$seccion->tipoSeccion->clave] += 1 ?>
            <?php } ?>
            </div>
        </section>
        <div class="warning-text text-center">
            <p>Please click ‘Next’ if you are ready to move onto the next <span id="warning-type"><?= $secciones[0]->tipoSeccion->clave == "USE" ? "question" : "section" ?></span>. You will not be able to return to this page.</p>
        </div>
        <div id="acciones-examen">
            <a href="javascript:;" id="next" class="btn-oxford">Next</a>
        </div>
        <div id="spinner-examen" class="oculto">
            <div class="lds-dual-ring"></div>
        </div>
    <?php ActiveForm::end(); ?>
</div>
<div id="countdown-timer"><span id="time"></span></div>

<div id="time-dialog" class="mfp-hide white-popup-block">
    <h2>TIME OVER!</h2>
    <p>Your time for this section has finished. Click on continue to skip to next question.</p>
    <a class="popup-modal-dismiss btn-oxford" href="javascript:;">Continue</a>
</div>
<div id="connection-dialog" class="mfp-hide white-popup-block">
    <h2>Connection Problem</h2>
    <p>Please check your internet connection and reload the page</p>
</div>
<div id="error-dialog" class="mfp-hide white-popup-block">
    <h2>Error saving answers</h2>
    <p>There was an error saving your awnsers.</p>
    <div class="error-web"></div>
    <a class="btn-oxford retry-answers" href="javascript:;">Try Again</a>
</div>
