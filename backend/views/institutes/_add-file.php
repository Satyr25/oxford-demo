<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;
?>

<div class='import-group-form oculto' >
    <div class="row">
        <?php $form = ActiveForm::begin(['id' => 'import-grupo-form', 'options' => ['enctype' => 'multipart/form-data']]) ?>
        <?= Html::hiddenInput('ImportGrupoForm[id]', $id); ?>
        <?= $form->field($model, 'grupoFile')->fileInput(['class' => 'col-md-10'])->label(false) ?>
        <div class="lds-dual-ring importar col-md-2 oculto"></div>
    </div>
    <div class="form-group">
        <?= Html::a('Download template', '@web/files/grupo-template.xls', ['class' => 'btn-oxford boton-peque', 'id' => 'btn-template']) ?>
        <?= Html::a('Import', 'javascript:;', ['class' => 'btn-oxford boton-peque', 'id' => 'btn-import-group']) ?>
    </div>
<?php ActiveForm::end() ?>
</div>
