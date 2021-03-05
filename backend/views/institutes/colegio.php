<?php
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\widgets\Pjax;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$this->title = 'Institutes';
echo Html::hiddenInput('name', Yii::$app->controller->id, ['class'=>'controller']);
$cookie = Yii::$app->controller->id.'-'.$this->context->action->id;
$seleccionados = isset($_COOKIE[$cookie]) ? explode(',',$_COOKIE[$cookie]) : false;
?>

<div class="colegio">
    <section id="inicio-colegio" class="inicio">
        <div class="container">
            <h2>Institutes</h2>
        </div>
    </section>

    <section id="view-colegio">
        <div class="container">
            <div class="nombre-colegio">
                <h3><?= $colegio->nombre ?></h3>
                <a href="javascript:;" class="btn-oxford editar-user" id="edit-instituto-boton">Editar</a>
                <a href="<?= Url::to(['institutes/export-delivery-format', 'id' => $colegio->id]) ?>" class="btn-oxford editar-user">Delivery format</a>
            </div>

            <div class="show-info">
                <div class="half-div tabla-datos">
                    <?php if ($colegio->pruebas): ?>
                    <div class="separa-datos">
                        <p class="nombre-dato important"><strong>Test Institute</strong></p>
                    </div>
                    <?php endif; ?>
                    <div class="separa-datos">
                        <p class="nombre-dato">Institute Name*:</p><p><?= $colegio->nombre ?></p>
                    </div>
                    <?php if($programa){ ?>
                        <div class="separa-datos">
                            <p class="nombre-dato">Program:</p><p><?= $programa->nombre ?></p>
                        </div>
                    <?php } ?>
                    <div class="separa-datos">
                        <p class="nombre-dato">Email*:</p><p> <?= $colegio->email ?></p>
                    </div>
                    <div class="separa-datos">
                        <p class="nombre-dato">Adress line 1:</p><p> <?= $colegio->direccion->calle.' '.$colegio->direccion->numero_ext  ?></p>
                    </div>
                    <div class="separa-datos">
                        <p class="nombre-dato">Adress line 2:</p><p> <?= $colegio->direccion->colonia ?></p>
                    </div>
                    <div class="separa-datos">
                        <p class="nombre-dato">Adress line 3:</p><p> <?= $colegio->direccion->municipio ?></p>
                    </div>
                    <div class="separa-datos">
                        <p class="nombre-dato">City:</p><p> <?php
                        if (isset($colegio->direccion->ciudad)) {
                            echo $colegio->direccion->ciudad;
                        }
                        ?></p>
                    </div>
                    <div class="separa-datos">
                        <p class="nombre-dato">State:</p><p>
                            <?php
                                if(isset($colegio->direccion->estado_id)){
                                    echo $colegio->direccion->estado->estadonombre;
                                }
                            ?>
                        </p>
                    </div>
                    <div class="separa-datos">
                        <p class="nombre-dato">Country:</p><p>
                            <?php
                            if (isset($colegio->direccion->pais_id)) {
                                echo $colegio->direccion->pais->nombre;
                            }
                            ?>
                        </p>
                    </div>
                    <div class="separa-datos">
                        <p class="nombre-dato">Region:</p><p> <?= $colegio->region->nombre ?></p>
                    </div>
                </div><!--
             --><div class="half-div tabla-datos">
                    <div class="separa-datos">
                        <p class="nombre-dato">Zipcode:</p><p> <?= $colegio->direccion->codigo_postal ?></p>
                    </div>
                    <div class="separa-datos">
                        <p class="nombre-dato">Reference:</p><p> <?= $colegio->direccion->referencia ?></p>
                    </div>
                    <div class="separa-datos">
                        <p class="nombre-dato">Phone*:</p><p> <?= $colegio->telefono ?></p>
                    </div>
                    <div class="separa-datos ultimo-dato">
                        <p class="nombre-dato">Status:</p><p> <?php
                        if($colegio->status){
                            echo "Activo";
                        }else{
                            echo "Inactivo";
                        }
                        ?>
                    </p>
                    </div>

                    <h4 class="nombre-dato">Contact Details</h4>
                    <?php
                    foreach ($colegio->profesors as $profesor) {
                    ?>
                        <div class="separa-datos">
                            <p class="nombre-dato">Contact Name*:</p><p> <?= $profesor->nombre ?></p>
                        </div>
                        <div class="separa-datos ultimo-dato">
                            <p class="nombre-dato">Contact Email*:</p><p> <?= $profesor->email ?></p>
                        </div>
                    <?php
                    }
                    ?>

                    <h4 class="nombre-dato">Dates</h4>
                    <div class="separa-datos">
                        <p class="nombre-dato">Diagnostic Date:</p><p><?= $colegio->fecha_examen_dia ? date('d/m/Y', $colegio->fecha_examen_dia) : "No date" ?></p>
                    </div>
                    <?php if ($colegio->programa->clave == "CLI"): ?>
                    <div class="separa-datos">
                        <p class="nombre-dato">Mock Date:</p><p><?= $colegio->fecha_examen_moc ? date('d/m/Y', $colegio->fecha_examen_moc) : "No date" ?></p>
                    </div>
                    <div class="separa-datos">
                        <p class="nombre-dato">Speaking Date:</p><p><?= $colegio->fecha_examen_spe ? date('d/m/Y', $colegio->fecha_examen_spe) : "No date" ?></p>
                    </div>
                    <?php endif; ?>
                    <div class="separa-datos">
                        <p class="nombre-dato">Certificate Date:</p><p><?= $colegio->fecha_examen_cer ? date('d/m/Y', $colegio->fecha_examen_cer) : "No date" ?></p>
                    </div>
                    <?php if ($colegio->programa->clave != "CLI"): ?>
                    <div class="separa-datos">
                        <p class="nombre-dato">Round:</p><p><?= $colegio->ronda ? $colegio->ronda : "N/A" ?></p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php echo $this->render('_edit-instituto',[
                'colegio'=>$colegio,
                'institutoForm'=>$institutoForm,
                'paises'=>$paises,
                'programas' => $programas,
                'regiones' => $regiones
            ])?>
        </div>
    </section>

    <section id="tabla-grupos" class="tabla">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <a href="add-group?id=<?= $colegio->id ?>" class="boton-add group">+ Add Group</a>
                </div>
                <div class="col-md-offset-4 col-md-4 botones-busqueda">
                    <input type="hidden" value="<?= Url::base(true); ?>" id="base_url" />
                    <div class="col-md-5">
                        <a href="<?= Url::to(['institutes/export-groups-complete', 'id' => $colegio->id, 'ciclo_escolar' => $ciclo_escolar]); ?>" class="acciones-busqueda export-buttons">Export all</a>
                    </div>
                    <div class="col-md-4">
                        <div class="dropdown">
                            <a href="#" class="dropdown-toggle acciones-busqueda" data-toggle="dropdown">Export</a>
                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                <li class="dropdown-submenu">
                                    <a class="show-submenu" href="#">Diagnostic</a>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a class="export-buttons" href="<?= Url::to(['institutes/export-groups', 'id' => $colegio->id, 'type' => $diagnosticType->id, 'file' => 'pdf']) ?>">PDF</a>
                                        </li>
                                        <li>
                                            <a class="export-buttons" href="<?= Url::to(['institutes/export-groups', 'id' => $colegio->id, 'type' => $diagnosticType->id, 'file' => 'xls']) ?>">XLS</a>
                                        </li>
                                    </ul>
                                </li>
                                <li class="dropdown-submenu">
                                    <a class="show-submenu" href="#">Mock</a>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a class="export-buttons" href="<?= Url::to(['institutes/export-groups', 'id' => $colegio->id, 'type' => $mockType->id, 'file' => 'pdf']) ?>">PDF</a>
                                        </li>
                                        <li>
                                            <a class="export-buttons" href="<?= Url::to(['institutes/export-groups', 'id' => $colegio->id, 'type' => $mockType->id, 'file' => 'xls']) ?>">XLS</a>
                                        </li>
                                    </ul>
                                </li>
                                <li class="dropdown-submenu">
                                    <a class="show-submenu" href="#">Certificate</a>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a class="export-buttons" href="<?= Url::to(['institutes/export-groups', 'id' => $colegio->id, 'type' => $certificateType->id, 'file' => 'pdf']) ?>">PDF</a>
                                        </li>
                                        <li>
                                            <a class="export-buttons" href="<?= Url::to(['institutes/export-groups', 'id' => $colegio->id, 'type' => $certificateType->id, 'file' => 'xls']) ?>">XLS</a>
                                        </li>
                                    </ul>
                                </li>
                            <ul/>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <a href="javascript:;" class="acciones-busqueda" id="delete-grupos-institutes">Delete</a>
                    </div>
                    <div class="lds-dual-ring exportar oculto"></div>
                </div>
            </div>
            <?php
            echo Html::beginForm(['delete-multiple-groups'], 'post', ['id' => 'grupos-table-form']);
            Pjax::begin(['id' => 'pjax-grid-grupos']);
            echo GridView::widget([
            'dataProvider' => $dataProvider,
            'columns' => [
                [
                    'class' => 'yii\grid\CheckboxColumn',
                    'checkboxOptions' => function ($model) use ($seleccionados){
                        $checked = false;
                        if($seleccionados && in_array($model->id,$seleccionados)){
                            $checked = true;
                        }
                        return ['value' => $model->id, 'checked' => $checked];
                    },
                ],
                [
                    'class' => 'yii\grid\SerialColumn',
                ],
                [
                    'label'=>'Group',
                    'attribute'=>'grupo',
                    'contentOptions'=>function($model){return ['class'=>'view-grupo','id'=>$model->id];}
                ],
                [
                    'label' => 'Level',
                    'attribute'=>'nivel.nombre',
                    'contentOptions'=>function($model){return ['class'=>'view-grupo','id'=>$model->id];}
                ],
                [
                    'attribute'=>'created_at',
                    'contentOptions'=>function($model){return ['class'=>'view-grupo','id'=>$model->id];},
                    'format'=> 'date'
                ],
                [
                    'attribute'=>'updated_at',
                    'contentOptions'=>function($model){return ['class'=>'view-grupo','id'=>$model->id];},
                    'format'=> 'date'
                ],
            ],
        ]);
        ?>
        <input type="hidden" value="<?= $ciclo_escolar ?>" name="ciclo_escolar">
        <?php
        Pjax::end();
        echo Html::endForm();
        ?>
        </div>
    </section>
</div>
