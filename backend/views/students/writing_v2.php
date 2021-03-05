<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;

$this->title = 'Writing';
?>

<div class="writing">
    <section id='inicio-writing' class='inicio'>
        <div class="container">
            <h2>Writing Section</h2>
        </div>
    </section>
    <input type="hidden" id="tiempo-writing" value="<?= $tiempo ?>" />
    <input type="hidden" id="tiempo-writing-used" value="<?= ($tiempo_usado ? $tiempo_usado : 0) ?>" />
    <section class="body-writing">
        <div class="container">
            <?php
                    if($writing->status == 1){    ?>
                        <div class="instruccion">
                            <?= $writing->instrucciones ?>
                        </div>
                        <div class="pregunta">
                            <?= $writing->pregunta ?>
                        </div>
                        <?php foreach($imagenes as $imagen){ ?>
                            <div class="imagen-reading">
                                <a class="zoom-reading" href="javascript:;" data-section="<?= $imagen->id ?>">
                                    <?= Html::img('@web/'.$imagen->imagen) ?>
                                </a>
                                <div id="imagen-reading-<?= $imagen->id ?>" class="mfp-hide">
                                    <?= Html::img('@web/'.$imagen->imagen) ?>
                                </div>
                            </div>
                        <?php } ?>
                        <?php $form = ActiveForm::begin(['id' => 'solve-writing-form', 'action' => 'save-writing']); ?>
                            <?php echo Html::hiddenInput('WritingResueltoForm[id]', $alumnoExamen->id, ['id'=>'alumno_examen']); ?>
                            <?php echo Html::hiddenInput('WritingResueltoForm[reactivo]', $writing->id, ['id'=>'reactivo']); ?>
                            <div class="row">
                                <div class="col-md-10"></div>
                                <div class="col-md-2">
                                    <p><span id="word-counter">0</span> words</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <?= $form->field($writingForm, 'texto')->textArea(['rows' => '6', 'data-gramm' => 'false', 'spellcheck' => 'false', 'autocorrect' => 'off', 'id'=>"solve-writing-field"]) ?>
                                </div>
                            </div>

                            <div class="form-group col-md-12" id="div-guardar">
                                <button type="button" class="btn-oxford boton-peque" id="send-writing-btn">Submit</button>
                            </div>
                        <?php ActiveForm::end(); ?>
                <?php }
            ?>
        </div>
    </section>
</div>

<div id="countdown-timer"><span id="time"></span></div>

<div id="time-dialog" class="mfp-hide white-popup-block">
    <h2>TIME OVER!</h2>
    <p>Your time for this section has finished. Click on continue to skip to end test.</p>
    <a class="popup-modal-dismiss-writing btn-oxford warning-writing-btn">Continue</a>
</div>
<div id="warning-dialog" class="mfp-hide white-popup-block">
    <h2>Have you finished?</h2>
    <p>Please click ‘Submit’ if you have finished your test. You will not be able to return to this page.</p>
    <div class="row">
        <div class="col-md-12 text-center">
            <button class="btn-oxford warning-writing-btn">Submit</button>
        </div>
    </div>
</div>
