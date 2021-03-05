<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;


$this->title = 'Writing';
echo Html::hiddenInput('name', Yii::$app->controller->id, ['class' => 'controller']);
?>

<div class="score-writing-stud">
    <section id="inicio-score-writing" class="inicio">
        <div class="container">
            <h2>Writing</h2>
        </div>
    </section>

    <section id="body-score-writing">
        <div class="container">
            <div class="datos-seccion cuatro-col">
                <div>
                    <span class="nombre-campo">Level:</span>
                    <span><?= $aluexa->alumnoExamen->examen->nivelAlumno->clave ?></span>
                </div>
                <div>
                    <span class="nombre-campo">Exam:</span>
                    <span><?= $aluexa->alumnoExamen->examen->tipoExamen->nombre ?> V2</span>
                </div>
                <div>
                    <!-- <span class="nombre-campo">Points:</span>
                    <span><?= $aluexa->reactivo->puntos ?></span> -->
                </div>
                <div>
                    <span class="nombre-campo">Code:</span>
                    <span><?= $aluexa->alumnoExamen->alumno->users[0]->codigo ?></span>
                </div>
            </div>
            <?php if($aluexa->alumnoExamen->inactivity){ ?>
            <div id="timed-out" style="margin:0 auto;display:flex;">
                Closed due to inactivity.
            </div>
            <?php } ?>
        </div>
    </section>

    <section id="solved-writing-v2">
        <div class="container">
            <div class="texto-writing">
                <div class="row">
                    <div class="col-md-6 answer">
                        <div class="row">
                            <?php foreach($imagenes as $imagen){ ?>
                                <div class="imagen-writing">
                                    <?= Html::img('@web/'.$imagen->imagen) ?>
                                </div>
                            <?php } ?>
                            <div class="col-md-12">
                                <p class="bold"><?= $aluexa->reactivo->instrucciones ?></p>
                                <p class="bold"><?= $aluexa->reactivo->pregunta ?></p>
                                <p><?= nl2br($aluexa->respuestaWriting) ?></p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-10"></div>
                            <div class="col-md-2">
                                <p>
                                    <span>
                                        <?php
                                        $cadena = preg_replace('/\s+/', ' ', trim($aluexa->respuestaWriting));
                                        $cadenaSeparada = explode(' ', $cadena);
                                        $numeroPalabras = count($cadenaSeparada);
                                        if ($cadenaSeparada[$numeroPalabras - 1] == '') {
                                            $numeroPalabras--;
                                            }
                                        echo $numeroPalabras;
                                        ?>
                                    </span> words
                                </p>
                            </div>
                            <?php if($timedOut){ ?>
                                <div id="timed-out">
                                    Timed Out
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="col-md-6 col-tabla-score">
                        <table id="score-writingv2" class="tabla table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Skill</th>
                                    <th>0</th>
                                    <th>1</th>
                                    <th>2</th>
                                    <th>3</th>
                                    <th>4</th>
                                    <th>5</th>
                                </tr>
                            </thead>
                            <tr>
                                <td>Vocabulary</td>
                                <td>
                                    <input type="radio" class="vocabulary" name="vocabulary" value="0" autocomplete="off" />
                                </td>
                                <td>
                                    <input type="radio" class="vocabulary" name="vocabulary" value="1" autocomplete="off" />
                                </td>
                                <td>
                                    <input type="radio" class="vocabulary" name="vocabulary" value="2" autocomplete="off" />
                                </td>
                                <td>
                                    <input type="radio" class="vocabulary" name="vocabulary" value="3" autocomplete="off" />
                                </td>
                                <td>
                                    <input type="radio" class="vocabulary" name="vocabulary" value="4" autocomplete="off" />
                                </td>
                                <td>
                                    <input type="radio" class="vocabulary" name="vocabulary" value="5" autocomplete="off" />
                                </td>
                            </tr>
                            <tr>
                                <td>Structure</td>
                                <td>
                                    <input type="radio" class="structure" name="structure" value="0" autocomplete="off" />
                                </td>
                                <td>
                                    <input type="radio" class="structure" name="structure" value="1" autocomplete="off" />
                                </td>
                                <td>
                                    <input type="radio" class="structure" name="structure" value="2" autocomplete="off" />
                                </td>
                                <td>
                                    <input type="radio" class="structure" name="structure" value="3" autocomplete="off" />
                                </td>
                                <td>
                                    <input type="radio" class="structure" name="structure" value="4" autocomplete="off" />
                                </td>
                                <td>
                                    <input type="radio" class="structure" name="structure" value="5" autocomplete="off" />
                                </td>
                            </tr>
                            <tr>
                                <td>Grammar</td>
                                <td>
                                    <input type="radio" class="grammar" name="grammar" value="0" autocomplete="off" />
                                </td>
                                <td>
                                    <input type="radio" class="grammar" name="grammar" value="1" autocomplete="off" />
                                </td>
                                <td>
                                    <input type="radio" class="grammar" name="grammar" value="2" autocomplete="off" />
                                </td>
                                <td>
                                    <input type="radio" class="grammar" name="grammar" value="3" autocomplete="off" />
                                </td>
                                <td>
                                    <input type="radio" class="grammar" name="grammar" value="4" autocomplete="off" />
                                </td>
                                <td>
                                    <input type="radio" class="grammar" name="grammar" value="5" autocomplete="off" />
                                </td>
                            </tr>
                        </table>
                        <table id="word-percentaje" class="tabla table-striped table-bordered">
                            <tr>
                                <th>Word Count Percentage</th>
                                <?php $porcentaje = ($numeroPalabras/$words[$aluexa->alumnoExamen->examen->nivelAlumno->clave])*100 ?>
                                <td><?= $porcentaje <= 100 ? number_format($porcentaje,2) : 100 ?>%</td>
                                <input type="hidden" value="<?= $porcentaje <= 100 ? number_format($porcentaje,2) : 100 ?>" id="wcp" />
                                <input type="hidden" id="writing_data" value="<?= $writing_data->id ?>" autocomplete="off" />
                            </tr>
                        </table>
                        <table id="relevance" class="tabla table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th colspan="3">Relevance</th>
                                </tr>
                            </thead>
                            <tr>
                                <td>
                                    <a href="javascript:;" class="relevance" data-percentaje='33.33'> Not relevant</a>
                                </td>
                                <td>
                                    <a href="javascript:;" class="relevance" data-percentaje='66.66'> Quite relevant</a>
                                </td>
                                <td>
                                    <a href="javascript:;" class="relevance" data-percentaje='100%%'> Very relevant</a>
                                </td>
                            </tr>
                        </table>
                        <a href="javascript:;" id="save-v2" class="boton-peque btn-oxford">Finish Scoring</a>
                        <div class="clear"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- <section id="form-score-writing">
        <div class="container">
            <?php echo $this->render('_save-points',[
                'id'=>$aluexa->id,
                'tipo' => $aluexa->alumnoExamen->examen->tipoExamen->clave
            ]); ?>
        </div>
    </section> -->
</div>

<div id="v2-confirm" class="mfp-hide white-popup-block">
    <div>
        <h2>Confirm</h2>
        <p>You are about to grade this exam with  <span id="grade-selected"></span>.</p>
        <a class="btn-oxford" id="submit-v2" href="javascript:;">Submit</a>
        <a class="btn-oxford" id="close-v2" href="javascript:;">Go back</a>
    </div>
</div>
