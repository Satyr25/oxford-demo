    <?php
use yii\helpers\Html;
use yii\helpers\Url;
use app\models\Paises;

$this->title = 'Exams';
?>

<div class="see-exams">
   <section class="inicio">
        <div class="container">
            <h2>
                <?php
                switch ($tipo) {
                    case 'DIA':
                        echo "Diagnostic Reports";
                        break;
                        case 'DIAV2':
                            echo "Diagnostic V2 Reports";
                            break;
                    case 'MOC':
                        echo 'Mock Reports';
                        break;
                    case 'CER':
                        echo 'Certificate Reports';
                        break;
                    case 'CERV2':
                        echo 'Certificate V2 Reports';
                        break;
                    default:
                        echo 'Reports';
                        break;
                }
                ?>
            </h2>
        </div>
    </section>
    <section id="search-exam" class="busqueda">
        <div class="container">
            <div id="export-questions">
                <a class="export-buttons" href="<?php echo Url::to(['academic-reports/questions', 'tipo' => $tipo, 'ciclo' => $ciclo_actual]); ?>">
                    Export Questions
                </a>
            </div>
            <div class="lds-dual-ring exportar oculto"></div>
            <input type="hidden" id="url" value="<?= Url::base(true) ?>/academic-reports/index?tipo=<?=$tipo?>&ciclo=" />
            <select id="ciclo-reportes">
                <?php foreach($ciclos as $ciclo){ ?>
                    <option value="<?= $ciclo->id ?>" <?= $ciclo->id == $ciclo_actual ? 'selected' : '' ?>>
                        <?= $ciclo->nombre ?>
                    </option>
                <?php } ?>
            </select>
        </div>
    </section>
    <section id="reports">
        <div class="container">
            <div class="tabla">
                <table id="totales-generales" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>LEVEL</th>
                            <th>STUDENTS</th>
                            <th>%</th>
                            <th>AVERAGE GRADE</th>
                        </tr>
                    </thead>
                    <?php foreach($totales as $nivel => $total){ ?>
                        <tr>
                            <td><?= Html::a($nivel,Url::to(['academic-reports/level', 'tipo' => $tipo, 'nivel' => $nivel, 'ciclo' => $ciclo_actual])) ?></td>
                            <td><?= $total['alumnos'] ? number_format($total['alumnos'],0) : '-' ?></td>
                            <td><?= $total['porcentaje'] ? $total['porcentaje'] : '-' ?></td>
                            <td><?= $total['promedio'] ? $total['promedio'] : '-' ?></td>
                        </tr>
                        <?php
                            $total_alumnos += $total['alumnos'];
                            $promedios += $total['promedio'];
                        ?>
                    <?php } ?>
                    <tr>
                        <td>TOTAL</td>
                        <td><?= number_format($total_alumnos,0) ?></td>
                        <td>100</td>
                        <td><?= number_format(round($promedios/count($totales),2,PHP_ROUND_HALF_EVEN),2) ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </section>
</div>
