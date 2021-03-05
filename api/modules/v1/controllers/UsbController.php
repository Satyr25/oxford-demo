<?php

namespace api\modules\v1\controllers;

use Yii;
use yii\rest\ActiveController;
use yii\base\Exception;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;

use api\modules\v1\models\Alumno;
use api\modules\v1\models\AlumnoExamen;
use api\modules\v1\models\Calificaciones;
use api\modules\v1\models\AluexaReactivos;
use api\modules\v1\models\TipoExamen;
use yii\helpers\Json;
use api\modules\v1\models\StatusExamen;
use api\modules\v1\models\Examen;

class UsbController extends ActiveController
{
    public $modelClass = 'api\modules\v1\models\AlumnoExamen';
    public $transaction;


    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => CompositeAuth::className(),
            'except' => ['sync', 'app-request'],
            'authMethods' => [
                HttpBasicAuth::className(),
                HttpBearerAuth::className(),
                QueryParamAuth::className(),
            ],
        ];
        return $behaviors;
    }

    public function actionSync(){
        $datos = file_get_contents('php://input');
        if(!$datos){
            return true;
        }
        $examenes = json_decode($datos, true);
        foreach ($examenes as $alumno_id => $datos_alumno) {
            $alumno = Alumno::findOne($alumno_id);
            if($alumno){
                $alumno->nivel_alumno_id = $datos_alumno['nivel_alumno_id'];
                $alumno->nivel_mock_id = $datos_alumno['nivel_mock_id'];
                $alumno->nivel_inicio_mock_id = $datos_alumno['nivel_inicio_mock_id'];
                $alumno->nivel_certificate_id = $datos_alumno['nivel_certificate_id'];
                $alumno->status_examen_id = 4;
                $alumno->save();
                $examenes = $datos_alumno['examenes'];
                foreach($examenes as $id_alumnoexamen => $examen){
                    $alumno_examen = AlumnoExamen::find()->where('alumno_id = '.$alumno_id.' AND id = '.$id_alumnoexamen)->one();
                    if(!$alumno_examen)
                        $alumno_examen = new AlumnoExamen();
                    $alumno_examen->alumno_id = $alumno->id;
                    $alumno_examen->examen_id = $examen['examen_id'];
                    $alumno_examen->tipo_examen_id = $examen['tipo_examen_id'];
                    $alumno_examen->status = $examen['status'];
                    $alumno_examen->fecha_realizacion = $examen['fecha_realizacion'];
                    $alumno_examen->save();

                    $tipo_examen = TipoExamen::findOne($examen['tipo_examen_id']);
                    if($examen['calificaciones']){
                        $calificaciones = new Calificaciones();
                        $calificaciones->calificacionUse = $examen['calificaciones']['calificacionUse'];
                        $calificaciones->calificacionReading = $examen['calificaciones']['calificacionReading'];
                        $calificaciones->calificacionListening = $examen['calificaciones']['calificacionListening'];
                        $calificaciones->calificacionWriting = $examen['calificaciones']['calificacionWriting'];
                        $calificaciones->save();
                        $alumno_examen->calificaciones_id = $calificaciones->id;
                        $alumno_examen->save();
                    }
                    if($examen['respuestas']){
                        foreach($examen['respuestas'] as $respuesta){
                            $respuesta_alumno = new AluexaReactivos();
                            $respuesta_alumno->alumno_examen_id = $alumno_examen->id;
                            $respuesta_alumno->reactivo_id = $respuesta['reactivo_id'];
                            $respuesta_alumno->respuesta_alu = $respuesta['respuesta_alu'];
                            $respuesta_alumno->enunciado_alu = $respuesta['enunciado_alu'];
                            $respuesta_alumno->respuestaWriting = $respuesta['respuestaWriting'];
                            $respuesta_alumno->calificado = $respuesta['calificado'];
                            $respuesta_alumno->respuesta_completar = $respuesta['respuesta_completar'];
                            $respuesta_alumno->save();
                        }
                    }
                }
                if($tipo_examen->clave == 'DIA' || $tipo_examen->clave == 'CER'){
                    $alumno->status_examen_id = 3;
                    $alumno_examen->save();
                }
            }
        }
    }

    public function actionAppRequest(){
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $this->transaction = \Yii::$app->db->beginTransaction();
        $receivedData = Json::decode(\Yii::$app->request->getRawBody(), true);
        foreach ($receivedData["students"] as $student) {
            $alreadySyncedMock = false;
            $alumno = Alumno::findOne($student["id"]);
            switch ($student["exams"][0]["type"]){
                case "DIA":
                $statusExamen = StatusExamen::find()->where(["codigo" => "AWA"])->one();
                $alumno->status_examen_id = $statusExamen->id;
                $alumno->nivel_alumno_id = $student["level"];
                break;
                case "MOC":
                $statusExamen = StatusExamen::find()->where(["codigo" => "PEN"])->one();
                $alumno->status_examen_id = $statusExamen->id;
                $alumno->nivel_mock_id = $student["level"];
                $alumno->nivel_certificate_id = $student["level"];
                $certificatePending = new AlumnoExamen();
                $certificatePending->alumno_id = $alumno->id;
                $tipoExamen = TipoExamen::find()->where(["clave" => "CER"])->one();
                $certificatePending->tipo_examen_id = $tipoExamen->id;
                $certificatePending->status = 1;
                $alumno->nivel_mock_id = $student["level"];
                break;
                case "CER":
                $statusExamen = StatusExamen::find()->where(["codigo" => "AWA"])->one();
                $alumno->status_examen_id = $statusExamen->id;
                break;
            }
            $alumno->update();
            $tipoExamen = TipoExamen::find()->where(["clave" => $student["exams"][0]["type"]])->one();
            $pendingExam = AlumnoExamen::find()->where([
                "alumno_id" => $alumno->id,
                "tipo_examen_id" => $tipoExamen->id,
                "fecha_realizacion" => null
                ]
            )->one();
            if($pendingExam) {
                $pendingExam->delete();
            }
            foreach ($student["exams"] as $exam) {
                $tipoExamen = TipoExamen::find()->where(["clave" => $exam["type"]])->one();
                $examPreviouslySynced = AlumnoExamen::find()
                    ->where(["fecha_realizacion" => $exam["date"], "alumno_id" => $alumno->id])
                    ->one();
                $examToSync = Examen::findOne($exam["id"]);
                $examDone = AlumnoExamen::find()
                    ->joinWith("examen")
                    ->where([
                        "alumno_examen.alumno_id" => $alumno->id,
                        "alumno_examen.tipo_examen_id" => $tipoExamen->id,
                        "examen.nivel_alumno_id" => $examToSync->nivel_alumno_id
                    ])
                    ->one();
                if ($examPreviouslySynced || $examDone) {
                    $alreadySyncedMock = true;
                    continue;
                }
                $calificaciones = new Calificaciones();
                $calificaciones->calificacionUse = $exam["score"]["useScore"];
                $calificaciones->calificacionReading = $exam["score"]["readingScore"];
                $calificaciones->calificacionListening = $exam["score"]["listeningScore"];
                switch ($examToSync->tipoExamen->clave) {
                    case 'DIA':
                        $calificaciones->setAverageScoreForDiagnostic($examToSync);
                        break;
                    case 'MOC':
                        $calificaciones->setAverageScoreForMock($examToSync);
                        break;
                    case 'CER':
                        $calificaciones->setAverageScoreForCertificate($examToSync, $alumno->instituteProgram);
                        break;
                }
                if (!$calificaciones->save()) {
                    $this->transaction->rollback();
                    return [
                        "success" => false,
                        "message" => "Error at creating scores"
                    ];
                }
                $alumnoExamen = new AlumnoExamen();
                $alumnoExamen->alumno_id = $alumno->id;
                $alumnoExamen->examen_id = $exam["id"];
                $alumnoExamen->tipo_examen_id = $tipoExamen->id;
                $alumnoExamen->calificaciones_id = $calificaciones->id;
                $alumnoExamen->status = 1;
                $alumnoExamen->fecha_realizacion = $exam["date"];
                if (!$alumnoExamen->save()){
                    $this->transaction->rollback();
                    return [
                        "success" => false,
                        "message" => "Error at creating exam"
                    ];
                }

                foreach ($exam["answers"] as $answer){
                    $respuesta = new AluexaReactivos();
                    $respuesta->alumno_examen_id = $alumnoExamen->id;
                    $respuesta->reactivo_id = $answer["question"];
                    if ($answer["answer"] == 0) {
                        $respuesta->respuestaWriting = $answer["writingAnswer"];
                    } else {
                        $respuesta->respuesta_alu = $answer["answer"];
                    }
                    if (!$respuesta->save()) {
                        $this->transaction->rollback();
                        return [
                            "success" => false,
                            "message" => "Error at creating exam"
                        ];
                    }
                }
            }
            if (isset($certificatePending) && !$alreadySyncedMock) {
                if (!$certificatePending->save()) {
                    $this->transaction->rollback();
                    return [
                        "success" => false,
                        "message" => "Error at creating exam certificate"
                    ];
                }
            }
        }
        $this->transaction->commit();
        return [
            "success" => true,
            "message" => "Sync done"
        ];
    }
}
