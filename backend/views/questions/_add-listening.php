<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;

use backend\models\forms\ListeningForm;
use app\models\TipoReactivo;
use app\models\Examen;

$audioForm = new ListeningForm();
?>

<div class="add-listening oculto">
<?php $form = ActiveForm::begin(['id' => 'listening-add-form', 'action' => ['questions/add-listening']]); ?>

    <div class="instituto-fields">
        <div class="row">
            <!-- <div class="col-md-4">
                <?php //echo $form->field($audioForm, 'nivel')->dropDownList($niveles, ['prompt' => 'Select']) ?>
            </div> -->
            <div class="col-md-6">
                <?php echo $form->field($audioForm, 'examen')->dropDownList($examenes, ['prompt' => 'Select']) ?>
            </div>
            <div class="col-md-6">
                <?php echo $form->field($audioForm, 'puntos')->textInput() ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <?= $form->field($audioForm, 'general_instructions')->textarea() ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <?= $form->field($audioForm, 'audio_guardado')->dropDownList($audios, ['prompt' => 'New Audio' ]); ?>
            </div>
        </div>
        <div id="audio-title"></div>
        <div class="row">
           <div class="col-md-12">
                <?= $form->field($audioForm, 'nombre')->textInput() ?>
            </div>
            <div class="col-md-12">
                <?= $form->field($audioForm, 'audio')->fileInput() ?>
            </div>
        </div>

        <div class="preguntas list">
            <div class="principal row">
                <div class="row">
                    <div class="col-md-12">
                        <?= $form->field($audioForm, 'instrucciones[]')->textArea(['rows' => '1']) ?>
                    </div>
                    <div class="col-md-12">
                        <?= $form->field($audioForm, 'pregunta[]')->textArea(['rows' => '1']) ?>
                    </div>
                </div>
                <div class="col-md-12">
                    <?php
                    echo $form->field($audioForm, 'tipos[]')->dropDownList(ArrayHelper::map(TipoReactivo::find()->where('tipo_reactivo.clave != "WRI"')->all(), 'clave', 'nombre'), ['class' => 'select-tipo-react', 'prompt' => 'Select']);
                    ?>
                </div>
                <div class="pregunta-com oculto">
                    <h2 class="titulo-respuestas">Answers <a href="javascript:;" class="add-awnser">(add more)</a></h2>
                    <div class="row">
                        <div class="col-md-6">
                            <?= $form->field($audioForm, 'respuestasCompletar[0][]')->textInput()->label(false) ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($audioForm, 'respuestasCompletar[0][]')->textInput()->label(false) ?>
                        </div>
                     </div>
                     <div class="row more-awnsers"></div>
                </div>
                <div class="pregunta-mult oculto">
                    <div class="row">
                        <div class="col-md-1">
                        <?= $form->field($audioForm, 'correctosMul[0]')->radioList(['a' => '', 'b' => '', 'c' => '']); ?>
                        </div>
                        <div class="col-md-11">
                            <?= $form->field($audioForm, 'respuestasMultiple[]')->textInput() ?>
                            <?= $form->field($audioForm, 'respuestasMultiple[]')->textInput() ?>
                            <?= $form->field($audioForm, 'respuestasMultiple[]')->textInput() ?>
                        </div>
                     </div>
                </div>
                <div class="rel-columna oculto">
                    <div class="row">
                        <div class="col-md-6">
                            <?= $form->field($audioForm, 'enunciados[]')->textInput() ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($audioForm, 'respuestasColumna[]')->textInput() ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <?= $form->field($audioForm, 'enunciados[]')->textInput() ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($audioForm, 'respuestasColumna[]')->textInput() ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <?= $form->field($audioForm, 'enunciados[]')->textInput() ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($audioForm, 'respuestasColumna[]')->textInput() ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <?= $form->field($audioForm, 'enunciados[]')->textInput() ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($audioForm, 'respuestasColumna[]')->textInput() ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <?= $form->field($audioForm, 'enunciados[]')->textInput() ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($audioForm, 'respuestasColumna[]')->textInput() ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <a href="javascript:;" class="btn-add-question list">+ Add question</a>

        <div class="form-group col-md-12" id="div-guardar">
            <?= Html::submitButton('Save', ['class' => 'btn-oxford boton-peque']) ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>
