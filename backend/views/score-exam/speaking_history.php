<?php
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

$this->title = 'Scoring History';
?>

<div class="score-exam">
    <section id="inicio-score-exam" class="inicio">
        <div class="container">
            <h2>Score History</h2>
        </div>
    </section>

    <section id="add-search-user" class="busqueda">
        <div class="container">
            <div class="col-md-8"></div>

            <?php echo $this->render('_search-speaking-history', ['filtro' => $searchModel]); ?>
        </div>
    </section>

    <section id="tabla-writing" class="tabla">
        <div class="container">
            <?php
            echo GridView::widget([
                'dataProvider' => $dataProvider,
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    [
                        'label' => 'Level',
                        'value' => function ($model) {
                            return $model->level ? $model->level : 'N/A';
                        },
                    ],
                    'code',
                    'score',
                    [
                        'label' => 'Academic',
                        'attribute' => 'academico',
                    ],
                    [
                        'label' => 'fecha',
                        'attribute' => 'fecha',
                        'contentOptions' => function ($model) {
                            return ['class' => 'view-writing', 'id' => $model->id];
                        },
                        'format' => 'date'
                    ]
                ],
            ]);
            ?>
        </div>
    </section>


</div>
