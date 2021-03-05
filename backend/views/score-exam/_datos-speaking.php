<div class="desglose-speaking">
    <div class="dato-speaking">
        <span>Code:</span>
        <?= $alumno_examen->alumno->users[0]->codigo ?>
    </div>
    <div class="dato-speaking">
        <span>Level:</span>
        <?= $alumno_examen->alumno->nivelCertificate->nombre ?>
    </div>
    <div class="dato-speaking">
        <span>Date:</span>
        <?php if($alumno_examen->fecha_realizacion){ ?>
            <?= date('d M Y', $alumno_examen->fecha_realizacion) ?>
        <?php }else{ ?>
            N/A
        <?php } ?>
    </div>
    <div class="clear"></div>
</div>
<div class="calificaciones-certificate">
    <h2>Grades</h2>
    <div class="clear"></div>
    <?php
        if($alumno_examen->calificaciones){
            $examen = $alumno_examen->examen;
            foreach ($examen->seccions as $seccion){
                $tipo = $seccion->tipoSeccion->clave;
                switch ($tipo) {
                    case "USE":
                        $calificacionUse = floor(($alumno_examen->calificaciones->calificacionUse * 100) / $seccion->puntos_seccion);
                        break;
                    case 'REA':
                        $calificacionRea = floor((($alumno_examen->calificaciones->calificacionReading * 100) / $seccion->puntos_seccion) / $examen->cantidadSecciones('REA'));
                        break;
                    case 'LIS':
                        $calificacionLis = floor((($alumno_examen->calificaciones->calificacionListening * 100) / $seccion->puntos_seccion) / $examen->cantidadSecciones('LIS'));
                        break;
                    case 'WRI':
                        $calificacionWri = floor(round(($alumno_examen->calificaciones->calificacionWriting * 100) / $seccion->puntos_seccion,0,PHP_ROUND_HALF_DOWN));
                        break;
                }
            }
        }
    ?>
    <div class="calificacion">
        <span>Listening:</span>
        <br>
        <?php if(isset($examen)){ ?>
            <?= $calificacionLis ?>%
        <?php }else{ ?>
            N/A
        <?php } ?>
    </div>
    <div class="calificacion">
        <span>Reading:</span>
        <br>
        <?php if(isset($examen)){ ?>
            <?= $calificacionRea ?>%
        <?php }else{ ?>
            N/A
        <?php } ?>
    </div>
    <div class="calificacion">
        <span>Use of English:</span>
        <br>
        <?php if(isset($examen)){ ?>
            <?= $calificacionUse ?>%
        <?php }else{ ?>
            N/A
        <?php } ?>
    </div>
    <div class="calificacion">
        <span>Writing:</span>
        <br>
        <?php if(isset($examen)){ ?>
            <?= $calificacionWri ?>%
        <?php }else{ ?>
            N/A
        <?php } ?>
    </div>
    <div class="clear"></div>
</div>

<div id="calificacion-speaking">
    <span>Speaking:</span>
    <br>
    <input type="number" id="puntos-speaking" />
    <a
        href="javascript:;"
        id="save-speaking"
        data-calificaciones="<?= $alumno_examen->calificaciones->id ?>"
        data-alumno="<?= $alumno_examen->alumno_id ?>"
        class="btn-oxford"
        >
        Save
    </a>
</div>
