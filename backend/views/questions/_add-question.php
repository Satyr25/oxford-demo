<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
?>

<div class="formulario agrega-pregunta">
    <h2>Add question</h2>

    <?php
        echo Html::dropDownList('categoria', 'USE', $secciones, $options = ['prompt' => 'Select', 'id' => 'select-category']);
    ?>
    <div class="add-question">
        <?php
        echo $this->render('_add-reading',['articulos' => $articulos, 'examenes' => $examenes]);
        echo $this->render('_add-listening',['audios' => $audios, 'examenes' => $examenes]);
        echo $this->render('_add-use',['examenes' => $examenes]);
        echo $this->render('_add-writing',['examenes' => $examenes]);
        ?>
    </div>

</div>
