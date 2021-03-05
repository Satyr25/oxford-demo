<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;

use backend\models\forms\WritingForm;
use app\models\TipoReactivo;
use app\models\Examen;

$writingForm = new WritingForm();
?>

<div class="add-writing oculto">
<?php $form = ActiveForm::begin(['id' => 'writing-add-form', 'action' => ['questions/add-writing','options' => ['enctype' => 'multipart/form-data']]]); ?>

    <div class="instituto-fields">
        <div class="row">
            <!-- <div class="col-md-4">
                <?php //echo $form->field($writingForm, 'nivel')->dropDownList($niveles, ['prompt' => 'Select']) ?>
            </div> -->
            <div class="col-md-6">
                <?php echo $form->field($writingForm, 'examen')->dropDownList($examenes, ['prompt' => 'Select']) ?>
            </div>
            <div class="col-md-6">
                <?php echo $form->field($writingForm, 'puntos')->textInput() ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <?= $form->field($writingForm, 'general_instructions')->textarea() ?>
            </div>
        </div>
        <div id="vista_anterior" style="display:none;">
            <h2>Saved Question</h2>
            <div id="pregunta_anterior"></div>
            <div id="instruccion_anterior"></div>
            <a href="javascript:" id="capturar-nueva" class="btn btn-oxford">Add new question</a>
        </div>

        <div class="preguntas wri">
            <div class="principal row">
                <div class="row">
                    <div class="col-md-12">
                        <?= $form->field($writingForm, 'instrucciones')->textArea(['rows' => '1']) ?>
                    </div>
                    <div class="col-md-12">
                        <?= $form->field($writingForm, 'pregunta')->textArea(['rows' => '1']) ?>
                    </div>
                    <div class="col-md-12">
                        <div id="imagen-writing-1" class="imagen-writing">
                            <?= $form->field($writingForm, 'imagenes[]')->fileInput(['multiple' => true, 'accept' => 'image/*']) ?>
                        </div>
                        <div id="imagen-writing-2" class="imagen-writing" style="display:none;">
                            <?= $form->field($writingForm, 'imagenes[]')->fileInput(['multiple' => true, 'accept' => 'image/*']) ?>
                        </div>
                        <div id="imagen-writing-3" class="imagen-writing" style="display:none;">
                            <?= $form->field($writingForm, 'imagenes[]')->fileInput(['multiple' => true, 'accept' => 'image/*']) ?>
                        </div>
                        <div id="imagen-writing-4" class="imagen-writing" style="display:none;">
                            <?= $form->field($writingForm, 'imagenes[]')->fileInput(['multiple' => true, 'accept' => 'image/*']) ?>
                        </div>
                        <div id="imagen-writing-5" class="imagen-writing" style="display:none;">
                            <?= $form->field($writingForm, 'imagenes[]')->fileInput(['multiple' => true, 'accept' => 'image/*']) ?>
                        </div>
                        <a href="javascript:;" class="boton-add" id="add-img-writing">+ ADD IMAGE</a>
                    </div>
                </div>

        <div class="form-group col-md-12" id="div-guardar">
            <?= Html::submitButton('Save', ['class' => 'btn-oxford boton-peque']) ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>
