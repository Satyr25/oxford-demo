<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;

use backend\models\forms\ReadingForm;
use app\models\Examen;
use app\models\TipoReactivo;

$readingForm = new ReadingForm();
?>

<div class="add-reading oculto">
<?php $form = ActiveForm::begin(['id' => 'reading-add-form', 'action' => ['questions/add-reading']]); ?>

    <div class="instituto-fields">
        <div class="row">
            <!-- <div class="col-md-4">
                <?php //echo $form->field($useForm, 'nivel')->dropDownList($niveles, ['prompt' => 'Select']) ?>
            </div> -->
            <div class="col-md-6">
                <?php echo $form->field($readingForm, 'examen')->dropDownList($examenes, ['prompt' => 'Select']) ?>
            </div>
            <div class="col-md-6">
                <?php echo $form->field($readingForm, 'puntos')->textInput() ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <?= $form->field($readingForm, 'general_instructions')->textarea() ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <?= $form->field($readingForm, 'reading')->dropDownList($articulos, ['prompt' => 'New Article' ]); ?>
            </div>
        </div>
        <div id="article-title"></div>
        <div class="row">
                <div class="col-md-12">
                <?= $form->field($readingForm, 'nombre')->textInput() ?>
            </div>
            <div class="col-md-12">
                <?= $form->field($readingForm, 'texto')->textarea(['rows' => '10']) ?>
            </div>
            <div class="col-md-12">
                <?= $form->field($readingForm, 'imagen')->fileInput() ?>
            </div>
        </div>

        <div class="preguntas read">
            <div class="row principal">
                <div class="row">
                    <div class="col-md-12">
                        <?= $form->field($readingForm, 'instrucciones[]')->textArea(['rows' => '1']) ?>
                    </div>
                    <div class="col-md-12">
                        <?= $form->field($readingForm, 'pregunta[]')->textArea(['rows' => '1']) ?>
                    </div>
                </div>
                <div class="col-md-12">
                    <?php
                    echo $form->field($readingForm, 'tipos[]')->dropDownList(ArrayHelper::map(TipoReactivo::find()->where('tipo_reactivo.clave != "WRI"')->all(), 'clave', 'nombre'), ['class' => 'select-tipo-react', 'prompt' => 'Select']);
                    ?>
                </div>
                <div class="pregunta-com oculto">
                    <h2 class="titulo-respuestas">Answers <a href="javascript:;" class="add-awnser">(add more)</a></h2>
                    <div class="row">
                        <div class="col-md-6">
                            <?= $form->field($readingForm, 'respuestasCompletar[0][]')->textInput()->label(false) ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($readingForm, 'respuestasCompletar[0][]')->textInput()->label(false) ?>
                        </div>
                     </div>
                     <div class="row more-awnsers"></div>
                </div>
                <div class="pregunta-mult oculto">
                    <div class="row">
                        <div class="col-md-1">
                        <?= $form->field($readingForm, 'correctosMul[0]')->radioList(['a' => '', 'b' => '', 'c' => '']); ?>
                        </div>
                        <div class="col-md-11">
                            <?= $form->field($readingForm, 'respuestasMultiple[]')->textInput() ?>
                            <?= $form->field($readingForm, 'respuestasMultiple[]')->textInput() ?>
                            <?= $form->field($readingForm, 'respuestasMultiple[]')->textInput() ?>
                        </div>
                     </div>
                </div>
                <div class="rel-columna oculto">
                    <div class="row">
                        <div class="col-md-6">
                            <?= $form->field($readingForm, 'enunciados[]')->textInput() ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($readingForm, 'respuestasColumna[]')->textInput() ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <?= $form->field($readingForm, 'enunciados[]')->textInput() ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($readingForm, 'respuestasColumna[]')->textInput() ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <?= $form->field($readingForm, 'enunciados[]')->textInput() ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($readingForm, 'respuestasColumna[]')->textInput() ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <?= $form->field($readingForm, 'enunciados[]')->textInput() ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($readingForm, 'respuestasColumna[]')->textInput() ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <?= $form->field($readingForm, 'enunciados[]')->textInput() ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($readingForm, 'respuestasColumna[]')->textInput() ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <a href="javascript:;" class="btn-add-question read">+ Add question</a>

        <div class="form-group col-md-12" id="div-guardar">
            <?= Html::submitButton('Save', ['class' => 'btn-oxford boton-peque']) ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>
