<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
?>
<?php $form = ActiveForm::begin([]); ?>

    <div class="row">
        <?php if($reactivoForm->reading){ ?>
            <div>
                <?= $form->field($reactivoForm, 'reading')->dropDownList($reactivoForm->articulos, ['prompt' => 'Select One' ]); ?>
            </div>
        <?php } ?>
        <?php if($reactivoForm->listening){ ?>
            <div>
                <?= $form->field($reactivoForm, 'listening')->dropDownList($reactivoForm->audios, ['prompt' => 'Select One' ]); ?>
            </div>
        <?php } ?>
        <div>
            <?= $form->field($reactivoForm, 'instrucciones')->textArea() ?>
        </div>
        <div>
            <?= $form->field($reactivoForm, 'pregunta')->textArea() ?>
        </div>
    </div>
    <div class="row">
        <?php foreach($reactivoForm->enunciados_id as $i => $enunciado_id){ ?>
            <div class="enunciado-respuesta row">
                <div class="enunciado">
                    <?= $form->field($reactivoForm, 'enunciados['.$i.']')->textInput()->label(false) ?>
                        <?= $form->field($reactivoForm, 'enunciados_id['.$i.']')->hiddenInput()->label(false) ?>
                </div>
                <div class="respuesta">
                    <?= $form->field($reactivoForm, 'respuestas['.$i.']')->textInput()->label(false) ?>
                    <?= $form->field($reactivoForm, 'respuestas_id['.$i.']')->hiddenInput()->label(false) ?>
                </div>
            </div>
        <?php } ?>
    </div>

    <div class="form-group col-md-12" id="div-guardar">
        <?= Html::submitButton('Save', ['class' => 'btn-oxford boton-peque']) ?>
    </div>

<?php ActiveForm::end(); ?>
