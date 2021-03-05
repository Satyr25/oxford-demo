<?php
namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use common\models\LoginForm;
use frontend\models\InstitutoForm;
use frontend\models\PasswordResetRequestForm;
use app\models\Pais;
use app\models\Estado;

class TutorialsController extends Controller
{
    public $paisesHabilitados = '"MX", "GT", "SV", "HN", "CR", "PA", "BR", "VE", "CO", "EC", "CL", "PE", "AR", "UY", "PY", "PT", "ES", "IT", "SI", "BG", "AM", "UA", "RU", "MN", "CN", "GR", "TR", "IN", "RO", "PR", "EG", "DO", "MA"';

    public function actionIndex(){
        $model = new LoginForm();
        $institutoForm = new InstitutoForm();
        $passwordForm = new PasswordResetRequestForm();
        $paises = ArrayHelper::map(Pais::find()
            ->where('codigo IN (' . $this->paisesHabilitados . ')')
            ->orderBy('nombre')
            ->all(), 'id', 'nombre');
        if ($model->load(Yii::$app->request->post())) {
            if($model->email){
                if ($model->login()){
                    $redirect = '';
                    if($_SERVER['HTTP_HOST'] != 'localhost' && $_SERVER['HTTP_HOST'] != '127.0.0.1'){
                        if(strpos($_SERVER['HTTP_HOST'],'blackrobot') !== false){
                            $redirect = 'http://oxford-admin.blackrobot.mx/';
                        }else if(strpos($_SERVER['HTTP_HOST'],'oxfordtccv2') !== false){
                            $redirect = 'http://www.admin.oxfordtccv2.co.uk';
                        }else{
                            $redirect = 'http://www.admin.oxfordtcc.co.uk';
                        }
                        return $this->redirect($redirect);
                    }
                    return $this->redirect($redirect);
                }
            } else if($model->codigo){
                if ($model->loginStudent()) {
                    if($model->sesion_anterior){
                        Yii::$app->user->logout();
                        Yii::$app->session->setFlash('error', 'This user has previously logged in into another browser. You cannot have two open sessions. Please wait 15 minutes and try again.');
                        return $this->goHome();
                    }
                    $redirect = '';
                    if($_SERVER['HTTP_HOST'] != 'localhost' && $_SERVER['HTTP_HOST'] != '127.0.0.1'){
                        if(strpos($_SERVER['HTTP_HOST'],'blackrobot') !== false){
                            $redirect = 'http://oxford-admin.blackrobot.mx/';
                        }else if(strpos($_SERVER['HTTP_HOST'],'oxfordtccv2') !== false){
                            $redirect = 'http://www.admin.oxfordtccv2.co.uk';
                        }else{
                            $redirect = 'http://www.admin.oxfordtcc.co.uk';
                        }
                        return $this->redirect($redirect);
                    }
                    return $this->redirect($redirect);
                } else {
                    $model->password = '';

                    return $this->render('index', [
                        'model' => $model,
                        'institutoForm' => $institutoForm,
                        'passwordForm' => $passwordForm,
                        'paises' => $paises,
                    ]);
                }
            }
        }else if($passwordForm->load(Yii::$app->request->post())){
            if(!$passwordForm->recover()){
                Yii::$app->session->setFlash('error', 'No user was found with the email provided.');
                return $this->refresh();
            }
            Yii::$app->mailer->compose('_password', [
                    'token' => $passwordForm->token
                ])
                ->setTo($passwordForm->email)
                ->setFrom(["equipo@blackrobot.mx" => "Oxford TCC"])
                ->setSubject("Password Recovery")
                ->send();
            Yii::$app->session->setFlash('success', 'We sent you an email with further information.');
            return $this->refresh();
        }else if ($institutoForm->load(Yii::$app->request->post())) {
            if($institutoForm->guardar()){
                Yii::$app->session->setFlash('success', 'Thanks for signing up! We sent you an email with further information.');
                Yii::$app->mailer->compose()
                    ->setTo($institutoForm->email)
                    ->setFrom(["equipo@blackrobot.mx" => "Oxford TCC"])
                    ->setSubject("Site Registration")
                    ->setHtmlBody($this->renderPartial('_correo'))
                    ->send();
                $cc = Yii::$app->params['email-cc'];
                $mail = Yii::$app->mailer->compose()
                    ->setTo(Yii::$app->params['email-notificacion'])
                    ->setFrom(["equipo@blackrobot.mx" => "Oxford TCC"])
                    ->setSubject("Institute Registration: ".$institutoForm->nombre)
                    ->setHtmlBody($this->renderPartial('_correo_admin',['instituto' => $institutoForm->nombre]));
                if($cc){
                    $cc = explode(',',$cc);
                    $mail->setCc($cc);
                }
                $mail->send();
                return $this->refresh();
            }
            return $this->refresh();
        }else{
            $model->password = '';

            return $this->render('index', [
                'model' => $model,
                'institutoForm' => $institutoForm,
                'passwordForm' => $passwordForm,
                'paises'=>$paises,
            ]);
        }
    }

    public function actionCorreo(){
        Yii::$app->mailer->compose()
            ->setTo(['christian@blackrobot.mx','ana@blackrobot.mx','ramon@blackrobot.mx'])
            ->setFrom(["equipo@blackrobot.mx" => "Oxford TCC"])
            ->setSubject("Site Registration")
            ->setHtmlBody($this->renderPartial('_correo'))
            ->send();
    }

    public function actionSubpais(){
        $out = [];
        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];
            if ($parents != null) {
                $pais_id = $parents[0];
                $estados = Estado::find()->where(['pais_id' => $pais_id])->all();
                foreach ($estados as $estado) {
                    $tempArray = [];
                    $tempArray['id'] = $estado->id;
                    $tempArray['name'] = $estado->estadonombre;
                    array_push($out, $tempArray);
                }
                return Json::encode(['output' => $out, 'selected' => '']);
            }
        }
        return Json::encode(['output' => '', 'selected' => '']);
    }
}
