<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;

use backend\models\forms\UseOfEnglishForm;
use app\models\TipoReactivo;
use app\models\Examen;

$useForm = new UseOfEnglishForm();
?>

<div class="add-use">
<?php $form = ActiveForm::begin(['id' => 'use-add-form', 'action' => ['questions/add-use']]); ?>

    <div class="instituto-fields">
        <div class="row">
            <!-- <div class="col-md-4">
                <?php //echo $form->field($useForm, 'nivel')->dropDownList($niveles, ['prompt' => 'Select']) ?>
            </div> -->
            <div class="col-md-6">
                <?php echo $form->field($useForm, 'examen')->dropDownList($examenes, ['prompt' => 'Select']) ?>
            </div>
            <div class="col-md-6">
                <?php echo $form->field($useForm, 'puntos')->textInput() ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <?= $form->field($useForm, 'general_instructions')->textarea() ?>
            </div>
        </div>

        <div class="preguntas use">
            <div class="principal row">
                <div class="row">
                    <div class="col-md-12">
                        <?= $form->field($useForm, 'instrucciones[]')->textArea(['rows'=>'1']) ?>
                    </div>
                    <div class="col-md-12">
                        <?= $form->field($useForm, 'pregunta[]')->textArea(['rows'=>'1']) ?>
                    </div>
                </div>
                <div class="col-md-12">
                    <?php
                    echo $form->field($useForm, 'tipos[]')->dropDownList(ArrayHelper::map(TipoReactivo::find()->where('tipo_reactivo.clave != "WRI"')->all(), 'clave', 'nombre'), ['class' => 'select-tipo-react', 'prompt' => 'Select']);
                    ?>
                </div>
                <div class="pregunta-com oculto">
                    <h2 class="titulo-respuestas">Answers <a href="javascript:;" class="add-awnser">(add more)</a></h2>
                    <div class="row">
                        <div class="col-md-6">
                            <?= $form->field($useForm, 'respuestasCompletar[0][]')->textInput()->label(false) ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($useForm, 'respuestasCompletar[0][]')->textInput()->label(false) ?>
                        </div>
                     </div>
                     <div class="row more-awnsers"></div>
                </div>
                <div class="pregunta-mult oculto">
                    <div class="row">
                        <div class="col-md-1">
                        <?= $form->field($useForm, 'correctosMul[0]')->radioList(['a' => '', 'b' => '', 'c' => '']); ?>
                        </div>
                        <div class="col-md-11">
                            <?= $form->field($useForm, 'respuestasMultiple[]')->textInput() ?>
                            <?= $form->field($useForm, 'respuestasMultiple[]')->textInput() ?>
                            <?= $form->field($useForm, 'respuestasMultiple[]')->textInput() ?>
                        </div>
                     </div>
                </div>
                <div class="rel-columna oculto">
                    <div class="row">
                        <div class="col-md-6">
                            <?= $form->field($useForm, 'enunciados[]')->textInput() ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($useForm, 'respuestasColumna[]')->textInput() ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <?= $form->field($useForm, 'enunciados[]')->textInput() ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($useForm, 'respuestasColumna[]')->textInput() ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <?= $form->field($useForm, 'enunciados[]')->textInput() ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($useForm, 'respuestasColumna[]')->textInput() ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <?= $form->field($useForm, 'enunciados[]')->textInput() ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($useForm, 'respuestasColumna[]')->textInput() ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <?= $form->field($useForm, 'enunciados[]')->textInput() ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($useForm, 'respuestasColumna[]')->textInput() ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <a href="javascript:;" class="btn-add-question use">+ Add question</a>

        <div class="form-group col-md-12" id="div-guardar">
            <?= Html::submitButton('Save', ['class' => 'btn-oxford boton-peque']) ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>
