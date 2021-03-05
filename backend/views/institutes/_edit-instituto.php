<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use kartik\depdrop\DepDrop;
use app\models\Estado;
use kartik\date\DatePicker;

$estado = Estado::findOne($institutoForm->estado);
?>

<div class='edit-info oculto'>
    <div>
        <?php $form = ActiveForm::begin([
            'id' => 'edit-instituto-form',
            'action' => 'edit-instituto',
            'enableClientValidation'=>true,
        ]) ?>
        <?= Html::hiddenInput('InstitutoForm[id]', $colegio->id); ?>
        <div class="half-div tabla-datos">
            <div class="separa-datos">
                <p class="nombre-dato">Institute Name*:</p><?php echo $form->field($institutoForm, 'nombre')->textInput()->label(false); ?>
            </div>
            <div class="separa-datos">
                <p class="nombre-dato">Program:</p><?php echo $form->field($institutoForm, 'programa')->dropDownList($programas, ['prompt' => 'Select'])->label(false) ?>
            </div>
            <div class="separa-datos">
                <p class="nombre-dato">Email*:</p><?php echo $form->field($institutoForm, 'email')->textInput()->label(false); ?>
            </div>
            <div class="separa-datos">
                <p class="nombre-dato">Password:</p><?php echo $form->field($institutoForm, 'password')->passwordInput()->label(false); ?>
            </div>
            <div class="separa-datos">
                <p class="nombre-dato">Adress line 1:</p>
                <?php
                    echo $form->field($institutoForm, 'calle')->textInput(['placeholder'=>'Calle'])->label(false);
                    echo $form->field($institutoForm, 'numero_int')->textInput(['placeholder'=>'Int. No.'])->label(false);
                    echo $form->field($institutoForm, 'numero_ext')->textInput(['placeholder'=>'Ext. No.'])->label(false);
                ?>
            </div>
            <div class="separa-datos">
                <p class="nombre-dato">Adress line 2:</p><?php echo $form->field($institutoForm, 'colonia')->textInput(['placeholder'=>'Colonia'])->label(false); ?>
            </div>
            <div class="separa-datos">
                <p class="nombre-dato">Adress line 3:</p><?php echo $form->field($institutoForm, 'municipio')->textInput(['placeholder'=>'Municipio'])->label(false); ?>
            </div>
            <div class="separa-datos">
                <p class="nombre-dato">City:</p><?php echo $form->field($institutoForm, 'ciudad')->textInput()->label(false); ?>
            </div>
            <div class="separa-datos">
                <p class="nombre-dato">Country:</p><?php echo $form->field($institutoForm, 'pais')->dropDownList($paises, ['prompt' => 'Select', 'id' => 'paises-drop'])->label(false) ?>
            </div>
            <div class="separa-datos">
               <p class="nombre-dato">State</p><?php echo $form->field($institutoForm, 'estado')->widget(DepDrop::classname(), [
                   'data'=>[$estado->id => $estado->estadonombre],
                    'pluginOptions' => [
                        'depends' => ['paises-drop'],
                        'placeholder' => 'Select...',
                        'url' => Url::to(['/institutes/subpais'])
                    ]
                ])->label(false) ?>
            </div>
            <div class="separa-datos">
                <p class="nombre-dato">Region:</p><?php echo $form->field($institutoForm, 'region')->dropDownList($regiones)->label(false); ?>
            </div>
            <div class="separa-datos">
                <p class="nombre-dato">Pruebas:</p><?php echo $form->field($institutoForm, 'pruebas')->checkbox(null, false)->label(false); ?>
            </div>
         </div><!--
             --><div class="half-div tabla-datos">
            <div class="separa-datos">
                <p class="nombre-dato">Zipcode:</p><?php echo $form->field($institutoForm, 'codigo_postal')->textInput()->label(false); ?>
            </div>
            <div class="separa-datos">
                <p class="nombre-dato">Reference:</p><?php echo $form->field($institutoForm, 'referencia')->textInput()->label(false); ?>
            </div>
            <div class="separa-datos">
                <p class="nombre-dato">Phone*:</p><?php echo $form->field($institutoForm, 'telefono')->textInput()->label(false); ?>
            </div>
            <div class="separa-datos ultimo-dato">
                <!-- <p class="nombre-dato">Status:</p><p> -->
                    <?php
                        // if ($colegio->status) {
                        //     echo "Activo";
                        // } else {
                        //     echo "Inactivo";
                        // }
                    ?>
            <!-- </p> -->
            </div>
            <h4 class="nombre-dato">Contact Details</h4>
            <div class="separa-datos">
                <p class="nombre-dato">Contact Name*:</p><?php echo $form->field($institutoForm, 'nombre_contacto')->textInput()->label(false); ?>
            </div>
            <div class="separa-datos ultimo-dato">
                <p class="nombre-dato">Contact Email*:</p><?php echo $form->field($institutoForm, 'email_contacto')->textInput()->label(false); ?>
            </div>
            <h4 class="nombre-dato">Dates</h4>
            <div class="separa-datos">
                <p class="nombre-dato">Diagnostic Date:</p>
                <?= $form->field($institutoForm, 'diagnosticDate')->widget(DatePicker::className(), [
                    'pluginOptions' => [
                        'autoclose' => true,
                        'format' => 'dd/mm/yyyy'
                    ]
                ]) -> label(false) ?>
            </div>
            <?php if ($colegio->programa->clave == "CLI"): ?>
            <div class="separa-datos">
                <p class="nombre-dato">Mock Date:</p>
                <?= $form->field($institutoForm, 'mockDate')->widget(DatePicker::className(), [
                    'pluginOptions' => [
                        'autoclose' => true,
                        'format' => 'dd/mm/yyyy'
                    ]
                ]) -> label(false) ?>
            </div>
            <div class="separa-datos">
                <p class="nombre-dato">Speaking Date:</p>
                <?= $form->field($institutoForm, 'speakingDate')->widget(DatePicker::className(), [
                    'pluginOptions' => [
                        'autoclose' => true,
                        'format' => 'dd/mm/yyyy'
                    ]
                ]) -> label(false) ?>
            </div>
            <?php endif; ?>
            <div class="separa-datos">
                <p class="nombre-dato">Certificate Date:</p>
                <?= $form->field($institutoForm, 'certificateDate')->widget(DatePicker::className(), [
                    'pluginOptions' => [
                        'autoclose' => true,
                        'format' => 'dd/mm/yyyy'
                    ]
                ]) -> label(false) ?>
            </div>
            <?php if ($colegio->programa->clave != "CLI"): ?>
            <div class="separa-datos">
                <p class="nombre-dato">Round:</p>
                <?= $form->field($institutoForm, 'ronda')->dropDownList(
                    ['A' => 'A', 'B' => 'B', 'C' => 'C'],
                    ['prompt' => 'Select...']
                )->label(false) ?>
            </div>
            <?php endif; ?>
        </div>
        <div class="col-md-12 row" id="div-guardar">
            <?= Html::submitButton('Save', ['class' => 'btn-oxford boton-peque']) ?>
            <?php ActiveForm::end() ?>
        </div>
    </div>
</div>
