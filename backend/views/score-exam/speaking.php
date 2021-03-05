<?php
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\widgets\Pjax;
use yii\helpers\Html;

$this->title = 'Score Exam';
?>

<div class="score-exam">
    <section id="inicio-score-exam" class="inicio">
        <div class="container">
            <h2>Speaking</h2>
        </div>
    </section>

    <section id="add-search-user" class="busqueda">
        <div class="container">
            <div class="col-md-8"></div>
            <?php echo $this->render('_search-speaking', ['filtro' => $searchModel]); ?>
        </div>
    </section>

    <section id="tabla-writing" class="tabla">
        <div class="container">
            <?php // Pjax::begin(['id' => 'pjax-grid-institutos']); ?>
            <?php
            echo GridView::widget([
                'dataProvider' => $dataProvider,
                'rowOptions' => function ($model, $index, $widget, $grid) {
                    return ['class' => 'score-speaking', 'id' => $model->id];
                },
                'columns' => [
                    [
                        'label' => 'Code',
                        'value' => function ($model) {
                            return $model->codigo;
                        },
                    ],
                    [
                        'label' => 'Name',
                        'value' => function ($model) {
                            return $model->nombre_alumno;
                        },
                    ],
                    [
                        'label' => 'Level',
                        'value' => function ($model) {
                            return $model->nivel;
                        },
                    ],
                    [
                        'label' => 'Institute',
                        'value' => function ($model) {
                            return $model->instituto;
                        },
                    ],
                    [
                        'label' => 'Date',
                        'value' => function ($model) {
                            return $model->fecha_realizacion ? date( "M d, Y", $model->fecha_realizacion) : 'N/A';
                        }
                    ]
                ],
            ]);
            ?>
        <?php // Pjax::end(); ?>
        </div>
    </section>

    <div id="score-speaking" class="mfp-hide white-popup-block">
        <div class="lds-dual-ring oculto"></div>
        <div class="contenido"></div>
    </div>


</div>
