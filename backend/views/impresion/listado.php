<?php
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\widgets\Pjax;
use yii\helpers\Html;
use yii\helpers\Url;

use app\models\Calificaciones;

$this->title = 'Impresión';
?>
<div class="impresion">
    <section class="inicio">
        <div class="container">
            <h2>Impresiones</h2>
            <?php if($entidad == 'INS'||$entidad == 'GPO'||$entidad == 'ALU'){ ?>
                <div class="nombre-entidad">
                    <span>Instituto:</span> <?= $instituto ?>
                </div>
            <?php } ?>
            <?php if($entidad == 'GPO'||$entidad == 'ALU'){ ?>
                <div class="nombre-entidad">
                    <span>Grupo:</span> <?= $grupo ?>
                </div>
            <?php } ?>
            <?php if($entidad == 'ALU'){ ?>
                <div class="nombre-entidad">
                    <span>Alumno:</span> <?= $alumno ?>
                </div>
            <?php } ?>
        </div>
    </section>

    <div class="busqueda">
        <div class="container">
            <div class="col-md-6" id="tipo-impresion">
                <a href="<?= Url::to(['impresion/listado', 'entidad' => $entidad, 'id' => $id_entidad, 'tipo' => 'CER', 'ciclo' => $ciclo]) ?>" class="<?= $tipo == 'CER' ? 'seleccionado' : '' ?>">
                    Certificados
                </a>
                /
                <a href="<?= Url::to(['impresion/listado', 'entidad' => $entidad, 'id' => $id_entidad, 'tipo' => 'DIP', 'ciclo' => $ciclo]) ?>" class="<?= $tipo == 'DIP' ? 'seleccionado' : '' ?>">
                    Diplomas
                </a>
            </div>
            <div class="search-bar-institutes col-md-6" id="search-block">
                 <input class="typeahead busqueda-impresion campo-busqueda form-control" type="text" placeholder="Buscar" />
                 <div class="botones-busqueda">
                     <a href="javascript:;" id="imprimir" class="acciones-busqueda">
                         Imprimir
                     </a>
                 </div>
            </div>
        </div>
    </div>

    <section class="tabla tabla-impresion">
        <div class="container">
            <?php echo Html::beginForm([$tipo == 'CER' ? 'impresion/certificado' : 'impresion/diploma'], 'post', ['id' => 'impresion-form']);?>
                <input type="hidden" value="<?= $tipo ?>" name="documento"/>
                <?php
                echo GridView::widget([
                    'dataProvider' => $dataProvider,
                    'columns' => [
                        [
                            'class' => 'yii\grid\CheckboxColumn',
                            'checkboxOptions' => function ($model) {
                                return ['value' => $model->id];
                            },
                        ],
                        [
                            'attribute' => 'Alumno',
                            'value' => function ($model) use ($tipo, $ciclo){
                                return HTML::a($model->nombre,Url::to(['impresion/listado', 'entidad' => 'ALU', 'id' => $model->id, 'tipo' => $tipo, 'ciclo' =>$ciclo]));
                            },
                            'format' => 'raw'
                        ],
                        [
                            'attribute' => 'Grupo',
                            'value' => function ($model) use ($tipo, $ciclo) {
                                return HTML::a($model->grupo_nombre,Url::to(['impresion/listado', 'entidad' => 'GPO', 'id' => $model->grupo_id, 'tipo' => $tipo, 'ciclo' =>$ciclo]));
                            },
                            'format' => 'raw'
                        ],
                        [
                            'attribute' => 'Instituto',
                            'value' => function ($model) use ($tipo, $ciclo) {
                                return HTML::a($model->instituto,Url::to(['impresion/listado', 'entidad' => 'INS', 'id' => $model->instituto_id, 'tipo' => $tipo, 'ciclo' =>$ciclo]));
                            },
                            'format' => 'raw'
                        ],
                        [
                            'attribute' => 'Impresiones',
                            'value' => function ($model) use ($tipo, $ciclo) {
                                return HTML::a($model->impresiones,Url::to(['impresion/listado', 'entidad' => 'INS', 'id' => $model->instituto_id, 'tipo' => $tipo, 'ciclo' =>$ciclo]));
                            },
                            'format' => 'raw'
                        ],
                    ],
                ]);
                ?>
        <?php echo Html::endForm(); ?>
        </div>
    </section>
</div>
