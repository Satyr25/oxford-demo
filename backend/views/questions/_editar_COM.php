<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
?>
<?php $form = ActiveForm::begin([]); ?>

    <div class="row">
        <div>
            <?= $form->field($reactivoForm, 'instrucciones')->textArea() ?>
        </div>
        <div>
            <?= $form->field($reactivoForm, 'pregunta')->textArea() ?>
        </div>
        <div>
            <h2 class="titulo-respuestas">Answers <a href="javascript:;" id="agregar-respuesta">(add more)</a></h2>
            <div id="respuestas-guardadas">
                <div clas="row">
                    <?php foreach($reactivoForm->respuestas_guardadas as $respuesta){ ?>
                        <div class="col-md-6">
                            <?= $form->field($reactivoForm, 'respuestas[]')->textInput(['value' => $respuesta])->label(false)  ?>
                        </div>
                    <?php } ?>
                </div>
                <div class="row" id="nuevas-respuestas">
                </div>
            </div>
        </div>
    </div>

    <div class="form-group col-md-12" id="div-guardar">
        <?= Html::submitButton('Save', ['class' => 'btn-oxford boton-peque']) ?>
    </div>

<?php ActiveForm::end(); ?>
