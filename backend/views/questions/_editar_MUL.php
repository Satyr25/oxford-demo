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
        <div>
            <div id="reactivomulform-correcta" class="columna-correcta">
                <?php foreach($reactivoForm->respuestas_id as $i => $respuesta_id){ ?>
                    <div class="radio">
                        <label>
                            <input name="ReactivoMULForm[correcta]" value="<?= $respuesta_id ?>" type="radio" <?= $respuesta_id == $reactivoForm->correcta ? 'checked' : '' ?>>
                        </label>
                    </div>
                <?php } ?>
            </div>
        </div>
        <div class="columna-respuestas">
            <?php foreach($reactivoForm->respuestas as $i => $respuesta){ ?>
                <?= $form->field($reactivoForm, 'respuestas['.$i.']')->textInput()->label(false) ?>
                <?= $form->field($reactivoForm, 'respuestas_id_ligadas['.$i.']')->hiddenInput()->label(false) ?>
            <?php } ?>
        </div>
    </div>

    <div class="form-group col-md-12" id="div-guardar">
        <?= Html::submitButton('Save', ['class' => 'btn-oxford boton-peque']) ?>
    </div>

<?php ActiveForm::end(); ?>
