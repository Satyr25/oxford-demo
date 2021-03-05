<?php foreach($alumnos as $i => $alumno){ ?>
    <?php
        $alumno->impresiones = $alumno->impresiones+1;
        $alumno->save();
    ?>
    <div id="nombre-alumno">
        <?= $alumno->nombre.' '.$alumno->apellidos ?>
    </div>
    <?php if($i+1 < $total_diplomas){ ?>
        <pagebreak>
    <?php } ?>
<?php } ?>
