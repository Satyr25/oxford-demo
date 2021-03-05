<?php foreach($certificados as $i => $certificado){ ?>
    <div id="nombre-alumno">
        <?= ucfirst($certificado->nombre_alumno) ?>
    </div>
    <div id="nivel">
        <?= $certificado->nivel ?>
    </div>
    <div id="acreditacion">
        ACREDITATION
    </div>
    <div id="nivel-prueba">
        TESTED LEVEL <?= $certificado->nivel ?>
    </div>
    <div id="puntos">
        <table>
            <tr>
                <th>OBTAINED POINS</th>
                <td>
                    <?=
                        $certificado->calificacionSpeaking+
                        $certificado->calificacionUse+
                        $certificado->calificacionListening+
                        $certificado->calificacionReading+
                        $certificado->calificacionWriting ?>
                </td>
            </tr>
            <tr>
                <th>TOTAL POINTS</th>
                <td></td>
            </tr>
            <tr>
                <th>OVERALL PERCENTAGE</th>
                <td></td>
            </tr>
        </table>
    </div>
    <div id="calificaciones">
        <table>
            <tr>
                <th>USE OF ENGLISH</th>
                <td></td>
            </tr>
            <tr>
                <th>READING</th>
                <td></td>
            </tr>
            <tr>
                <th>LISTENING</th>
                <td></td>
            </tr>
            <tr>
                <th>WRITING</th>
                <td></td>
            </tr>
            <tr>
                <th>SPEAKING</th>
                <td></td>
            </tr>
        </table>
    </div>
    <div id="fecha">
        <?= date("F, Y") ?>
    </div>
    <?php if($i+1 < $total_certificados){ ?>
        <pagebreak>
    <?php } ?>
<?php } ?>
