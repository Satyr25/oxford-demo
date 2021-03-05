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
                    <?php
                        $tipo = '';
                        if($aluexa->alumnoExamen->examen->diagnostic_v2 == 1){
                            $tipo = ' V2';
                        }else if($aluexa->alumnoExamen->examen->diagnostic_v3 == 1){
                            $tipo = ' V3';
                        }
                    ?>
                    <span><?= $aluexa->alumnoExamen->examen->tipoExamen->nombre.$tipo ?></span>
                </div>
                <div>
                    <span class="nombre-campo">Points:</span>
                    <span><?= $aluexa->reactivo->puntos ?></span>
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

    <section id="solved-writing">
        <div class="container">
            <div class="texto-writing">
                <div class="row">
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
        </div>
    </section>

    <section id="form-score-writing">
        <div class="container">
            <?php echo $this->render('_save-points',[
                'id'=>$aluexa->id,
                'tipo' => $aluexa->alumnoExamen->examen->tipoExamen->clave
            ]); ?>
        </div>
    </section>
</div>

<div id="academic-grade-warning" class="mfp-hide white-popup-block">
    <div>
        <h2>Next Exam</h2>
        <p>You are scoring an exam with <span id="grade-selected">X</span> points out of <?= $aluexa->reactivo->seccion->puntos_seccion ?>. If you are sure, please select one of these two options:<br>If you want to modify the score, please click on the X on the right corner to go back to the exam.</p>
        <a class="btn-oxford continue-scoring-button" href="javascript:;">Continue Scoring</a>
        <a class="btn-oxford finish-scoring-button" href="javascript:;">Finish</a>
    </div>
</div>
