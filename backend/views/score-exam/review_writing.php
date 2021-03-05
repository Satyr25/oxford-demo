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
            <h2>Score Review</h2>
        </div>
    </section>

    <section id="body-score-writing">
        <div class="container">
            <div class="datos-seccion review">
                <div>
                    <span class="nombre-campo">Code:</span>
                    <br>
                    <span><?= $aluexa->alumnoExamen->alumno->users[0]->codigo ?></span>
                </div>
                <div>
                    <span class="nombre-campo">Exam:</span>
                    <br>
                    <span><?= $aluexa->alumnoExamen->examen->tipoExamen->nombre ?> <?= $aluexa->alumnoExamen->examen->certificate_v2 == 1 ? 'V2' : '' ?></span>
                </div>
                <div>
                    <span class="nombre-campo">Academic:</span>
                    <br>
                    <span><?= $academico->nombre.' '.$academico->apellidos ?></span>
                </div>
                <div>
                    <span class="nombre-campo">Score:</span>
                    <br>
                    <span><?= $aluexa->alumnoExamen->calificaciones->calificacionWriting ?></span>
                </div>
                <div>
                    <?php
                        if($aluexa->alumnoExamen->calificaciones->fecha_calificacion){
                            $fecha = new DateTime();
                            $fecha->setTimestamp($aluexa->alumnoExamen->calificaciones->fecha_calificacion);
                        }
                    ?>
                    <span class="nombre-campo">Scoring Date:</span>
                    <br>
                    <span>
                        <?php if($aluexa->alumnoExamen->calificaciones->fecha_calificacion){ ?>
                            <?= $fecha->format('M d, Y') ?>
                        <?php } ?>
                    </span>
                </div>
            </div>
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
                </div>
            </div>
        </div>
    </section>
</div>
