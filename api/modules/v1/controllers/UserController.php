<?php

namespace api\modules\v1\controllers;

use Yii;
use yii\rest\ActiveController;
use yii\base\Exception;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;

use api\modules\v1\models\User;
use api\modules\v1\models\Acceso;
use api\modules\v1\models\AlumnoExamen;

class UserController extends ActiveController
{
    public $modelClass = 'api\modules\v1\models\User';

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => CompositeAuth::className(),
            'except' => ['login'],
            'authMethods' => [
                HttpBasicAuth::className(),
                HttpBearerAuth::className(),
                QueryParamAuth::className(),
            ],
        ];
        return $behaviors;
    }

    public function actionLogin(){
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $usuario = new User();
        $usuario = $usuario->login(Yii::$app->request->get('codigo'),Yii::$app->request->get('password'));
        if(!$usuario){
            return [
                'success' => false,
                'message' => 'Username or password is invalid.'
            ];
        }

        $acceso = new Acceso();
        $acceso->user_id = $usuario->id;
        $acceso->access_key = $usuario->access_key;
        if(!$acceso->save()){
            return [
                'success' => false,
                'message' => 'Error at creating access key.'
            ];
        }

        $alumno = $usuario->alumno;
        $pendingExam = AlumnoExamen::find()->where(['alumno_id' => $alumno->id, 'fecha_realizacion' => null])->one();
        switch ($pendingExam->tipoExamen->clave) {
            case "DIA":
                if ($alumno->nivelAlumno->clave == "NO") {
                    $examLevel = 1;
                } else {
                    $examLevel = $alumno->nivel_alumno_id;
                }
            break;
            case "MOC":
                if (isset($alumno->nivel_inicio_mock_id)){
                    $examLevel = $alumno->nivel_inicio_mock_id;
                } else {
                    $examLevel = $alumno->nivel_mock_id;
                }
            break;
            case "CER":
                if (isset($alumno->nivel_inicio_certificate_id)){
                    $examLevel = $alumno->nivel_inicio_certificate_id;
                } else {
                    $examLevel = $alumno->nivel_certificate_id;
                }
            break;
        }
        return [
            'success' => true,
            'id' => $alumno ? $alumno->id : 0,
            'access_key' => $acceso->access_key,
            'name' => $alumno ? $alumno->fullName : '',
            'student_level' => $alumno ? $examLevel : 0,
            'exam_pending' => $pendingExam ? $pendingExam->tipoExamen->clave : '',
        ];
    }
}