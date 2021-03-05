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
                         echo "Diagnostic ".$nivel;
                         break;
                     case 'DIAV2':
                         echo "Diagnostic V2 ".$nivel;
                             break;
                     case 'MOC':
                         echo 'Mock '.$nivel;
                         break;
                     case 'CER':
                         echo 'Certificate '.$nivel;
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
             <input type="hidden" id="url" value="<?= Url::base(true) ?>/academic-reports/level?tipo=<?=$tipo?>&nivel=<?=$nivel?>&ciclo=" />
             <select id="ciclo-reportes">
                 <?php foreach($ciclos as $ciclo){ ?>
                     <option value="<?= $ciclo->id ?>" <?= $ciclo->id == $ciclo_actual ? 'selected' : '' ?>>
                         <?= $ciclo->nombre ?>
                     </option>
                 <?php } ?>
             </select>
         </div>
     </section>
    <div class="see-exams">
        <section id="reports">
            <div class="container">
                <div class="tabla">
                    <table id="totales-generales" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>VERSION</th>
                                <th>USE</th>
                                <th>REA</th>
                                <th>LIS</th>
                                <?php if($tipo != 'MOC'){ ?>
                                    <th>WRI</th>
                                <?php } ?>
                            </tr>
                        </thead>
                        <?php
                            $total_use = 0;
                            $total_rea = 0;
                            $total_lis = 0;
                            $total_wri = 0;
                        ?>
                        <?php foreach($totales as $examen => $total){ ?>
                            <tr>
                                <td><?= $examen ?></td>
                                <td><?= $total['USE'].'%' ?></td>
                                <td><?= $total['REA'].'%' ?></td>
                                <td><?= $total['LIS'].'%' ?></td>
                                <?php if($tipo != 'MOC'){ ?>
                                    <td><?= $total['WRI'].'%' ?></td>
                                <?php } ?>
                            </tr>
                            <?php
                                $total_use += $total['USE'];
                                $total_rea += $total['REA'];
                                $total_lis += $total['LIS'];
                                $total_wri += $total['WRI'];
                            ?>
                        <?php } ?>
                        <tr>
                            <td>TOTAL</td>
                            <?php if(count($totales) > 0){ ?>
                                <td><?= number_format($total_use/count($totales),2).'%' ?></td>
                                <td><?= number_format($total_rea/count($totales), 2).'%' ?></td>
                                <td><?= number_format($total_lis/count($totales), 2).'%' ?></td>
                                <?php if($tipo != 'MOC'){ ?>
                                    <td><?= number_format($total_wri/count($totales), 2).'%' ?></td>
                                <?php } ?>
                            <?php }else{ ?>
                                <td>-</td>
                                <td>-</td>
                                <td>-</td>
                                <?php if($tipo != 'MOC'){ ?>
                                    <td>-</td>
                                <?php } ?>
                            <?php } ?>
                        </tr>
                    </table>
                </div>
            </div>
        </section>
    </div>
</div>
