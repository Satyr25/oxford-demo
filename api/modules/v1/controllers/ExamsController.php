<?php

namespace api\modules\v1\controllers;

use Yii;
use yii\rest\ActiveController;
use yii\base\Exception;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;
use yii\helpers\ArrayHelper;

use api\modules\v1\models\Examen;

class ExamsController extends ActiveController
{
    public $modelClass = 'api\modules\v1\models\Examen';

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => CompositeAuth::className(),
            'except' => [''],
            'authMethods' => [
                HttpBasicAuth::className(),
                HttpBearerAuth::className(),
                QueryParamAuth::className(),
            ],
        ];
        return $behaviors;
    }

    public function actionExamsSync(){
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        // inicializo el array de datos
        $data  = [];
        //obtengo examenes
        $examenes = Examen::findAll(['status' => 1]);
        ArrayHelper::setValue($data, ['data'], array());
        foreach($examenes as $examen){
            if ($examen->diagnostic_v2 == 1) {
                continue;
            }
            $total_reactivos = 0;
            $secciones = $examen->seccions;
            $examenArray = array();
            ArrayHelper::setValue($examenArray, ['id'], $examen->id);
            ArrayHelper::setValue($examenArray, ['use_duration'], $examen->english_duration);
            ArrayHelper::setValue($examenArray, ['reading_duration'], $examen->reading_duration);
            ArrayHelper::setValue($examenArray, ['listening_duration'], $examen->listening_duration);
            ArrayHelper::setValue($examenArray, ['writing_duration'], $examen->writing_duration);
            ArrayHelper::setValue($examenArray, ['porcentaje'], $examen->porcentaje);
            ArrayHelper::setValue($examenArray, ['puntos'], $examen->puntos);
            ArrayHelper::setValue($examenArray, ['nivel_alumno'], $examen->nivel_alumno_id);
            ArrayHelper::setValue($examenArray, ['version'], $examen->variante->nombre);
            ArrayHelper::setValue($examenArray, ['tipo'], $examen->tipoExamen->clave);
            ArrayHelper::setValue($examenArray, ['sections'], []);
            foreach($secciones as $seccion){
                $reactivos = $seccion->reactivosActivos;
                $total_reactivos += count($reactivos);
                $seccionArray = array();
                ArrayHelper::setValue($seccionArray,['clave'], $seccion->tipoSeccion->clave);
                if($seccion->tipoSeccion->clave == 'LIS'){
                    ArrayHelper::setValue($seccionArray,['titulo'], $reactivos[0]->audio->nombre);
                    $audioStrings = explode('/', $reactivos[0]->audio->audio);
                    $audioFileName = substr($audioStrings[1], 0, -3);
                    ArrayHelper::setValue($seccionArray,['audio'], $audioFileName."mp3");
                }
                if($seccion->tipoSeccion->clave == 'REA'){
                    ArrayHelper::setValue($seccionArray,['titulo'], $reactivos[0]->articulo->titulo);
                    ArrayHelper::setValue($seccionArray,['articulo'], $reactivos[0]->articulo->texto);
                }
                ArrayHelper::setValue($seccionArray,['puntos'], $seccion->puntos_seccion);
                ArrayHelper::setValue($seccionArray,['reactivos'], []);
                foreach($reactivos as $reactivo){
                    $respuestas = $reactivo->respuestas;
                    $reactivosArray = array();
                    ArrayHelper::setValue($reactivosArray,['id'], $reactivo->id);
                    ArrayHelper::setValue($reactivosArray,['pregunta'], $reactivo->pregunta);
                    ArrayHelper::setValue($reactivosArray,['instrucciones'], $reactivo->instrucciones);
                    ArrayHelper::setValue($reactivosArray,['puntos'], $reactivo->puntos);
                    ArrayHelper::setValue($reactivosArray,['tipo'], $reactivo->tipoReactivo->clave);
                    ArrayHelper::setValue($reactivosArray,['respuestas'], []);
                    foreach($respuestas as $respuesta){
                        $respuestasArray = array();
                        ArrayHelper::setValue($respuestasArray, ['id'], $respuesta->id);
                        ArrayHelper::setValue($respuestasArray,['enunciado'], $respuesta->respuesta);
                        ArrayHelper::setValue($respuestasArray,['correcto'], $respuesta->correcto);
                        array_push($reactivosArray['respuestas'], $respuestasArray);
                    }
                    array_push($seccionArray['reactivos'], $reactivosArray);
                }
                array_push($examenArray['sections'], $seccionArray);
            }
            ArrayHelper::setValue($examenArray, ['num_reactivos'], $total_reactivos);
            array_push($data['data'], $examenArray);
        }
        return $data;
    }
}
