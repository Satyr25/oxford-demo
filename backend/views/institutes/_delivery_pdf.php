<?php
use yii\helpers\Html;
?>
<?= Html::img('@web/images/logoColor.png',['id'=>'logo']) ?>
<div id="titulo">SOLICITUD DE MENSAJERÍA</div>

<div class="espacio-vertical"></div>
<div class="bloque-mitad">
    <div>SOLICITADO POR:</div>
    <table class="contenido">
        <tr>
            <td></td>
        </tr>
    </table>
</div>
<div class="bloque-mitad">
    <div>FECHA:</div>
    <table class="contenido">
        <tr>
            <td><?= date("d-m-Y") ?></td>
        </tr>
    </table>
</div>

<div class="bloque-mitad">
    <div>EMPRESA:</div>
    <table class="contenido">
        <tr>
            <td></td>
        </tr>
    </table>
</div>
<div class="bloque-mitad">
    <div>PROGRAMA:</div>
    <table class="contenido">
        <tr>
            <td><?= $instituto->programa->nombre ?></td>
        </tr>
    </table>
</div>

<div class="espacio-vertical"></div>
<div>
    <div class="subtitulo">INSTRUCCIONES DE ENVIO</div>
    <div class="bloque-mitad">
        <div>DESTINATARIO (CONTACTO):</div>
        <table class="contenido">
            <tr>
                <td><?= $instituto->profesors[0]->nombre ?></td>
            </tr>
        </table>
    </div>
    <div class="bloque-mitad">
        <div>COLEGIO/ PARTICULAR:</div>
        <table class="contenido">
            <tr>
                <td><?= $instituto->nombre ?></td>
            </tr>
        </table>
    </div>
    <div class="bloque-mitad">
        <div>CALLE Y No.:</div>
        <table class="contenido">
            <tr>
                <td><?= $direccion->calle.' '.$direccion->numero_ext.($direccion->numero_int ? $direccion->numero_int : '') ?></td>
            </tr>
        </table>
    </div>
    <div class="bloque-mitad">
        <div>COLONIA/DELEGACION:</div>
        <table class="contenido">
            <tr>
                <td><?= $direccion->colonia.($direccion->municipio ? ', '.$direccion->municipio : '') ?></td>
            </tr>
        </table>
    </div>
    <div class="bloque-mitad">
        <div>ESTADO:</div>
        <table class="contenido">
            <tr>
                <td><?= $direccion->estado->estadonombre ?></td>
            </tr>
        </table>
    </div>
    <div class="bloque-mitad">
        <div>PAIS:</div>
        <table class="contenido">
            <tr>
                <td><?= $direccion->pais->nombre ?></td>
            </tr>
        </table>
    </div>
    <div class="bloque-mitad">
        <div>C.P.:</div>
        <table class="contenido">
            <tr>
                <td><?= $direccion->codigo_postal ?></td>
            </tr>
        </table>
    </div>
    <div class="bloque-mitad">
        <div>REFERENCIA:</div>
        <table class="contenido">
            <tr>
                <td><?= $direccion->referencia ?></td>
            </tr>
        </table>
    </div>
    <div class="bloque-mitad">
        <div>TEL DEL DESTINATARIO:</div>
        <table class="contenido">
            <tr>
                <td><?= $instituto->telefono ?></td>
            </tr>
        </table>
    </div>
</div>

<div class="espacio-vertical"></div>
<div class="subtitulo">TIPO DE ENVIÓ</div>
<div class="bloque-mitad">
    <div id="documento">DOCUMENTO</div>
    <div id="paquete">PAQUETE</div>
</div>
<div class="bloque-mitad asegurar">
    <div id="texto-asegurar">ASEGURAR PAQUETE:</div>
    <div id="si">SÍ</div>
    <div id="no">NO</div>
</div>

<div class="bloque-mitad" id="tiempos">
    <div>ENVIO EXPRESS (1-2 DIAS)</div>
    <div>ESTÁNDAR (2-5 DIAS)</div>
    <div style="" id="firma-solicitante">FIRMA DEL SOLICITANTE:</div>
</div>
<div class="bloque-mitad" id="observaciones">
    <div>OBSERVACIONES (ESPECIFICAR EL DOCUMENTO O PAQUETE QUE ENVIAS ):</div>
    <table class="contenido">
        <tr>
            <td></td>
        </tr>
    </table>
</div>
<div class="bloque-mitad">
    <div>AUTORIZA:</div>
    <div class="nombre-firma">NOMBRE Y FIRMA</div>
</div>
<div class="bloque-mitad" id="autorizacion">
    <div>VoBo.:</div>
    <div class="nombre-firma">NOMBRE Y FIRMA</div>
</div>
