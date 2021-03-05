<?php
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
?>

<div class="view-react">
    <section id="inicio-view-react" class="inicio">
        <div class="container">
            <div class="mitad-izq">
                <h2>Question</h2>
            </div>
            <div class="mitad-der">
                <a href="<?= Url::to(['questions/delete', 'id' => $reactivo->id]) ?>" class="btn-oxford boton-peque btn-rojo" id="delete-question">Delete</a>
                <a href="javascript:;" class="btn-oxford boton-peque" id="edit-question">Edit</a>
            </div>
        </div>
    </section>

    <section class="detalles-reactivo">
        <p class="question-type">Question type: <?= $reactivo->tipoReactivo->nombre ?></p>
        <p>
            <?php
             if($reactivo->articulo_id)
             {
                echo "Articulo: ".nl2br($reactivo->articulo->texto);
             }
            ?>
        </p>
        <?php if($reactivo->audio_id)
        {
        ?>
             <audio controls>
                <source src="<?= Url::to('@web/'.$reactivo->audio->audio) ?>" type="audio/mpeg">
            </audio>
        <?php
        }
        ?>
        <div id="pregunta-visible">
            <p><?= nl2br($reactivo->instrucciones) ?></p>
            <p>Q: <?= nl2br($reactivo->pregunta) ?></p>
            <?php
            if($reactivo->tipoReactivo->clave == 'MUL' || $reactivo->tipoReactivo->clave == 'CAM' || $reactivo->tipoReactivo->clave == 'ART' || $reactivo->tipoReactivo->clave == 'AUD'){
                $respuestas = $reactivo->respuestas;
                foreach($respuestas as $respuesta){
                    if ($respuesta->correcto == 1){
            ?>
                <p class="correcto-resp"><?= $respuesta->respuesta ?></p>
            <?php }}
            foreach($respuestas as $respuesta){
                if ($respuesta->correcto == 0){
            ?>
                <p><?= $respuesta->respuesta ?></p>
            <?php }}}else if($reactivo->tipoReactivo->clave == 'COM'){ ?>
                <?php $respuestas = $reactivo->respuestasCompletar; ?>
                <p class="correcto-resp">
                    <?= implode(', ',explode('|',$respuestas[0]->respuesta)) ?>
                </p>
            <?php } ?>

            <?php
                if($reactivo->tipoReactivo->clave == 'REL')
                {
                    foreach($reactivo->enunciadoColumns as $enunciado){
                        echo '<p>'.$enunciado->enunciado. ' - ' .$enunciado->respuestaColumn->respuesta . '</p>';
                    }
                }
            ?>
        </div>
        <section id="editar-reactivo" class="oculto">
            <?php if($reactivoForm->tipo == 'MUL'){ ?>
                <?= $this->render('_editar_MUL',[
                    'reactivoForm' => $reactivoForm
                    ]) ?>
            <?php }else if($reactivoForm->tipo == 'REL'){ ?>
                <?= $this->render('_editar_REL',[
                    'reactivoForm' => $reactivoForm
                    ]) ?>
            <?php }else if($reactivoForm->tipo == 'WRI'){ ?>
                <?= $this->render('_editar_WRI',[
                    'reactivoForm' => $reactivoForm
                    ]) ?>
            <?php }else if($reactivoForm->tipo == 'COM'){ ?>
                <?= $this->render('_editar_COM',[
                    'reactivoForm' => $reactivoForm
                    ]) ?>
            <?php } ?>
        </section>
    </section>
</div>
