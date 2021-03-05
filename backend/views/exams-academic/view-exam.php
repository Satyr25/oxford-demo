<?php
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\widgets\Pjax;
use yii\helpers\Html;
use app\models\Examen;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;

$this->title = 'Exam';
echo Html::hiddenInput('name', Yii::$app->controller->id, ['class' => 'controller']);
?>

<div class="exam-acad">
    <section id="inicio-exam-acad" class="inicio">
        <div class="container">
            <div class="mitad-izq">
                <h2><?= $examen->nivelAlumno->nombre . ' ' . $examen->tipoExamen->nombre . ' ' . $examen->variante->nombre ?></h2>
            </div>
            <div class="mitad-der">
                <a href="<?= Url::to(['exams-academic/delete', 'id' => $examen->id]) ?>" class="btn-oxford boton-peque btn-rojo" id="delete-exam">Delete</a>
                <a href="javascript:;" class="btn-oxford boton-peque" id="edit-exam">Edit</a>
            </div>
        </div>
    </section>

    <section id="view-exam-acad" class="ver">
        <div class="container" id="datos-examen">
            <p class="col-md-6 nombre-campo">Test name:</p><p class="col-md-6"><?= $examen->nivelAlumno->nombre . ' ' . $examen->tipoExamen->nombre . ' ' . $examen->variante->nombre ?></p>
            <?php if($examen->tipo_examen_id == '1'){ ?>
                <?php
                    if($examen->diagnostic_v2 == '1'){
                        $version = 'V2';
                    }else if($examen->diagnostic_v3 == '1'){
                        $version = 'V3';
                    }else{
                        $version = 'V1';
                    }
                ?>
                <p class="col-md-6 nombre-campo">Diagnostic Version:</p><p class="col-md-6"><?= $version ?></p>
            <?php } ?>
            <p class="col-md-6 nombre-campo">Level:</p><p class="col-md-6"><?= $examen->nivelAlumno->nombre ?></p>
            <p class="col-md-6 nombre-campo">Exam Type:</p><p class="col-md-6"><?= $examen->tipoExamen->nombre ?></p>
            <p class="col-md-6 nombre-campo">Version:</p><p class="col-md-6"><?= $examen->variante->nombre ?></p>
            <!-- <p class="col-md-6 nombre-campo">Description:</p><p class="col-md-6">Lorem ipsum dolor sit amet consectetur adipisicing elit. Aliquam debitis veniam dicta dolorem impedit voluptates omnis, asperiores commodi facilis obcaecati ea, eaque distinctio minima perspiciatis cupiditate reiciendis dolorum iste ab?</p> -->
            <p class="col-md-6 nombre-campo">Test Passing Criteria Percentage:</p><p class="col-md-6"><?= $examen->porcentaje ?>%</p>
            <!-- <p class="col-md-6 nombre-campo">Select Section:</p><p class="col-md-6">A2</p> -->
            <p class="col-md-6 nombre-campo">Total Duration:</p><p class="col-md-6"><?= $total ?> minutes</p>
            <!-- <p class="col-md-6 nombre-campo">Unique Points Available in Sections:</p><p class="col-md-6">15</p> -->
            <p class="col-md-6 nombre-campo">Total Points:</p><p class="col-md-6"><?= $examen->puntos ?></p>
            <div class="clear"></div>
            <?php
            if($examen->tipoExamen->clave == 'MOC'){
                if($audios){ ?>
                <div class="agrega-seccion">
                    <?php $formListening = ActiveForm::begin(); ?>
                        <?= $formListening->field($listeningMock, 'examen')->hiddenInput()->label(false) ?>
                        <?=
                            $formListening->field($listeningMock, 'seccion')->dropDownList($audios,['prompt'=>'Listening'])->label(false);
                        ?>
                        <div class="form-group">
                            <?= Html::submitButton('Guardar', ['class' => 'btn btn-oxford']) ?>
                        </div>
                        <div class="clear"></div>
                    <?php ActiveForm::end(); ?>
                </div>
            <?php } ?>
            <?php if($articulos){ ?>
                <div class="agrega-seccion">
                    <?php $formReading = ActiveForm::begin(); ?>
                        <?= $formReading->field($readingMock, 'examen')->hiddenInput()->label(false) ?>
                        <?=
                            $formReading->field($readingMock, 'seccion')->dropDownList($articulos,['prompt'=>'Readings'])->label(false);
                        ?>
                        <div class="form-group">
                            <?= Html::submitButton('Guardar', ['class' => 'btn btn-oxford']) ?>
                        </div>
                        <div class="clear"></div>
                    <?php ActiveForm::end(); ?>
                </div>
            <?php }
            }?>
            <?php
            if($examen->tipoExamen->clave == 'CER'){
                if($audios){ ?>
                <div class="agrega-seccion">
                    <?php $formListening = ActiveForm::begin(); ?>
                        <?= $formListening->field($listeningCertificate, 'examen')->hiddenInput()->label(false) ?>
                        <?=
                            $formListening->field($listeningCertificate, 'seccion')->dropDownList($audios,['prompt'=>'Listening'])->label(false);
                        ?>
                        <div class="form-group">
                            <?= Html::submitButton('Guardar', ['class' => 'btn btn-oxford']) ?>
                        </div>
                        <div class="clear"></div>
                    <?php ActiveForm::end(); ?>
                </div>
            <?php } ?>
            <?php if($articulos){ ?>
                <div class="agrega-seccion <?= $examen->nivelAlumno->clave == 'B2' ? 'doble' : '' ?>">
                    <?php $formReading = ActiveForm::begin(); ?>
                        <?= $formReading->field($readingCertificate, 'examen')->hiddenInput()->label(false) ?>
                        <?=
                            $formReading->field($readingCertificate, 'seccion')->dropDownList($articulos,['prompt'=>'Readings'])->label(false);
                        ?>
                        <?php if($examen->nivelAlumno->clave == 'B2'){ ?>
                            <div class="clear" id="segundo-reading">
                                <?=
                                    $formReading->field($readingCertificate, 'seccion2')->dropDownList($articulos,['prompt'=>'Readings'])->label(false);
                                ?>
                            </div>
                        <?php } ?>
                        <div class="form-group">
                            <?= Html::submitButton('Guardar', ['class' => 'btn btn-oxford']) ?>
                        </div>
                        <div class="clear"></div>
                    <?php ActiveForm::end(); ?>
                </div>
            <?php } ?>
            <?php if($writings){ ?>
                <div class="agrega-seccion">
                    <?php $formWriting = ActiveForm::begin(); ?>
                        <?= $formWriting->field($writingCertificate, 'examen')->hiddenInput()->label(false) ?>
                        <?=
                            $formWriting->field($writingCertificate, 'reactivo')->dropDownList($writings,['prompt'=>'Writing'])->label(false);
                        ?>
                        <div class="form-group">
                            <?= Html::submitButton('Guardar', ['class' => 'btn btn-oxford']) ?>
                        </div>
                        <div class="clear"></div>
                    <?php ActiveForm::end(); ?>
                </div>
            <?php } ?>
        <?php }?>
         </div>
        <?php
        $secciones = $examen->seccions;
        foreach ($secciones as $seccion) { ?>
            <?php $reactivos = $seccion->reactivosActivos(); ?>
            <?php if(count($reactivos) > 0){ ?>
            <div class="container horizontal-separator"></div>
                <div class="container">
                    <div class="datos-seccion">
                        <div>
                            <span class="nombre-campo">Section name:</span>
                            <span><?= $seccion->tipoSeccion->nombre ?></span>
                        </div>
                        <!-- <p class="col-md-6 nombre-campo">Duration:</p><p class="col-md-6"><?php // echo $seccion->duracion ?></p> -->
                        <div>
                            <span class="nombre-campo">Points:</span>
                            <span><?= $seccion->puntos_seccion ?></span>
                        </div>
                        <div>
                            <a class="btn-oxford boton-peque ajax-render-link" href="<?= Url::to(['exams-academic/update-section-form', 'seccion' => $seccion->id]) ?>">Edit</a>
                        </div>
                        <span class="clear"></span>
                    </div>
            <?php
            $helper = false;
            foreach ($reactivos as $i => $reactivo) { ?>
                <div class="detalles-reactivo">
                <?php
                if (!$helper) {
                    if ($reactivo->articulo_id) { ?>
                        <div class="articulo" id="articulo-<?= $reactivo->articulo_id ?>">
                            <div class="bloque-reading-editar">
                                <a href="<?= Url::to(['exams-academic/update-reading-form','id' => $reactivo->articulo_id]) ?>" class="btn-oxford boton-peque update-reading">Edit</a>
                            </div>
                            <div style="clear:both;"></div>
                            <div class="titulo"><?= $reactivo->articulo->titulo ?></div>
                            <div class="article"><?= nl2br($reactivo->articulo->texto) ?></div>
                            <?php $imagen = $reactivo->articulo->imagen ?>
                            <?php if($imagen){ ?>
                                <div id="contenedor-imagen-<?= $reactivo->articulo->id ?>">
                                    <?= Html::img('@web/'.$imagen,['class' => 'imagen-reading']) ?>
                                </div>
                            <?php } ?>
                        </div>
                    <?php }
                    if ($reactivo->audio_id) { ?>
                        <div class="audio" id="audio-<?= $reactivo->audio_id ?>">
                            <div class="bloque-audio-editar">
                                <a href="<?= Url::to(['exams-academic/update-audio-form','id' => $reactivo->audio_id]) ?>" class="btn-oxford boton-peque update-audio">Edit</a>
                            </div>
                            <div class="titulo"><?= $reactivo->audio->nombre ?></div>
                            <audio controls>
                                <source src="<?php echo Url::to('@web/' . $reactivo->audio->audio) ?>" type="audio/mpeg">
                            </audio>
                        </div>
            <?php }
                $helper = true;
                ?>
                <div class="datos-seccion instructions">
                    <p><?= isset($seccion->instrucciones_generales) ? $seccion->instrucciones_generales : 'No Description' ?></p>
                </div>
            <?php } ?>
            <p><?= $reactivo->instrucciones ?></p>
            <p class="pregunta"><?= $i+1 ?>.- Q: <?= $reactivo->pregunta ?></p>

            <?php foreach($reactivo->imagenes() as $img_reactivo){ ?>
                    <?= Html::img('@web/'.$img_reactivo->imagen,['class' => 'imagen-reading']) ?>
            <?php } ?>
            <?php
            if ($reactivo->tipoReactivo->clave == 'MUL' || $reactivo->tipoReactivo->clave == 'CAM' || $reactivo->tipoReactivo->clave == 'ART' || $reactivo->tipoReactivo->clave == 'AUD') {
                $respuestas = $reactivo->respuestas;
                $contador_respuesta = 'a';
                foreach ($respuestas as $respuesta) { ?>
                    <?php if($respuesta->respuesta){ ?>
                        <p class="<?= $respuesta->correcto == 1 ? 'correcto-resp' : ''  ?>">
                            <?= $contador_respuesta.') '.$respuesta->respuesta ?>
                        </p>
                    <?php } ?>
                    <?php $contador_respuesta++ ?>
                <?php } ?>
            <?php } ?>

            <?php
            if ($reactivo->tipoReactivo->clave == 'REL') {
                foreach ($reactivo->enunciadoColumns as $enunciado) { ?>
                    <div class="relacionar">
                        <span><?= $enunciado->enunciado ?></span>
                        -
                        <?= $enunciado->respuestaColumn->respuesta ?>
                    </div>
                <?php }
            }
            ?>
                </div>
            <?php

        } ?>
            </div>
        <?php
        }
    } ?>
    </section>

    <section id="view-exam-acad" class="editar oculto">
        <?php echo $this->render('_edit-exam',[
            'examForm' => $examForm,
            'tipos'=>$tipos,
            'niveles'=>$niveles,
            'versiones'=>$versiones,
            ]) ?>
    </section>

</div>
