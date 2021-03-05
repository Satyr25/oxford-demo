<?php
use app\models\Examen;
use app\models\Seccion;
use app\models\NivelAlumno;
?>

<?php foreach($certificados as $i => $certificado){ ?>
    <?php $examen = Examen::findOne($certificado->examen_id); ?>
    <?php
        foreach ($examen->seccions as $seccion) {
            $tipo = $seccion->tipoSeccion->clave;
            switch ($tipo) {
                case "USE":
                    $calificacionUse = $certificado->promedio_use;
                    break;
                case 'REA':
                    $calificacionRea = $certificado->promedio_reading;
                    break;
                case 'LIS':
                    $calificacionLis = $certificado->promedio_listening ;
                    break;
                case 'WRI':
                    $calificacionWri = $certificado->promedio_writing;
                    break;
            }
        }
        $calificacionSpeaking = $certificado->calificacionSpeaking;
        $promedio = $certificado->promedio;
    ?>
    <div id="nombre-alumno">
        <?= ucfirst($certificado->nombre_alumno) ?>
    </div>
    <div id="nivel">
        <?= $certificado->nivel ?>
    </div>
    <div id="acreditacion">
        <?= $certificado->codigo ?>
    </div>
    <div id="nivel-prueba">
        <?php $tested =  NivelAlumno::findOne($certificado->tested_level) ?>
        TESTED LEVEL <?= $tested->nombre ?>
    </div>
    <div id="puntos">
        <table>
            <tr>
                <th>OVERALL PERCENTAGE</th>
                <td>
                    <?= floor($promedio) ?>%
                </td>
            </tr>
        </table>
    </div>
    <div id="calificaciones">
        <table>
            <tr>
                <th>USE OF ENGLISH</th>
                <td><?= floor($calificacionUse) ?>%</td>
            </tr>
            <tr>
                <th>READING</th>
                <td><?= floor($calificacionRea) ?>%</td>
            </tr>
            <tr>
                <th>LISTENING</th>
                <td><?= floor($calificacionLis) ?>%</td>
            </tr>
            <tr>
                <th>WRITING</th>
                <td><?= floor($calificacionWri) ?>%</td>
            </tr>
            <?php if($calificacionSpeaking !== null){ ?>
                <tr>
                    <th>SPEAKING</th>
                    <td><?= floor($calificacionSpeaking) ?>%</td>
                </tr>
            <?php } ?>
        </table>
    </div>
    <div id="fecha">
        <?= date("F, Y") ?>
    </div>
    <?php if($i+1 < $total_certificados){ ?>
        <pagebreak>
    <?php } ?>
<?php } ?>
