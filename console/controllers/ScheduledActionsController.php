<?php
namespace console\controllers;

use Yii;
use yii\console\Controller;
use yii\helpers\Console;
use app\models\Instituto;
use app\models\Grupo;
use app\models\Alumno;
use app\models\NivelAlumno;
use app\models\StatusExamen;
use app\models\AlumnoExamen;
use app\models\AluexaReactivos;
use app\models\Reactivo;
use app\models\TipoReactivo;
use app\models\Respuesta;
use app\models\Seccion;
use app\models\TipoSeccion;
use app\models\Calificaciones;
use app\models\Examen;
use app\models\TipoExamen;
use app\models\Programa;

class ScheduledActionsController extends Controller
{
    private $transaction;

    public function actionRememberMail(){
        $institutos = Instituto::find()->all();
        $cc = [];
        $notification = Yii::$app->params['email-notification'];
        array_push($cc, $notification);
        $copia = Yii::$app->params['email-cc'];
        if($copia){
            array_push($cc, $copia);
        }
        foreach($institutos as $instituto){
            $finishedHelper = true;
            foreach($instituto->gruposActivos as $grupo){
                foreach($grupo->alumnosActivos as $alumno){
                    $statusFinished = StatusExamen::find()->where(['codigo'=>'FIN'])->one();
                    if($alumno->status_examen_id == $statusFinished->id){
                        $finishedHelper = false;
                    }
                }
            }
            if(!$finishedHelper){
                Yii::$app->mailer->compose('_institute-remember', [
                    'instituto' => $instituto,
                ])
                ->setFrom(["equipo@blackrobot.mx" => "Oxford TCC"])
                ->setTo($instituto->email)
                ->setCc($cc)
                ->setSubject('Pending exams')
                ->send();
            }
        }
    }

    public function actionFinalizaExamenes(){
        $certificate = TipoExamen::find()->where('clave="CER"')->one();
        $alumnos = AlumnoExamen::find()
            ->select([
                'DISTINCT(alumno_examen.id) AS id',
                'alumno_examen.examen_id AS examenHecho',
                'CONCAT_WS(" ",alumno.nombre,alumno.apellidos) AS alumno'
            ])
            ->join('INNER JOIN', 'alumno', 'alumno.id = alumno_examen.alumno_id')
            ->where(
                'alumno_examen.tipo_examen_id != '.$certificate->id.
                ' AND alumno_examen.status = 1 AND alumno.status = 1'.
                ' AND fecha_realizacion IS NULL AND (alumno.status_examen_id IS NULL OR alumno.status_examen_id = 2)'.
                ' AND ultima_actualizacion IS NOT NULL'.
                ' AND ultima_actualizacion < (UNIX_TIMESTAMP() - 3600)'
            )->all();
        foreach($alumnos AS $alumno){
            $this->calificar($alumno->id,$alumno->examenHecho);
        }
        // SELECT DISTINCT(alumno.id), CONCAT_WS(' ',alumno.nombre, alumno.apellidos) AS alumno, status_examen.nombre AS status FROM alumno_examen INNER JOIN alumno ON alumno.id = alumno_examen.alumno_id INNER JOIN status_examen ON status_examen.id = alumno.status_examen_id WHERE alumno.status = 1 AND status_examen_id = 2 AND (alumno_examen.calificaciones_id IS NOT NULL AND alumno_examen.fecha_realizacion IS NOT NULL) AND alumno_examen.tipo_examen_id != 3;
    }

    private function calificar($id, $examen){
        $respuestasGuardadas = AluexaReactivos::find()->where('aluexa_reactivos.alumno_examen_id='.$id)->all();

        $correctasUse = 0;
        $correctasRea = 0;
        $correctasLis = 0;

        foreach($respuestasGuardadas as $respuesta){
            if($respuesta->reactivo->tipoReactivo->clave == 'MUL'){
                $respAlu = Respuesta::findOne($respuesta->respuesta_alu);
                if(!$respAlu){
                    continue;
                }
                if($respAlu->correcto == 1){
                    $seccion = $respuesta->reactivo->seccion->tipoSeccion->clave;
                    switch ($seccion){
                        case 'USE':
                            $correctasUse = $correctasUse + $respAlu->reactivo->puntos;
                            break;
                        case 'REA':
                            $correctasRea = $correctasRea + $respAlu->reactivo->puntos;
                            break;
                        case 'LIS':
                            $correctasLis = $correctasLis + $respAlu->reactivo->puntos;
                            break;
                    }
                }
            }else if($respuesta->reactivo->tipoReactivo->clave == 'COM'){
                $correcta = false;
                foreach($respuesta->reactivo->respuestasCompletar as $correcta_completar){
                    $respuestas = explode('|', $correcta_completar->respuesta);
                    if(is_array($respuestas)){
                        foreach($respuestas as $respuesta_individual){
                            if(strtolower(trim($respuesta_individual)) == strtolower(trim($respuesta->respuesta_completar))){
                                $correcta = true;
                            }
                        }
                    }
                }
                if($correcta){
                    $seccion = $respuesta->reactivo->seccion->tipoSeccion->clave;
                    switch ($seccion){
                        case 'USE':
                            $correctasUse = $correctasUse + $respuesta->reactivo->puntos;
                            break;
                        case 'REA':
                            $correctasRea = $correctasRea + $respuesta->reactivo->puntos;
                            break;
                        case 'LIS':
                            $correctasLis = $correctasLis + $respuesta->reactivo->puntos;
                            break;
                    }
                }
            }else if($respuesta->reactivo->tipoReactivo->clave == 'REL'){
                $enunciadoAlu = EnunciadoColumn::findOne($respuesta->enunciado_alu);
                if($enunciadoAlu->respuesta_column_id == $respuesta->respuesta_alu){
                    $seccion = $respuesta->reactivo->seccion->tipoSeccion->clave;
                    switch ($seccion) {
                        case 'USE':
                            $correctasUse = $correctasUse + 2;
                            break;
                        case 'REA':
                            $correctasRea = $correctasRea + 2;
                            break;
                        case 'LIS':
                            $correctasLis = $correctasLis + 2;
                            break;
                    }
                }
            }
        }
        $calificaciones = new Calificaciones;
        $calificaciones->calificacionUse = $correctasUse;
        $calificaciones->calificacionReading = $correctasRea;
        $calificaciones->calificacionListening = $correctasLis;
        if(!$calificaciones->save()){
            return false;
        }

        $examenObj = Examen::findOne($examen);
        $alumnoExamen = AlumnoExamen::findOne($id);
        $alumnoExamen->examen_id = $examenObj->id;
        $alumnoExamen->calificaciones_id = $calificaciones->id;
        $alumnoExamen->inactivity = 1;
        $alumnoExamen->update();

        $anteriores = AlumnoExamen::find()
            ->where('alumno_id = '.$alumnoExamen->alumno_id.' AND tipo_examen_id = '.$alumnoExamen->tipo_examen_id)
            ->orderBy('id DESC')
            ->limit(2)
            ->all();
        if(count($anteriores) > 1){
            $anterior = $anteriores[1];
            $anterior->inactivity = 1;
            $anterior->save();
        }

        $secciones = $examenObj->seccions;
        foreach($secciones as $seccion) {
            $tipo = $seccion->tipoSeccion->clave;
            switch ($tipo) {
                case "USE":
                    if($seccion->puntos_seccion > 0)
                        $calificacionUse = ($correctasUse * 100) / $seccion->puntos_seccion;
                    else
                        $calificacionUse = 0;
                    break;
                case 'REA':
                    if($seccion->puntos_seccion > 0)
                        $calificacionRea = ($correctasRea * 100) / $seccion->puntos_seccion;
                    else
                        $calificacionRea = 0;
                    break;
                case 'LIS':
                    if($seccion->puntos_seccion > 0)
                        $calificacionLis = ($correctasLis * 100) / $seccion->puntos_seccion;
                    else
                        $calificacionLis = 0;
                    break;
            }
        }

        $alumno = Alumno::findOne($alumnoExamen->alumno_id);
        $nivel = NivelAlumno::findOne($alumno->nivel_alumno_id);

        // Comienza segmento de MOCK
        $mockType = TipoExamen::find()->where(['clave'=>'MOC'])->one();
        if($examenObj->tipo_examen_id == $mockType->id){
            if($alumno->nivel_mock_id){
                $nivel = NivelAlumno::findOne($alumno->nivel_mock_id);
            }
            $finishedType = StatusExamen::find()->where(['codigo'=>'FIN'])->one();
            $calificacionUse = ($correctasUse * 100) / $examenObj->getPointsFromSection('USE');
            $calificacionRea = ($correctasRea * 100) / $examenObj->getPointsFromSection('REA');
            $calificacionLis = ($correctasLis * 100) / $examenObj->getPointsFromSection('LIS');
            $promedio = ($calificacionLis * .35) + ($calificacionRea * .35) + ($calificacionUse * .30);
            $calificaciones->promedio_use = round($calificacionUse, 0, PHP_ROUND_HALF_DOWN);
            $calificaciones->promedio_listening = round($calificacionLis, 0, PHP_ROUND_HALF_DOWN);
            $calificaciones->promedio_reading = round($calificacionRea, 0, PHP_ROUND_HALF_DOWN);
            $calificaciones->promedio = round($promedio, 0, PHP_ROUND_HALF_DOWN);
            $calificaciones->update();
            $testPassingPercentage = $nivel->nombre == 'C2' ? 70 : 60;
            if($calificaciones->promedio < $testPassingPercentage){
                if($nivel->nombre == 'A2' || $nivel->nombre == 'A1' || $nivel->nombre == 'N/A'){
                    $nuevo_nivel = NivelAlumno::find()->where('nombre="A1"')->one();
                }else if($nivel->nombre == 'B1'){
                    $nuevo_nivel = NivelAlumno::find()->where('nombre="A2"')->one();
                }else if($nivel->nombre == 'B2'){
                    $nuevo_nivel = NivelAlumno::find()->where('nombre="B1"')->one();
                }else if($nivel->nombre == 'C1'){
                    $nuevo_nivel = NivelAlumno::find()->where('nombre="B2"')->one();
                }else if($nivel->nombre == 'C2'){
                    $nuevo_nivel = NivelAlumno::find()->where('nombre="C1"')->one();
                }
                $alumno->nivel_mock_id = $nuevo_nivel->id;
            }
            $alumno->status_examen_id = $finishedType->id;
            $alumno->update();

            $alumnoExamen->fecha_realizacion = time();
            $alumnoExamen->update();
            return true;
        }
        // Termina segmento MOCK

        if($alumno->diagnostic_v2 == '1'){
            $promedio = ($calificacionUse*.29)+($calificacionRea*.355)+($calificacionLis*.355);
            if($nivel->nombre == 'B1'){
                if($promedio >= 50 && $promedio <= 59){
                    $nuevo_nivel = $nivel;
                }else{
                    if($promedio < 50){
                        $nuevo_nivel = NivelAlumno::find()->where('nombre="A1"')->one();
                    }else{
                        $nuevo_nivel = NivelAlumno::find()->where('nombre="B2"')->one();
                    }
                }
            }else if ($nivel->nombre == 'A1'){
                if($promedio <= 59){
                    $nuevo_nivel = $nivel;
                }else{
                    $nuevo_nivel = NivelAlumno::find()->where('nombre="A2"')->one();
                }
            }else if ($nivel->nombre == 'A2'){
                if($promedio < 50){
                    $nuevo_nivel = NivelAlumno::find()->where('nombre="A1"')->one();
                }else if($promedio <= 59){
                    $nuevo_nivel = $nivel;
                }else{
                    $nuevo_nivel = NivelAlumno::find()->where('nombre="B1"')->one();
                }
            }else if ($nivel->nombre == 'B2'){
                if($promedio < 50){
                    $nuevo_nivel = NivelAlumno::find()->where('nombre="B1"')->one();
                }else if($promedio >= 50 && $promedio <= 59){
                    $nuevo_nivel = $nivel;
                }else{
                    $nuevo_nivel = NivelAlumno::find()->where('nombre="C1"')->one();
                }
            }else if ($nivel->nombre == 'C1'){
                if($promedio < 50){
                    $nuevo_nivel = NivelAlumno::find()->where('nombre="B2"')->one();
                }else if($promedio >= 50 && $promedio < 60){
                    $nuevo_nivel = $nivel;
                }else{
                    $nuevo_nivel = NivelAlumno::find()->where('nombre="C2"')->one();
                }
            }else if ($nivel->nombre == 'C2'){
                if($promedio < 70){
                    $nuevo_nivel = NivelAlumno::find()->where('nombre="C1"')->one();
                }else{
                    $nuevo_nivel = $nivel;
                }
            }
        }else{
            $promedio = ($calificacionUse + $calificacionRea + $calificacionLis) / 3;
            if($nivel->nombre == 'C2'){
                $promedio = 0;
            }

            if($promedio < $examenObj->porcentaje){
                if($nivel->nombre == 'A1' || $nivel->nombre == 'N/A'){
                    $nuevo_nivel = NivelAlumno::find()->where('nombre="A1"')->one();
                }else if($nivel->nombre == 'A2'){
                    $nuevo_nivel = NivelAlumno::find()->where('nombre="A1"')->one();
                }else if($nivel->nombre == 'B1'){
                    $nuevo_nivel = NivelAlumno::find()->where('nombre="A2"')->one();
                }else if($nivel->nombre == 'B2'){
                    $nuevo_nivel = NivelAlumno::find()->where('nombre="B1"')->one();
                }else if($nivel->nombre == 'C1'){
                    $nuevo_nivel = NivelAlumno::find()->where('nombre="B2"')->one();
                }else if($nivel->nombre == 'C2'){
                    $nuevo_nivel = NivelAlumno::find()->where('nombre="C1"')->one();
                }
            }else{
                if($nivel->nombre == 'A1' || $nivel->nombre == 'N/A'){
                    $nuevo_nivel = NivelAlumno::find()->where('nombre="A2"')->one();
                }else if($nivel->nombre == 'A2'){
                    $nuevo_nivel = NivelAlumno::find()->where('nombre="B1"')->one();
                }else if($nivel->nombre == 'B1'){
                    $nuevo_nivel = NivelAlumno::find()->where('nombre="B2"')->one();
                }else if($nivel->nombre == 'B2'){
                    $nuevo_nivel = NivelAlumno::find()->where('nombre="C1"')->one();
                }else if($nivel->nombre == 'C1'){
                    $nuevo_nivel = NivelAlumno::find()->where('nombre="C2"')->one();
                }else if($nivel->nombre == 'C2'){
                    $nuevo_nivel = NivelAlumno::find()->where('nombre="C2"')->one();
                }
            }
        }

        $alumnoExamen->fecha_realizacion = time();
        $alumnoExamen->save();

        $diagnosticType = TipoExamen::find()->where(['clave'=>'DIA'])->one();
        if ($alumnoExamen->tipoExamen->clave == $diagnosticType->clave) {
            $writingAnswer = AluexaReactivos::find()
                ->where([
                    'and',
                    ['alumno_examen_id' => $alumnoExamen->id],
                    ['is not', 'respuestaWriting', null]
                ])
                ->one();
            if (!isset($writingAnswer)) {
                $nextLevelExam = AlumnoExamen::find()
                    ->leftJoin('examen', 'alumno_examen.examen_id = examen.id')
                    ->where([
                        'alumno_examen.alumno_id' => $alumnoExamen->alumno_id,
                        'alumno_examen.tipo_examen_id' => $diagnosticType->id,
                        'examen.nivel_alumno_id' => $nuevo_nivel
                    ])
                    ->one();
                if (!isset($nextLevelExam)) {
                    $nextLevelExam = AlumnoExamen::find()
                    ->leftJoin('examen', 'alumno_examen.examen_id = examen.id')
                    ->where([
                        'alumno_examen.alumno_id' => $alumnoExamen->alumno_id,
                        'alumno_examen.tipo_examen_id' => $diagnosticType->id,
                        'examen.nivel_alumno_id' => $nivel
                    ])
                    ->one();
                }
                $newAnswer = new AluexaReactivos();
                $newAnswer->alumno_examen_id = $nextLevelExam->id;
                $writingQuestion = $nextLevelExam->examen->getWritingQuestion();
                $newAnswer->reactivo_id = $writingQuestion->id;
                $newAnswer->respuestaWriting = "";
                $newAnswer->save();
                $isWritingCreated = true;
            }
        }

        if (isset($isWritingCreated) && $isWritingCreated) {
            $status_examen = StatusExamen::find()->where('codigo="AWA"')->one();
        } else {
            $status_examen = StatusExamen::find()->where('codigo="FIN"')->one();
        }
        $alumno->status_examen_id = $status_examen->id;
        $alumno->nivel_alumno_id = $nuevo_nivel->id;
        $alumno->save();
    }
}
