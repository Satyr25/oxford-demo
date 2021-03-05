<?php
namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

use app\models\Alumno;
use app\models\Examen;
use app\models\AlumnoExamen;
use app\models\search\StudentExamsSearch;
use backend\models\forms\ExamenResueltoForm;
use backend\models\forms\ExamenForm;
use backend\models\forms\WritingForm;
use backend\models\forms\WritingResueltoForm;
use app\models\AluexaReactivos;
use app\models\EnunciadoColumn;
use app\models\Calificaciones;
use app\models\Respuesta;
use app\models\TipoSeccion;
use app\models\Seccion;
use app\models\NivelAlumno;
use app\models\StatusExamen;
use app\models\TipoExamen;
use app\models\WritingData;
use app\models\Reactivo;
use common\models\User;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\IOFactory;

class StudentsController extends Controller
{

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['index', 'solve-exam', 'save-answers', 'calificar','results','get-calificaciones','writing', 'save-writing', 'save-exam-random', 'save-writing-partial', 'save-used-time'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    public function beforeAction($action)
    {
        $rol = Yii::$app->user->identity->tipoUsuario->clave;

        if ($rol == 'INS' || $rol == 'ACA') {
            return $this->redirect(\Yii::$app->urlManager->createUrl("site/index"));
        }
        if(Yii::$app->user->id){
            $user = User::findOne(Yii::$app->user->identity->id);
            $alumno = Alumno::find()->where('id='.$user->alumno_id)->one();
            if($alumno->status == '0'){
                $user->sesion_info = null;
                $user->save();
                $redirect = '';
                if($_SERVER['HTTP_HOST'] != 'localhost' && $_SERVER['HTTP_HOST'] != '127.0.0.1'){
                    if(strpos($_SERVER['HTTP_HOST'],'blackrobot') !== false){
                        $redirect = 'http://oxford.blackrobot.mx/';
                    }else if(strpos($_SERVER['HTTP_HOST'],'vejart') !== false){
                        $redirect = 'http://www.vejart.com';
                    }else{
                        $redirect = 'http://www.oxfordtccv2.co.uk';
                    }
                }
                Yii::$app->user->logout();
                Yii::$app->session->setFlash('error', 'The user is inactive.');
                return $this->redirect($redirect)->send();
            }
            if($user->sesion_info){
                $sesion_guardada = json_decode($user->sesion_info);
                $sesion_guardada = $sesion_guardada->session_id;
                if($sesion_guardada != Yii::$app->session->getId()){
                    $redirect = '';
                    if($_SERVER['HTTP_HOST'] != 'localhost' && $_SERVER['HTTP_HOST'] != '127.0.0.1'){
                        if(strpos($_SERVER['HTTP_HOST'],'blackrobot') !== false){
                            $redirect = 'http://oxford.blackrobot.mx/';
                        }else if(strpos($_SERVER['HTTP_HOST'],'vejart') !== false){
                            $redirect = 'http://www.vejart.com';
                        }else{
                            $redirect = 'http://www.oxfordtccv2.co.uk';
                        }
                    }
                    Yii::$app->user->logout();
                    Yii::$app->session->setFlash('error', 'La sesión ha expirado');
                    return $this->redirect($redirect)->send();
                }
            }
            $datos_sesion = [
                'session_id' => Yii::$app->session->getId(),
                'hora' => time()
            ];
            $user->sesion_info = json_encode($datos_sesion);
            $user->update();
        }

        return parent::beforeAction($action);
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    public function actionIndex()
    {
        if(!Yii::$app->user->identity->alumno->id){
            return $this->goHome();
        }
        $searchModel = new StudentExamsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProviderDone = $searchModel->searchDoneExams(Yii::$app->request->queryParams);
        $alumno = Alumno::findOne(Yii::$app->user->identity->alumno->id);

//        $alumnoExamen = AlumnoExamen::findOne($id);
//        var_dump($alumno->ceritificate_v2);exit;


        return $this->render('index', [
            'alumno'=>$alumno,
            // 'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'dataProviderDone' => $dataProviderDone
        ]);
    }

    public function actionSolveExam($id){
        $alumnoExamen = AlumnoExamen::findOne($id);
        switch($alumnoExamen->tipoExamen->clave)
        {
            case 'DIA':
                $examenForm = new ExamenResueltoForm();
                $respuestasGuardadas = AluexaReactivos::find()->where([
                    'alumno_examen_id' => $alumnoExamen->id
                    ])
                    ->all();
                if ($alumnoExamen->fecha_realizacion) {
                    return $this->redirect(['students/index']);
                }

                if ($alumnoExamen->examen_id && !$respuestasGuardadas) {
                    $examen = $alumnoExamen->examen;
                    $tiempos = [
                        'REA' => $examen->reading_duration,
                        'LIS' => $examen->listening_duration,
                        'USE' => $examen->english_duration,
                        'WRI' => $examen->writing_duration
                    ];
                    $tiemposUsados = [
                        'REA' => ($alumnoExamen->reading_used_time ? $alumnoExamen->reading_used_time : 0),
                        'LIS' => ($alumnoExamen->listening_used_time ? $alumnoExamen->listening_used_time : 0),
                        'USE' => ($alumnoExamen->use_used_time ? $alumnoExamen->use_used_time : 0),
                    ];

                    return $this->render('examen', [
                        'examen' => $examen,
                        'examenForm' => $examenForm,
                        'idAlumnoExamen' => $alumnoExamen->id,
                        'tiempos' => $tiempos,
                        'tiemposUsados' => $tiemposUsados,
                    ]);
                } else if ($respuestasGuardadas) {
                    $examenForm->cargaRespuestas($respuestasGuardadas);
                    $examen = $respuestasGuardadas[0]->alumnoExamen->examen;
                    $tiempos = [
                        'REA' => $examen->reading_duration,
                        'LIS' => $examen->listening_duration,
                        'USE' => $examen->english_duration,
                        'WRI' => $examen->writing_duration
                    ];
                    $tiemposUsados = [
                        'REA' => ($alumnoExamen->reading_used_time ? $alumnoExamen->reading_used_time : 0),
                        'LIS' => ($alumnoExamen->listening_used_time ? $alumnoExamen->listening_used_time : 0),
                        'USE' => ($alumnoExamen->use_used_time ? $alumnoExamen->use_used_time : 0),
                    ];

                    return $this->render('examen', [
                        'examen' => $examen,
                        'examenForm' => $examenForm,
                        'idAlumnoExamen' => $alumnoExamen->id,
                        'tiempos' => $tiempos,
                        'tiemposUsados' => $tiemposUsados,
                    ]);
                }

                $nivelInicio = NivelAlumno::findOne(Yii::$app->user->identity->alumno->nivel_alumno_id);
                if($nivelInicio->clave == 'NO'){
                    $nivelInicio = NivelAlumno::find()->where('clave="A1"')->one();
                }
                if($alumnoExamen->alumno->diagnostic_v2 == '1'){
                    $examenes = Examen::find()
                        ->joinWith('tipoExamen')
                        ->where([
                        'nivel_alumno_id' => $nivelInicio->id,
                        'clave' => 'DIA',
                        'status' => 1,
                        'diagnostic_v2' => 1
                    ])->all();
                }else if($alumnoExamen->alumno->diagnostic_v3 == '1'){
                    $examenes = Examen::find()
                        ->joinWith('tipoExamen')
                        ->where([
                        'nivel_alumno_id' => $nivelInicio->id,
                        'clave' => 'DIA',
                        'status' => 1,
                        'diagnostic_v3' => 1
                    ])->all();
                }else{
                    $examenes = Examen::find()
                        ->joinWith('tipoExamen')
                        ->where([
                        'nivel_alumno_id' => $nivelInicio->id,
                        'clave' => 'DIA',
                        'status' => 1,
                        'diagnostic_v2' => 0
                    ])->all();
                }
                if(count($examenes) == 0){
                    Yii::$app->session->setFlash('error', "There was an error assigning the exam.");
                    return $this->redirect(['students/index']);
                }

                $versiones = count($examenes);
                $random = rand(0, $versiones - 1);
                $examen = $examenes[$random];
                $tiempos = [
                    'REA' => $examen->reading_duration,
                    'LIS' => $examen->listening_duration,
                    'USE' => $examen->english_duration,
                    'WRI' => $examen->writing_duration
                ];
                $tiemposUsados = [
                    'REA' => ($alumnoExamen->reading_used_time ? $alumnoExamen->reading_used_time : 0),
                    'LIS' => ($alumnoExamen->listening_used_time ? $alumnoExamen->listening_used_time : 0),
                    'USE' => ($alumnoExamen->use_used_time ? $alumnoExamen->use_used_time : 0),
                    ];

                $statusExamen = StatusExamen::find()->where(['codigo' => 'PRO'])->one();
                $alumno = $alumnoExamen->alumno;
                $alumno->status_examen_id = $statusExamen->id;
                $alumno->update();

                $alumnoExamen->examen_id = $examen->id;
                $alumnoExamen->ultima_actualizacion = time();
                if(!$alumnoExamen->update()){
                    Yii::$app->session->setFlash('error', "There was an error assigning the exam.");
                    return $this->redirect(['students/index']);
                }

                return $this->render('examen', [
                    'examen' => $examen,
                    'examenForm' => $examenForm,
                    'idAlumnoExamen' => $alumnoExamen->id,
                    'tiemposUsados' => $tiemposUsados,
                    'tiempos' => $tiempos
                ]);
            break;
            case 'MOC':
                $examenForm = new ExamenResueltoForm();
                $respuestasGuardadas = AluexaReactivos::find()->where([
                    'alumno_examen_id' => $alumnoExamen->id
                    ])
                    ->all();
                if ($alumnoExamen->fecha_realizacion) {
                    return $this->redirect(['students/index']);
                }

                if ($alumnoExamen->examen_id && !$respuestasGuardadas) {
                    $examen = $alumnoExamen->examen;
                    $tiempos = [
                        'REA' => $examen->reading_duration,
                        'LIS' => $examen->listening_duration,
                        'USE' => $examen->english_duration,
                        'WRI' => $examen->writing_duration
                    ];
                    $tiemposUsados = [
                        'REA' => ($alumnoExamen->reading_used_time ? $alumnoExamen->reading_used_time : 0),
                        'LIS' => ($alumnoExamen->listening_used_time ? $alumnoExamen->listening_used_time : 0),
                        'USE' => ($alumnoExamen->use_used_time ? $alumnoExamen->use_used_time : 0),
                    ];

                    return $this->render('examen', [
                        'examen' => $examen,
                        'examenForm' => $examenForm,
                        'idAlumnoExamen' => $alumnoExamen->id,
                        'tiempos' => $tiempos,
                        'tiemposUsados' => $tiemposUsados,
                    ]);
                } else if ($respuestasGuardadas) {
                    $examenForm->cargaRespuestas($respuestasGuardadas);
                    $examen = $respuestasGuardadas[0]->alumnoExamen->examen;
                    $tiempos = [
                        'REA' => $examen->reading_duration,
                        'LIS' => $examen->listening_duration,
                        'USE' => $examen->english_duration,
                        'WRI' => $examen->writing_duration
                    ];
                    $tiemposUsados = [
                        'REA' => ($alumnoExamen->reading_used_time ? $alumnoExamen->reading_used_time : 0),
                        'LIS' => ($alumnoExamen->listening_used_time ? $alumnoExamen->listening_used_time : 0),
                        'USE' => ($alumnoExamen->use_used_time ? $alumnoExamen->use_used_time : 0),
                    ];

                    return $this->render('examen', [
                        'examen' => $examen,
                        'examenForm' => $examenForm,
                        'idAlumnoExamen' => $alumnoExamen->id,
                        'tiempos' => $tiempos,
                        'tiemposUsados' => $tiemposUsados,
                    ]);
                }

                if(Yii::$app->user->identity->alumno->nivel_mock_id){
                    $nivelInicio = NivelAlumno::findOne(Yii::$app->user->identity->alumno->nivel_mock_id);
                }else{
                    $nivelInicio = NivelAlumno::findOne(Yii::$app->user->identity->alumno->nivel_alumno_id);
                }
                if($nivelInicio->clave == 'NO'){
                    $nivelInicio = NivelAlumno::find()->where('clave="A1"')->one();
                }

                if($nivelInicio->clave == 'A1'){
                    $examenes = Examen::find()
                        ->joinWith('tipoExamen')
                        ->where([
                        'nivel_alumno_id' => $nivelInicio->id,
                        'clave' => 'MOC',
                        'status' => 1
                    ])->andWhere(['!=','variante_id', 1])->all();
                }else{
                    $examenes = Examen::find()
                        ->joinWith('tipoExamen')
                        ->where([
                        'nivel_alumno_id' => $nivelInicio->id,
                        'clave' => 'MOC',
                        'status' => 1
                    ])->all();
                }

                $versiones = count($examenes);
                $random = rand(0, $versiones - 1);
                $examen = $examenes[$random];
                $tiempos = [
                    'REA' => $examen->reading_duration,
                    'LIS' => $examen->listening_duration,
                    'USE' => $examen->english_duration,
                    'WRI' => $examen->writing_duration
                ];
                $tiemposUsados = [
                    'REA' => ($alumnoExamen->reading_used_time ? $alumnoExamen->reading_used_time : 0),
                    'LIS' => ($alumnoExamen->listening_used_time ? $alumnoExamen->listening_used_time : 0),
                    'USE' => ($alumnoExamen->use_used_time ? $alumnoExamen->use_used_time : 0),
                    ];

                $statusExamen = StatusExamen::find()->where(['codigo' => 'PRO'])->one();
                $alumno = $alumnoExamen->alumno;
                $alumno->status_examen_id = $statusExamen->id;
                $alumno->update();

                $alumnoExamen->examen_id = $examen->id;
                $alumnoExamen->ultima_actualizacion = time();
                if($alumnoExamen->update() === false){
                    Yii::$app->session->setFlash('error', "There was an error assigning the exam.");
                    return $this->redirect(['students/index']);
                }

                return $this->render('examen', [
                    'examen' => $examen,
                    'examenForm' => $examenForm,
                    'idAlumnoExamen' => $alumnoExamen->id,
                    'tiemposUsados' => $tiemposUsados,
                    'tiempos' => $tiempos
                ]);
            break;
            case 'CER':
            $examenForm = new ExamenResueltoForm();
            $respuestasGuardadas = AluexaReactivos::find()->where([
                'alumno_examen_id' => $alumnoExamen->id
                ])
                ->all();
            if ($alumnoExamen->fecha_realizacion) {
                return $this->redirect(['students/index']);
            }

            if ($alumnoExamen->examen_id && !$respuestasGuardadas) {
                $examen = $alumnoExamen->examen;
                $tiempos = [
                    'REA' => $examen->reading_duration,
                    'LIS' => $examen->listening_duration,
                    'USE' => $examen->english_duration,
                    'WRI' => $examen->writing_duration
                ];
                $tiemposUsados = [
                    'REA' => ($alumnoExamen->reading_used_time ? $alumnoExamen->reading_used_time : 0),
                    'LIS' => ($alumnoExamen->listening_used_time ? $alumnoExamen->listening_used_time : 0),
                    'USE' => ($alumnoExamen->use_used_time ? $alumnoExamen->use_used_time : 0),
                ];

                return $this->render('examen', [
                    'examen' => $examen,
                    'examenForm' => $examenForm,
                    'idAlumnoExamen' => $alumnoExamen->id,
                    'tiempos' => $tiempos,
                    'tiemposUsados' => $tiemposUsados,
                ]);
            } else if ($respuestasGuardadas) {
                $examenForm->cargaRespuestas($respuestasGuardadas);
                $examen = $respuestasGuardadas[0]->alumnoExamen->examen;
                $tiempos = [
                    'REA' => $examen->reading_duration,
                    'LIS' => $examen->listening_duration,
                    'USE' => $examen->english_duration,
                    'WRI' => $examen->writing_duration
                ];
                $tiemposUsados = [
                    'REA' => ($alumnoExamen->reading_used_time ? $alumnoExamen->reading_used_time : 0),
                    'LIS' => ($alumnoExamen->listening_used_time ? $alumnoExamen->listening_used_time : 0),
                    'USE' => ($alumnoExamen->use_used_time ? $alumnoExamen->use_used_time : 0),
                ];

                return $this->render('examen', [
                    'examen' => $examen,
                    'examenForm' => $examenForm,
                    'idAlumnoExamen' => $alumnoExamen->id,
                    'tiempos' => $tiempos,
                    'tiemposUsados' => $tiemposUsados,
                ]);
            }

            $nivel = NivelAlumno::findOne(Yii::$app->user->identity->alumno->nivel_certificate_id);
            if($nivel->clave == 'NO'){
                $nivel = NivelAlumno::find()->where('clave="A1"')->one();
            }

            if($alumnoExamen->alumno->certificate_v2 == '1'){
                $examenes = Examen::find()
                    ->joinWith('tipoExamen')
                    ->where([
                    'nivel_alumno_id' => $nivel->id,
                    'clave' => 'CER',
                    'status' => 1,
                    'certificate_v2' => 1
                ])->all();
            }else{
                $examenes = Examen::find()
                    ->joinWith('tipoExamen')
                    ->where([
                    'nivel_alumno_id' => $nivel->id,
                    'clave' => 'CER',
                    'status' => 1,
                    'certificate_v2' => NULL
                ])->all();
            }

            $versiones = count($examenes);
            $random = rand(0, $versiones - 1);
            $examen = $examenes[$random];
            $tiempos = [
                'REA' => $examen->reading_duration,
                'LIS' => $examen->listening_duration,
                'USE' => $examen->english_duration,
                'WRI' => $examen->writing_duration
            ];
            $tiemposUsados = [
                'REA' => ($alumnoExamen->reading_used_time ? $alumnoExamen->reading_used_time : 0),
                'LIS' => ($alumnoExamen->listening_used_time ? $alumnoExamen->listening_used_time : 0),
                'USE' => ($alumnoExamen->use_used_time ? $alumnoExamen->use_used_time : 0),
                ];

            $statusExamen = StatusExamen::find()->where(['codigo' => 'PRO'])->one();
            $alumno = $alumnoExamen->alumno;
            $alumno->status_examen_id = $statusExamen->id;
            $alumno->update();

            $alumnoExamen->examen_id = $examen->id;
            $alumnoExamen->ultima_actualizacion = time();
            if($alumnoExamen->update() === false){
                Yii::$app->session->setFlash('error', "There was an error assigning the exam.");
                return $this->redirect(['students/index']);
            }

            return $this->render('examen', [
                'examen' => $examen,
                'examenForm' => $examenForm,
                'idAlumnoExamen' => $alumnoExamen->id,
                'tiemposUsados' => $tiemposUsados,
                'tiempos' => $tiempos
            ]);
            break;

        }
    }

    public function actionSaveAnswers(){
        $examenForm = new ExamenResueltoForm();
        if ($examenForm->load(Yii::$app->request->post())) {
            if($examenForm->guardarRespuestas()){
                return true;
            } else {
                if (isset($examenForm->id)) {
                    $alumnoExamen = AlumnoExamen::findOne($examenForm->id);
                    $currentDate = time();
                    Yii::$app->mailer->compose()
                        ->setFrom('no_reply@oxford.tcc.co.uk')
                        ->setTo('christian@blackrobot.mx')
                        ->setSubject('Error at saving answers')
                        ->setTextBody("
                            alumno_examen_id: {$examenForm->id}
                            examen_id: {$alumnoExamen->examen_id}
                            alumno_id: {$alumnoExamen->alumno_id}
                            error: {$examenForm->error}
                            fecha: {$currentDate}
                        ")
                        ->send();
                }
            }
        }
        return false;
    }

    public function actionResults(){
        $alumno = Alumno::findOne(Yii::$app->user->identity->alumno->id);
        $diagnosticType = TipoExamen::find()->where(['clave'=>'DIA'])->one();
        $mockType = TipoExamen::find()->where(['clave'=>'MOC'])->one();
        $alumnoExamens = AlumnoExamen::find()
            ->join('INNER JOIN', 'calificaciones', 'calificaciones.id = alumno_examen.calificaciones_id')
            ->join('INNER JOIN', 'examen', 'examen.id = alumno_examen.examen_id')
            ->where(
                'alumno_examen.alumno_id = '.$alumno->id.
                ' AND examen.nivel_alumno_id = '.$alumno->nivel_alumno_id.
                ' AND calificacionWriting IS NOT NULL')
            ->all();
        $examenes = ArrayHelper::map($alumnoExamens, 'id', 'examen.examenNameNoVersion');

        return $this->render('results', [
            'alumno' => $alumno,
            'examenes' => $examenes,
            'mockType' => $mockType->id,
            'diagnosticType' => $diagnosticType->id
            ]
        );
    }

    public function actionCalificar($id, $examen){
        $connection = \Yii::$app->db;
        $transaction = $connection->beginTransaction();
        $session = Yii::$app->session;

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
            }
        }

        $alumnoExamen = AlumnoExamen::findOne($id);

        if($alumnoExamen->calificaciones_id){
            $calificaciones = Calificaciones::findOne($alumnoExamen->calificaciones_id);
        }else{
            $calificaciones = new Calificaciones();
        }
        $calificaciones->calificacionUse = $correctasUse;
        $calificaciones->calificacionReading = $correctasRea;
        $calificaciones->calificacionListening = $correctasLis;
        if(!$calificaciones->save())
        {
            $transaction->rollback();
            return $this->redirect(['students/index']);
        }

        $examenObj = Examen::findOne($examen);
        $alumnoExamen->examen_id = $examenObj->id;
        $alumnoExamen->calificaciones_id = $calificaciones->id;
        $alumnoExamen->update();

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
            $calificacionUse = ($correctasUse * 100) / $examenObj->getTotalPointsBySectionType('USE');
            $calificacionRea = ($correctasRea * 100) / $examenObj->getTotalPointsBySectionType('REA');
            $calificacionLis = ($correctasLis * 100) / $examenObj->getTotalPointsBySectionType('LIS');
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

            $transaction->commit();

            $instituto = $alumno->grupo->instituto;
            $total_alumnos = $instituto->totalAlumnos();
            $mocks_realizados = $instituto->examenesRealizados($mockType->id);
            if($mocks_realizados >= $total_alumnos){
                $instituto->finalizacion_mock = time();
                $instituto->save();
                $this->exportMock($instituto);
            }

            return $this->redirect(['students/index']);
        }
        // Termina segmento MOCK

        // Inicia segmento Certificate

        $cerType = TipoExamen::find()->where(['clave'=>'CER'])->one();
        if($examenObj->tipo_examen_id == $cerType->id){

            $transaction->commit();

            $writingForm = new WritingResueltoForm();
            $seccionTipo = TipoSeccion::find()->where(['clave'=>'WRI'])->one();
            if($alumnoExamen->examen->tipoExamen->clave == 'CER' && $alumnoExamen->examen->certificate_v2 == 1){
                $writing_data = WritingData::find()->select('reactivo_id')->where(['alumno_examen_id' => $alumnoExamen->id,'completed'=>1])->all();
                if(count($writing_data) == 2){
                    return $this->redirect(['students/index']);
                }
                if(count($writing_data) == 0){
                    $writing = Reactivo::find()
                        ->join('INNER JOIN', 'seccion', 'reactivo.seccion_id = seccion.id')
                        ->join('INNER JOIN', 'tipo_seccion', 'tipo_seccion.id = seccion.tipo_seccion_id')
                        ->where(
                            'seccion.examen_id = '.$alumnoExamen->examen->id.' AND tipo_seccion.clave = "WRI" AND reactivo.status = 1'
                        )->one();
                    $writing_data = count(WritingData::find()->select('reactivo_id')->where(['alumno_examen_id' => $alumnoExamen->id])->all());
                    if($writing_data == 0){
                        $writing_data = new WritingData();
                        $writing_data->alumno_examen_id = $alumnoExamen->id;
                        $writing_data->reactivo_id = $writing->id;
                        $writing_data->save();
                    }
                }else{
                    $writing = Reactivo::find()
                        ->join('INNER JOIN', 'seccion', 'reactivo.seccion_id = seccion.id')
                        ->join('INNER JOIN', 'tipo_seccion', 'tipo_seccion.id = seccion.tipo_seccion_id')
                        ->where(
                            'reactivo.id != '.$writing_data[0]->reactivo_id.' AND seccion.examen_id = '.$alumnoExamen->examen->id.' AND tipo_seccion.clave = "WRI" AND reactivo.status = 1'
                        )->one();
                }
                $writingSaved = AluexaReactivos::find()
                    ->where(['alumno_examen_id' => $alumnoExamen->id])
                    ->andWhere(['reactivo_id' => $writing->id])
                    ->one();
                if($writingSaved){
                    $writingForm->texto = $writingSaved->respuestaWriting;
                }
                $writing_data = WritingData::find()->where('alumno_examen_id='.$alumnoExamen->id.' AND reactivo_id='.$writing->id)->one();
                return $this->render('writing_v2', [
                    'alumnoExamen' => $alumnoExamen,
                    'writing' => $writing,
                    'writingForm' => $writingForm,
                    'tiempo' => $examenObj->writing_duration,
                    'tiempo_usado' => $writing_data->time,
                    'imagenes' => $writing->imagenes()
                ]);
            }else{
                $writing = Seccion::find()->where(['examen_id' => $alumnoExamen->examen_id, 'tipo_seccion_id' => $seccionTipo->id])->one();

                if (!$writing) {
                    return $this->redirect(['students/index']);
                }

                $writingSaved = AluexaReactivos::find()
                    ->where(['alumno_examen_id' => $alumnoExamen->id])
                    ->andWhere(['is not', 'respuestaWriting', null])
                    ->one();
                if($writingSaved){
                    $writingForm->texto = $writingSaved->respuestaWriting;
                }
                return $this->render('writing', [
                    'alumnoExamen' => $alumnoExamen,
                    'writing' => $writing,
                    'writingForm' => $writingForm,
                    'tiempo' => $examenObj->writing_duration
                ]);
            }
        }
        // Termina segmento Certificate

        //Inicia nuevo Diagnostic

        if($alumno->diagnostic_v2 == '1' || $alumno->diagnostic_v3 == '1'){
            $promedio = ($calificacionUse*.29)+($calificacionRea*.355)+($calificacionLis*.355);
            $calificaciones->promedio = round($promedio, 0, PHP_ROUND_HALF_DOWN);
            $calificaciones->promedio_use = round($calificacionUse, 0, PHP_ROUND_HALF_DOWN);
            $calificaciones->promedio_listening = round($calificacionLis, 0, PHP_ROUND_HALF_DOWN);
            $calificaciones->promedio_reading = round($calificacionRea, 0, PHP_ROUND_HALF_DOWN);
            $calificaciones->update();
            if($nivel->nombre == 'B1'){
                if($promedio >= 50 && $promedio <= 59){
                    $writing = $this->asignWriting($alumnoExamen, $examenObj->writing_duration);
                    if(!$writing){
                        $transaction->rollback();
                        return $this->redirect(['students/index']);
                    }
                    $transaction->commit();
                    $session->set('past_exam', $examenObj->id);
                    return $this->render('writing', $writing);
                }else{
                    if($promedio < 50){
                        $nuevo_nivel = NivelAlumno::find()->where('nombre="A1"')->one();
                    }else{
                        $nuevo_nivel = NivelAlumno::find()->where('nombre="B2"')->one();
                    }
                    $nuevo_examen = $this->asignExam($alumnoExamen, $alumno, $nuevo_nivel);
                    if(!$nuevo_examen){
                        $transaction->rollback();
                        return $this->redirect(['students/index']);
                    }
                    $transaction->commit();
                    $session->set('past_exam', $examenObj->id);
                    return $this->redirect($nuevo_examen);
                }
            }else if ($nivel->nombre == 'A1'){
                if($promedio <= 59){
                    $writing = $this->asignWriting($alumnoExamen, $examenObj->writing_duration);
                    if(!$writing){
                        $transaction->rollback();
                        return $this->redirect(['students/index']);
                    }
                    $transaction->commit();
                    $session->set('past_exam', $examenObj->id);
                    return $this->render('writing', $writing);
                }else{
                    $nuevo_nivel = NivelAlumno::find()->where('nombre="A2"')->one();
                    $nuevo_examen = $this->asignExam($alumnoExamen, $alumno, $nuevo_nivel);
                    if(!$nuevo_examen){
                        $transaction->rollback();
                        return $this->redirect(['students/index']);
                    }
                    $transaction->commit();
                    $session->set('past_exam', $examenObj->id);
                    return $this->redirect($nuevo_examen);
                }
            }else if ($nivel->nombre == 'A2'){
                if($promedio < 50){
                    $nuevo_nivel = NivelAlumno::find()->where('nombre="A1"')->one();
                    $examen_a1 = $alumnoExamen->porNivelAlumno($nuevo_nivel->id,$alumno->id,1);
                    if(!$examen_a1){
                        $transaction->rollback();
                        return $this->redirect(['students/index']);
                    }
                    $alumnoExamen->fecha_realizacion = time();
                    $alumnoExamen->fecha_realizacion;
                    if(!$alumnoExamen->save()){
                        $transaction->rollback();
                        return false;
                    }
                    $alumno->nivel_alumno_id = $nuevo_nivel->id;
                    if (!$alumno->save()) {
                        $transaction->rollback();
                        return false;
                    }
                    $writing = $this->asignWriting($examen_a1, $examen_a1->examen->writing_duration);
                    if(!$writing){
                        $transaction->rollback();
                        return $this->redirect(['students/index']);
                    }
                    $transaction->commit();
                    $session->set('past_exam', $examenObj->id);
                    return $this->render('writing', $writing);
                }else if($promedio <= 59){
                    $writing = $this->asignWriting($alumnoExamen, $examenObj->writing_duration);
                    if(!$writing){
                        $transaction->rollback();
                        return $this->redirect(['students/index']);
                    }
                    $transaction->commit();
                    $session->set('past_exam', $examenObj->id);
                    return $this->render('writing', $writing);
                }else{
                    $nuevo_nivel = NivelAlumno::find()->where('nombre="B1"')->one();
                    $examen_b1 = $alumnoExamen->porNivelAlumno($nuevo_nivel->id,$alumno->id,1);
                    if(!$examen_b1){
                        $transaction->rollback();
                        return $this->redirect(['students/index']);
                    }
                    $alumnoExamen->fecha_realizacion = time();
                    $alumnoExamen->fecha_realizacion;
                    if(!$alumnoExamen->save()){
                        $transaction->rollback();
                        return false;
                    }
                    $alumno->nivel_alumno_id = $nuevo_nivel->id;
                    if (!$alumno->save()) {
                        $transaction->rollback();
                        return false;
                    }
                    $writing = $this->asignWriting($examen_b1, $examen_b1->examen->writing_duration);
                    if(!$writing){
                        $transaction->rollback();
                        return $this->redirect(['students/index']);
                    }
                    $transaction->commit();
                    $session->set('past_exam', $examenObj->id);
                    return $this->render('writing', $writing);
                }
            }else if ($nivel->nombre == 'B2'){
                if($promedio < 50){
                    $nuevo_nivel = NivelAlumno::find()->where('nombre="B1"')->one();
                    $examen_b1 = $alumnoExamen->porNivelAlumno($nuevo_nivel->id,$alumno->id,1);
                    if(!$examen_b1){
                        $transaction->rollback();
                        return $this->redirect(['students/index']);
                    }
                    $alumnoExamen->fecha_realizacion = time();
                    $alumnoExamen->fecha_realizacion;
                    if(!$alumnoExamen->save()){
                        $transaction->rollback();
                        return false;
                    }
                    $alumno->nivel_alumno_id = $nuevo_nivel->id;
                    if (!$alumno->save()) {
                        $transaction->rollback();
                        return false;
                    }
                    $writing = $this->asignWriting($examen_b1, $examen_b1->examen->writing_duration);
                    if(!$writing){
                        $transaction->rollback();
                        return $this->redirect(['students/index']);
                    }
                    $transaction->commit();
                    $session->set('past_exam', $examenObj->id);
                    return $this->render('writing', $writing);
                }else if($promedio >= 50 && $promedio <= 59){
                    $writing = $this->asignWriting($alumnoExamen, $examenObj->writing_duration);
                    if(!$writing){
                        $transaction->rollback();
                        return $this->redirect(['students/index']);
                    }
                    $transaction->commit();
                    $session->set('past_exam', $examenObj->id);
                    return $this->render('writing', $writing);
                }else{
                    $nuevo_nivel = NivelAlumno::find()->where('nombre="C1"')->one();
                    $nuevo_examen = $this->asignExam($alumnoExamen, $alumno, $nuevo_nivel);
                    if(!$nuevo_examen){
                        $transaction->rollback();
                        return $this->redirect(['students/index']);
                    }
                    $transaction->commit();
                    $session->set('past_exam', $examenObj->id);
                    return $this->redirect($nuevo_examen);
                }
            }else if ($nivel->nombre == 'C1'){
                if($promedio < 50){
                    $nuevo_nivel = NivelAlumno::find()->where('nombre="B2"')->one();
                    $examen_b2 = $alumnoExamen->porNivelAlumno($nuevo_nivel->id,$alumno->id,1);
                    if(!$examen_b2){
                        $transaction->rollback();
                        return $this->redirect(['students/index']);
                    }
                    $alumnoExamen->fecha_realizacion = time();
                    $alumnoExamen->fecha_realizacion;
                    if(!$alumnoExamen->save()){
                        $transaction->rollback();
                        return false;
                    }
                    $alumno->nivel_alumno_id = $nuevo_nivel->id;
                    if (!$alumno->save()) {
                        $transaction->rollback();
                        return false;
                    }
                    $writing = $this->asignWriting($examen_b2, $examen_b2->examen->writing_duration);
                    if(!$writing){
                        $transaction->rollback();
                        return $this->redirect(['students/index']);
                    }
                    $transaction->commit();
                    $session->set('past_exam', $examenObj->id);
                    return $this->render('writing', $writing);
                }else if($promedio >= 50 && $promedio < 60){
                    $writing = $this->asignWriting($alumnoExamen, $examenObj->writing_duration);
                    if(!$writing){
                        $transaction->rollback();
                        return $this->redirect(['students/index']);
                    }
                    $transaction->commit();
                    $session->set('past_exam', $examenObj->id);
                    return $this->render('writing', $writing);
                }else{
                    $nuevo_nivel = NivelAlumno::find()->where('nombre="C2"')->one();
                    $nuevo_examen = $this->asignExam($alumnoExamen, $alumno, $nuevo_nivel);
                    if(!$nuevo_examen){
                        $transaction->rollback();
                        return $this->redirect(['students/index']);
                    }
                    $transaction->commit();
                    $session->set('past_exam', $examenObj->id);
                    return $this->redirect($nuevo_examen);
                }
            }else if ($nivel->nombre == 'C2'){
                if($promedio < 70){
                    $nuevo_nivel = NivelAlumno::find()->where('nombre="C1"')->one();
                    $examen_c1 = $alumnoExamen->porNivelAlumno($nuevo_nivel->id,$alumno->id,1);
                    if(!$examen_c1){
                        $transaction->rollback();
                        return $this->redirect(['students/index']);
                    }
                    $alumnoExamen->fecha_realizacion = time();
                    $alumnoExamen->fecha_realizacion;
                    if(!$alumnoExamen->save()){
                        $transaction->rollback();
                        return false;
                    }
                    $alumno->nivel_alumno_id = $nuevo_nivel->id;
                    if (!$alumno->save()) {
                        $transaction->rollback();
                        return false;
                    }
                    $writing = $this->asignWriting($examen_c1, $examen_c1->examen->writing_duration);
                    if(!$writing){
                        $transaction->rollback();
                        return $this->redirect(['students/index']);
                    }
                    $transaction->commit();
                    $session->set('past_exam', $examenObj->id);
                    return $this->render('writing', $writing);
                }else{
                    $writing = $this->asignWriting($alumnoExamen, $examenObj->writing_duration);
                    if(!$writing){
                        $transaction->rollback();
                        return $this->redirect(['students/index']);
                    }
                    $transaction->commit();
                    $session->set('past_exam', $examenObj->id);
                    return $this->render('writing', $writing);
                }
            }
        }else{
            $promedio = ($calificacionUse + $calificacionRea + $calificacionLis) / 3;
            $calificaciones->promedio = round($promedio, 0, PHP_ROUND_HALF_DOWN);
            $calificaciones->promedio_use = round($calificacionUse, 0, PHP_ROUND_HALF_DOWN);
            $calificaciones->promedio_listening = round($calificacionLis, 0, PHP_ROUND_HALF_DOWN);
            $calificaciones->promedio_reading = round($calificacionRea, 0, PHP_ROUND_HALF_DOWN);
            $calificaciones->update();
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
                $nuevo_nivel = false;
            }

            if($nivel->nombre == 'A1' || $nivel->nombre == 'N/A')
            {
                $session->set('past_exam', $examenObj->id);
            }

            $promedio = ($calificacionUse + $calificacionRea + $calificacionLis) / 3;
            if($nivel->nombre == 'C2'){
                $promedio = 0;
            }

            $calificaciones->promedio = round($promedio, 0, PHP_ROUND_HALF_DOWN);
            $calificaciones->promedio_use = round($calificacionUse, 0, PHP_ROUND_HALF_DOWN);
            $calificaciones->promedio_listening = round($calificacionLis, 0, PHP_ROUND_HALF_DOWN);
            $calificaciones->promedio_reading = round($calificacionRea, 0, PHP_ROUND_HALF_DOWN);
            $calificaciones->update();

            if($promedio < $examenObj->porcentaje || !$nuevo_nivel){
                if($promedio < $examenObj->porcentaje){
                    $alumnoExamen->fecha_realizacion = time();
                    $alumnoExamen->update();
                }

                $pastExam = $session->get('past_exam');
                if(!$pastExam){
                    $examen_pasado = AlumnoExamen::find()->where('alumno_id='.$alumno->id.' AND id != '.$alumnoExamen->id)->orderBy(['fecha_realizacion' => SORT_DESC])->limit(1)->one();
                    $pastExam = $examen_pasado->examen_id;
                }
                if($pastExam){
                    $pastAlumnoExamObj = AlumnoExamen::find()->where(['examen_id'=>$pastExam, 'alumno_id'=>$alumno->id])->one();
                    if($pastAlumnoExamObj){
                        $pastAlumnoExamObj->fecha_realizacion = null;
                        $pastAlumnoExamObj->update();
                    }
                }
                $writingForm = new WritingResueltoForm();
                $seccionTipo = TipoSeccion::find()->where(['clave'=>'WRI'])->one();
                // if(!$nuevo_nivel){
                //     if($nivel->nombre == 'C2'){
                //         $nuevo_nivel = NivelAlumno::find()->where('nombre="C1"')->one();
                //     }
                //     $alumno->nivel_alumno_id = $nuevo_nivel->id;
                //     if(!$alumno->update()){
                //         $transaction->rollback();
                //         return $this->redirect(['students/index']);
                //     }
                // }
                if(!$nuevo_nivel || $nuevo_nivel->clave == "A2"){
                    if ($promedio < $examenObj->porcentaje && $nivel->nombre == 'C2') {
                        $nuevo_nivel = NivelAlumno::find()->where('nombre="C1"')->one();
                        $alumno->nivel_alumno_id = $nuevo_nivel->id;
                        if (!$alumno->update()) {
                            $transaction->rollback();
                            return $this->redirect(['students/index']);
                        }
                        $pastExamObj = Examen::findOne($pastExam);
                        $writing = Seccion::find()->where(['examen_id' => $pastExamObj->id, 'tipo_seccion_id' => $seccionTipo->id])->one();
                        if (!$writing) {
                            $transaction->commit();
                            return $this->redirect(['students/index']);
                        }

                        $respondidoWriting = false;
                        foreach($alumno->alumnoExamens as $examen_anterior){
                            if(!$examen_anterior->fecha_realizacion && $examen_anterior->id != $alumnoExamen->id){
                                $examen_anterior->fecha_realizacion = time();
                                $examen_anterior->update();
                            }
                            foreach($examen_anterior->aluexaReactivos as $reactivo){
                                if($reactivo->respuestaWriting && $examen_anterior->fecha_realizacion){
                                    $respondidoWriting = true;
                                }
                            }
                        }
                        if($respondidoWriting){
                            $alumnoExamen->fecha_realizacion = time();
                            $alumnoExamen->update();
                            $transaction->commit();
                            return $this->redirect(['students/index']);
                        }
                        $transaction->commit();
                        $alumnoExamen = AlumnoExamen::find()->where('alumno_id = '.$alumno->id.' AND examen_id = '.$pastExam)->one();

                        $writingSaved = AluexaReactivos::find()
                            ->where(['alumno_examen_id' => $alumnoExamen->id])
                            ->andWhere(['is not', 'respuestaWriting', null])
                            ->one();
                        if($writingSaved){
                            $writingForm->texto = $writingSaved->respuestaWriting;
                        }
                        return $this->render('writing', [
                            'alumnoExamen' => $alumnoExamen,
                            'writing' => $writing,
                            'writingForm' => $writingForm,
                            'tiempo' => $examenObj->writing_duration
                        ]);
                    } else {
                        $writing = Seccion::find()->where(['examen_id' => $examenObj->id, 'tipo_seccion_id' => $seccionTipo->id])->one();
                        if (!$writing) {
                            $transaction->commit();
                            return $this->redirect(['students/index']);
                        }

                        $respondidoWriting = false;
                        foreach($alumno->alumnoExamens as $examen_anterior){
                            foreach($examen_anterior->aluexaReactivos as $reactivo){
                                if($reactivo->respuestaWriting && $examen_anterior->fecha_realizacion){
                                    $respondidoWriting = true;
                                }
                            }
                        }
                        if($respondidoWriting){
                            $alumnoExamen->fecha_realizacion = time();
                            $alumnoExamen->update();
                            $transaction->commit();
                            return $this->redirect(['students/index']);
                        }
                        $transaction->commit();

                        $writingSaved = AluexaReactivos::find()
                            ->where(['alumno_examen_id' => $alumnoExamen->id])
                            ->andWhere(['is not', 'respuestaWriting', null])
                            ->one();
                        if($writingSaved){
                            $writingForm->texto = $writingSaved->respuestaWriting;
                        }
                        return $this->render('writing', [
                            'alumnoExamen' => $alumnoExamen,
                            'writing' => $writing,
                            'writingForm' => $writingForm,
                            'tiempo' => $examenObj->writing_duration
                        ]);
                    }
                }
                if(!$pastExam){
                    $examen_pasado = AlumnoExamen::find()->where('alumno_id='.$alumno->id.' AND id != '.$alumnoExamen->id)->orderBy(['fecha_realizacion' => SORT_DESC])->limit(1)->one();
                    if($examen_pasado){
                        $pastExam = $examen_pasado->examen_id;
                    }else{
                        $pastExam = $alumnoExamen->examen_id;
                    }
                }
                if($pastExam){
                    $pastExamObj = Examen::findOne($pastExam);
                    if($nivel->nombre != 'A1' || $nivel->nombre != 'N/A'){
                        if($nivel->nombre == 'A2'){
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
                        $alumno->nivel_alumno_id = $nuevo_nivel->id;
                        if(!$alumno->update()){
                            $transaction->rollback();
                            return $this->redirect(['students/index']);
                        }
                    }
                    $writing = Seccion::find()->where(['examen_id' => $pastExamObj->id, 'tipo_seccion_id' => $seccionTipo->id])->one();
                    if (!$writing) {
                        $transaction->commit();
                        return $this->redirect(['students/index']);
                    }

                    $alumnoExamen = AlumnoExamen::find()->where('alumno_id = '.$alumno->id.' AND examen_id = '.$pastExam)->one();
                    $respondidoWriting = false;
                    foreach ($alumno->alumnoExamens as $examen_anterior) {
                        foreach ($examen_anterior->aluexaReactivos as $reactivo) {
                            if($reactivo->respuestaWriting && $examen_anterior->fecha_realizacion){
                                $respondidoWriting = true;
                            }
                        }
                    }

                    if ($respondidoWriting) {
                        $alumnoExamen->fecha_realizacion = time();
                        $alumnoExamen->update();
                        $transaction->commit();
                        return $this->redirect(['students/index']);
                    }
                    $transaction->commit();

                    $writingSaved = AluexaReactivos::find()
                        ->where(['alumno_examen_id' => $alumnoExamen->id])
                        ->andWhere(['is not', 'respuestaWriting', null])
                        ->one();
                    if($writingSaved){
                        $writingForm->texto = $writingSaved->respuestaWriting;
                    }
                    return $this->render('writing', [
                        'alumnoExamen' => $alumnoExamen,
                        'writing' => $writing,
                        'writingForm' => $writingForm,
                        'tiempo' => $pastExamObj->writing_duration
                    ]);
                }
            }else{
                $seccionTipo = TipoSeccion::find()->where(['clave'=>'WRI'])->one();
                $examenes_hechos = count(AlumnoExamen::find()->where('alumno_id='.$alumno->id)->all());
                $examen_realizado = AlumnoExamen::find()->joinWith('examen')->where('alumno_id='.$alumno->id.' AND nivel_alumno_id = '.$nuevo_nivel->id)->one();
                if($nivel->nombre=='A1' && $examenes_hechos > 1){
                    $examen_realizado = AlumnoExamen::find()->joinWith('examen')->where('alumno_id='.$alumno->id.' AND nivel_alumno_id = '.$nivel->id)->one();
                }
                if($examen_realizado){
                    $writing = Seccion::find()->where(['examen_id' => $examenObj->id, 'tipo_seccion_id' => $seccionTipo->id])->one();
                    $writingForm = new WritingResueltoForm();

                    $respondidoWriting = false;
                    foreach ($alumno->alumnoExamens as $examen_anterior) {
                        foreach ($examen_anterior->aluexaReactivos as $reactivo) {
                            if($reactivo->respuestaWriting && $examen_anterior->fecha_realizacion){
                                $respondidoWriting = true;
                            }
                        }
                    }
                    if ($respondidoWriting) {
                        $alumnoExamen->fecha_realizacion = time();
                        $alumnoExamen->update();
                        $transaction->commit();
                        return $this->redirect(['students/index']);
                    }
                    $transaction->commit();

                    $writingSaved = AluexaReactivos::find()
                    ->where(['alumno_examen_id' => $alumnoExamen->id])
                    ->andWhere(['is not', 'respuestaWriting', null])
                    ->one();
                    if($writingSaved){
                        $writingForm->texto = $writingSaved->respuestaWriting;
                    }
                    return $this->render('writing', [
                        'alumnoExamen' => $alumnoExamen,
                        'writing' => $writing,
                        'writingForm' => $writingForm,
                        'tiempo' => $examenObj->writing_duration
                    ]);
                }
                $alumnoExamen->fecha_realizacion = time();
                $alumnoExamen->update();

                $session->set('past_exam', $examenObj->id);
                $alumno->nivel_alumno_id = $nuevo_nivel->id;
                if(!$alumno->update()){
                    $transaction->rollback();
                    return $this->redirect(['students/index']);
                }
                $nuevo_examen = new AlumnoExamen();
                $nuevo_examen->alumno_id = $alumno->id;
                $nuevo_examen->tipo_examen_id = $alumnoExamen->tipo_examen_id;
                $nuevo_examen->status = 1;
                if(!$nuevo_examen->save()){
                    $transaction->rollback();
                    return $this->redirect(['students/index']);
                }
                $transaction->commit();
                return $this->redirect(['students/solve-exam', 'id' => $nuevo_examen->id]);
            }
        }
    }

    public function actionGetCalificaciones(){
        $id = 0;
        if (Yii::$app->request->isAjax) {
            $id = Yii::$app->request->post('id');
        }
        else{
            return false;
        }

        $alumnoExamen = AlumnoExamen::findOne($id);
        $secciones = $alumnoExamen->examen->seccions;
        $calificaciones = Calificaciones::findOne($alumnoExamen->id);

        $calificacionUse = 0;
        $calificacionRea = 0;
        $calificacionLis = 0;
        $calificacionWri = $alumnoExamen->calificaciones->calificacionWriting;
        if(!$calificacionWri){
            $calificacionWri = 0;
        }
        $promedio = 0;

        foreach($secciones as $seccion){
            $tipo = $seccion->tipoSeccion->clave;
            switch($tipo){
                case "USE":
                $calificacionUse = ($alumnoExamen->calificaciones->calificacionUse * 100)/ $seccion->puntos_seccion;
                break;
                case 'REA':
                $calificacionRea = ($alumnoExamen->calificaciones->calificacionReading * 100)/ $seccion->puntos_seccion;
                break;
                case 'LIS':
                $calificacionLis = ($alumnoExamen->calificaciones->calificacionListening * 100) / $seccion->puntos_seccion;
                break;
                case 'WRI':
                $calificacionWri = ($alumnoExamen->calificaciones->calificacionWriting * 100) / $seccion->puntos_seccion;
                break;
            }
        }

        $promedio = ($calificacionUse + $calificacionRea  + $calificacionLis + $calificacionWri) / 4;

        return json_encode([
            'USE' => $calificacionUse,
            'REA' => $calificacionRea,
            'WRI' => $calificacionWri,
            'LIS' => $calificacionLis,
            'PRO' => $promedio,
         ]);
    }

    public function actionSaveWriting(){
        $datos = Yii::$app->request->post('WritingResueltoForm');
        if(count($datos)){
            $alumnoExamen = AlumnoExamen::findOne($datos['id']);
            $alumno = $alumnoExamen->alumno;
        }
        if(Yii::$app->request->isAjax){
            $alumnoExamen = AlumnoExamen::findOne(Yii::$app->request->post('alumno_examen'));
            $alumno = $alumnoExamen->alumno;
            $examen = $alumnoExamen->examen;
            if($examen->certificate_v2 == 1){
                $writing_data = WritingData::find()->where([
                    'reactivo_id' => Yii::$app->request->post('reactivo'),
                    'alumno_examen_id' => $alumnoExamen->id
                ])->one();
                $writing_data->completed = 1;
                $writing_data->save();
                $writings_completed = count(WritingData::find()->where([
                    'alumno_examen_id' => $alumnoExamen->id,
                    'completed' => 1
                ])->all());

                $reactivo = AluexaReactivos::find()
                            ->where(['alumno_examen_id' => $alumnoExamen->id])
                            ->andWhere(['reactivo_id' => Yii::$app->request->post('reactivo')])
                            ->one();
                if(!$reactivo){
                    $reactivo = new AluexaReactivos();
                    $reactivo->alumno_examen_id = $alumnoExamen->id;
                    $reactivo->reactivo_id = Yii::$app->request->post('reactivo');
                    $reactivo->respuestaWriting = '';
                    $reactivo->save();
                }
                $reactivo->respuestaWriting = Yii::$app->request->post('texto');
                if(!$reactivo->save()){
                    $this->transaction->rollback();
                    return false;
                }

                if($writings_completed == 2){
                    $alumnoExamen->fecha_realizacion = time();
                    if(!$alumnoExamen->writing_used_time){
                        $alumnoExamen->writing_used_time = 0;
                    }
                    $alumnoExamen->save();

                    $alumno = $alumnoExamen->alumno;
                    $statusExamen = StatusExamen::find()->where(['codigo' => 'AWA'])->one();
                    $alumno->status_examen_id = $statusExamen->id;
                    $alumno->save();
                }
            }else{
                $statusExamen = StatusExamen::find()->where(['codigo' => 'AWA'])->one();
                $alumno->status_examen_id = $statusExamen->id;
                $alumno->save();

                if($alumnoExamen->writing_used_time == null){
                    $alumnoExamen->writing_used_time = 0;
                }
                if(($alumnoExamen->writing_used_time/60) >= $examen->writing_duration){
                    $alumnoExamen->timedout = 1;
                }
                $alumnoExamen->fecha_realizacion = time();
                if(!$alumnoExamen->writing_used_time){
                    $alumnoExamen->writing_used_time = 0;
                }
                $alumnoExamen->update();

                $alumno = $alumnoExamen->alumno;
                $statusExamen = StatusExamen::find()->where(['codigo' => 'AWA'])->one();
                $alumno->status_examen_id = $statusExamen->id;
                $alumno->save();

                $reactivo = AluexaReactivos::find()
                        ->where(['alumno_examen_id' => $alumnoExamen->id])
                        ->andWhere(['is not', 'respuestaWriting', null])
                        ->one();
                $reactivo->respuestaWriting = Yii::$app->request->post('texto');
                if(!$reactivo->save()){
                    Yii::$app->session->setFlash('error', "Error at saving writing Section.");
                }
            }
        }else{
            $writingForm = new WritingResueltoForm();
            if($alumnoExamen->examen->tipoExamen->clave == 'CER' && $alumnoExamen->examen->certificate_v2 == 1){
                if ($writingForm->load(Yii::$app->request->post())) {
                    if (!$writingForm->guardarV2()) {
                        Yii::$app->session->setFlash('error', "Error at saving writing Section.");
                    }
                    if(!$writingForm->done){
                        $writing_data = WritingData::find()->select('reactivo_id')->where(['alumno_examen_id' => $alumnoExamen->id,'completed'=>1])->all();
                        $writing = Reactivo::find()
                            ->join('INNER JOIN', 'seccion', 'reactivo.seccion_id = seccion.id')
                            ->join('INNER JOIN', 'tipo_seccion', 'tipo_seccion.id = seccion.tipo_seccion_id')
                            ->where(
                                'reactivo.id != '.$writing_data[0]->reactivo_id.' AND seccion.examen_id = '.$alumnoExamen->examen->id.' AND tipo_seccion.clave = "WRI" AND reactivo.status = 1'
                            )->one();
                        $writing_data = WritingData::find()->where('alumno_examen_id = '.$alumnoExamen->id.' AND reactivo_id = '.$writing->id)->one();
                        if(!$writing_data){
                            $writing_data = new WritingData();
                            $writing_data->alumno_examen_id = $alumnoExamen->id;
                            $writing_data->reactivo_id = $writing->id;
                            $writing_data->save();
                        }
                        $examenObj = $alumnoExamen->examen;
                        $writingForm = new WritingResueltoForm();
                        return $this->render('writing_v2', [
                            'alumnoExamen' => $alumnoExamen,
                            'writing' => $writing,
                            'writingForm' => $writingForm,
                            'tiempo' => $examenObj->writing_duration,
                            'imagenes' => $writing->imagenes(),
                            'tiempo_usado' => $writing_data->time
                        ]);
                    }else{
                        $statusExamen = StatusExamen::find()->where(['codigo' => 'AWA'])->one();
                        $alumno->status_examen_id = $statusExamen->id;
                        $alumno->save();
                        return $this->redirect(['students/index']);
                    }
                }
            }else{
                if ($writingForm->load(Yii::$app->request->post())) {
                    if (!$writingForm->guardar()) {
                        Yii::$app->session->setFlash('error', "Error at saving writing Section.");
                    }
                    return $this->redirect(['students/index']);
                }
            }
        }

        $awaitingStatus = StatusExamen::find()->where(['codigo' => 'AWA'])->one();
        if (isset($alumno) && isset($alumnoExamen) && $alumno->status_examen_id != $awaitingStatus->id){
            $alumno->status_examen_id = $awaitingStatus->id;
            $alumno->update();
            $writing = isset($writingForm->texto) ? $writingForm->texto : $reactivo->respuestaWriting;
            Yii::$app->mailer->compose()
                ->setFrom('no_reply@oxfordtcc.co.uk')
                ->setTo(Yii::$app->params['BlackRobotSupport'])
                ->setSubject('Alumno con status equivocado al terminar examen')
                ->setTextBody("
                    id: {$alumnoExamen->id}
                    alumno_id: {$alumnoExamen->alumno_id}
                    examen_id: {$alumnoExamen->examen_id}
                    tipo_examen_id: {$alumnoExamen->tipo_examen_id}
                    calificaciones_id: {$alumnoExamen->calificaciones_id}
                    status: {$alumnoExamen->status}
                    fecha_realizacion: {$alumnoExamen->fecha_realizacion}
                    writing_used_time: {$alumnoExamen->writing_used_time}
                    reading_used_time: {$alumnoExamen->reading_used_time}
                    listening_used_time: {$alumnoExamen->listening_used_time}
                    use_used_time: {$alumnoExamen->use_used_time}
                    use_used_time: {$alumnoExamen->use_used_time}
                    inactivity: {$alumnoExamen->inactivity}
                    respuesta alumno: {$writing}
                ")
                ->send();
        }
        return $this->redirect(['students/index']);
    }

    public function actionSaveExamRandom(){
         if(Yii::$app->request->isAjax){
            $alumnoExamen = AlumnoExamen::findOne(Yii::$app->request->post('alumno_examen'));
            $examen = Yii::$app->request->post('examen');
            if(isset($examen)){
                $alumnoExamen->examen_id = $examen;
            }
            $alumnoExamen->update();

            return true;
        }
    }

    public function actionSaveWritingPartial(){
        if(Yii::$app->request->isAjax){
            $alumnoExamen = AlumnoExamen::findOne(Yii::$app->request->post('alumnoExamen'));
            $writing_data = WritingData::find()->where('alumno_examen_id='.$alumnoExamen->id.' AND reactivo_id='.Yii::$app->request->post('reactivo'))->one();
            if($alumnoExamen->writing_used_time != null){
                if($alumnoExamen->examen->certificate_v2 == 1){
                    $writing_data = WritingData::find()->where('alumno_examen_id='.$alumnoExamen->id.' AND reactivo_id='.Yii::$app->request->post('reactivo'))->one();
                    $writing_data->time = $writing_data->time + 10;
                    $writing_data->save();
                }else{
                    $alumnoExamen->writing_used_time = $alumnoExamen->writing_used_time + 10;
                    $alumnoExamen->update();
                }

                if(Yii::$app->request->post('textoWriting')){
                    $reactivo = AluexaReactivos::find()
                    ->where(['alumno_examen_id' => $alumnoExamen->id])
                    ->andWhere(['is not', 'respuestaWriting', null])
                    ->one();
                    $reactivo->respuestaWriting = Yii::$app->request->post('textoWriting');
                    $reactivo->update();
                }
                return true;
            }else{
                if($alumnoExamen->examen->certificate_v2 == 1){
                    $writing_data = WritingData::find()->where('alumno_examen_id='.$alumnoExamen->id.' AND reactivo_id='.Yii::$app->request->post('reactivo'))->one();
                    if(!$writing_data){
                        $writing_data = new WritingData();
                        $writing_data->alumno_examen_id = $alumnoExamen->id;
                        $writing_data->reactivo_id = Yii::$app->request->post('reactivo');
                    }
                    $writing_data->time = 10;
                    $writing_data->save();
                }else{
                    $alumnoExamen->writing_used_time = 10;
                    $alumnoExamen->update();
                }

                $reactivo = AluexaReactivos::find()
                ->where(['alumno_examen_id' => $alumnoExamen->id])
                ->andWhere(['reactivo_id' => Yii::$app->request->post('reactivo')])
                ->one();
                if(!$reactivo){
                    $reactivo = new AluexaReactivos();
                    $reactivo->alumno_examen_id = $alumnoExamen->id;
                    $reactivo->reactivo_id = Yii::$app->request->post('reactivo');
                }
                $reactivo->respuestaWriting = Yii::$app->request->post('textoWriting');
                if (!$reactivo->save()) {
                    $this->transaction->rollback();
                    return false;
                }

                return true;
            }
        } else {
            return false;
        }
    }

    public function actionSaveUsedTime(){
        $alumnoExamenID = Yii::$app->request->post('alumnoExamen');
        $seccionVisible = Yii::$app->request->post('seccion');

        $alumnoExamen = AlumnoExamen::findOne($alumnoExamenID);
        switch($seccionVisible){
            case 'LIS':
                if($alumnoExamen->listening_used_time){
                    $alumnoExamen->listening_used_time = $alumnoExamen->listening_used_time + 10;
                } else {
                    $alumnoExamen->listening_used_time = 10;
                }
                break;
            case 'USE':
                if($alumnoExamen->use_used_time){
                    $alumnoExamen->use_used_time = $alumnoExamen->use_used_time + 10;
                } else {
                    $alumnoExamen->use_used_time = 10;
                }
                break;
            case 'REA':
                if ($alumnoExamen->reading_used_time) {
                    $alumnoExamen->reading_used_time = $alumnoExamen->reading_used_time + 10;
                } else {
                    $alumnoExamen->reading_used_time = 10;
                }
                break;
        }
        $alumnoExamen->ultima_actualizacion = time();
        $alumnoExamen->update();
        return true;
    }

    private function exportMock($instituto){
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()->setCreator('Oxford TCC')
            ->setLastModifiedBy('Black Robot')
            ->setTitle('Office 2007 XLSX Test Document')
            ->setSubject('Office 2007 XLSX Test Document')
            ->setDescription('Mock results')
            ->setKeywords('office 2007 openxml php')
            ->setCategory('Mock results file');
        $spreadsheet->getActiveSheet()->setTitle('Mock');

        $drawing = new Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('Logo');
        $drawing->setPath(realpath('./images/logoColor.png'));
        $drawing->setHeight(100);
        $drawing->setWorksheet($spreadsheet->getActiveSheet());
        $title = array();
        $mockType = TipoExamen::find()->where(['clave'=>'MOC'])->one();

        $texto_titulo = ['MOCK RESULTS'];

        $nombre_instituto = [$instituto->nombre . ', ' . strtoupper(date('M Y'))];
        array_push($title, $texto_titulo);
        array_push($title, $nombre_instituto);
        $spreadsheet->getActiveSheet()
            ->fromArray(
                $title,
                null,
                'E4'
            );

        $data = array();
        $encabezado = ['NAME', 'GRADE', 'CODE', 'PASSWORD', 'DATE', 'TEST', 'EXAM LEVEL', 'LISTENING', 'READING', 'USE OF ENGLISH', 'PERCENTAGE', 'SUGGESTED LEVEL'];
        array_push($data, $encabezado);
        foreach($instituto->gruposActivos as $grupo){
            foreach($grupo->alumnosActivos as $alumno){
                $user = $alumno->users[0];
                if ($user->acceso) {
                    $datos = [
                        $alumno->fullName,
                        $alumno->grupo->grupo,
                        $user->codigo,
                        $user->accesoDec . ' ',
                    ];
                } else {
                    $datos = [
                        $alumno->fullName,
                        $alumno->grupo->grupo,
                        $user->codigo,
                        'Not exist'
                    ];
                }

                $examenMock = AlumnoExamen::find()
                    ->joinWith('examen')
                    ->where([
                        'alumno_id' => $alumno->id,
                        'examen.tipo_examen_id' => $mockType->id,
                        'examen.nivel_alumno_id' => $alumno->nivelMock->id
                    ])
                    ->one();
                if (!$examenMock) {
                    $nivelHelper = '';
                    if ($alumno->nivelMock->nombre == 'A1') {
                        $nivelHelper = NivelAlumno::find()->where('nombre="A2"')->one();
                    } else if ($alumno->nivelMock->nombre == 'A2') {
                        $nivelHelper = NivelAlumno::find()->where('nombre="B1"')->one();
                    } else if ($alumno->nivelMock->nombre == 'B1') {
                        $nivelHelper = NivelAlumno::find()->where('nombre="B2"')->one();
                    } else if ($alumno->nivelMock->nombre == 'B2') {
                        $nivelHelper = NivelAlumno::find()->where('nombre="C1"')->one();
                    } else if ($alumno->nivelMock->nombre == 'C1') {
                        $nivelHelper = NivelAlumno::find()->where('nombre="C2"')->one();
                    } else if ($alumno->nivelMock->nombre == 'C2') {
                        $nivelHelper = NivelAlumno::find()->where('nombre="C2"')->one();
                    }

                    $examenMock = AlumnoExamen::find()
                        ->joinWith('examen')
                        ->where([
                            'alumno_id' => $alumno->id,
                            'examen.tipo_examen_id' => $mockType->id,
                            'examen.nivel_alumno_id' => $nivelHelper->id
                        ])
                        ->one();
                }
                if ($examenMock && $examenMock->calificaciones) {
                    if ($examenMock->examen->nivelAlumno->clave == 'A1' || $examenMock->examen->nivelAlumno->clave == 'A2') {
                        $calificacionUse = ($examenMock->calificaciones->calificacionUse * 100) / 12;
                        $calificacionRea = ($examenMock->calificaciones->calificacionReading * 100) / 24;
                        $calificacionLis = ($examenMock->calificaciones->calificacionListening * 100) / 24;
                    } else {
                        $calificacionUse = ($examenMock->calificaciones->calificacionUse * 100) / 15;
                        $calificacionRea = ($examenMock->calificaciones->calificacionReading * 100) / 32;
                        $calificacionLis = ($examenMock->calificaciones->calificacionListening * 100) / 32;
                    }
                    $promedio = ($calificacionLis * .35) + ($calificacionRea * .35) + ($calificacionUse * .30);
                    $nivel_inicial =  NivelAlumno::findOne($alumno->nivel_inicio_mock_id);
                    $nivel_inicial = $nivel_inicial ? $nivel_inicial->nombre : 'N/A';
                    array_push(
                        $datos,
                        date('d-m-Y', $examenMock->fecha_realizacion),
                        $examenMock->examen->tipoExamen->nombre,
                        $nivel_inicial,
                        (int)$calificacionLis . '%',
                        (int)$calificacionRea . '%',
                        (int)$calificacionUse . '%',
                        (int)$promedio . '%',
                        $alumno->nivelMock->nombre ? $alumno->nivelMock->nombre : 'N/A'
                    );
                } else {
                    $nivel_inicial =  NivelAlumno::findOne($alumno->nivel_inicio_mock_id);
                    $nivel_inicial = $nivel_inicial ? $nivel_inicial->nombre : 'N/A';
                    array_push(
                        $datos,
                        'N/A',
                        'N/A',
                        $nivel_inicial,
                        'N/A',
                        'N/A',
                        'N/A',
                        'N/A',
                        $alumno->nivelMock->nombre ? $alumno->nivelMock->nombre : 'N/A'
                    );
                }
                array_push($data, $datos);
            }
        }

        $spreadsheet->getActiveSheet()
            ->fromArray(
                $data,
                null,
                'A8'
            );

        $renglones = count($data) - 1;

        //creando estilos
        $spreadsheet->getActiveSheet()->setShowGridlines(false);
        $spreadsheet->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(15);
        $spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(15);
        $spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(15);
        $spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(15);
        $spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(15);
        $spreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(15);
        $spreadsheet->getActiveSheet()->getColumnDimension('I')->setWidth(20);
        $spreadsheet->getActiveSheet()->getColumnDimension('J')->setWidth(15);
        $spreadsheet->getActiveSheet()->getColumnDimension('K')->setWidth(15);
        $spreadsheet->getActiveSheet()->getColumnDimension('L')->setWidth(15);
        $spreadsheet->getActiveSheet()->getColumnDimension('M')->setWidth(15);

        $styleArrayHeader = [
            'font' => [
                'bold' => true,
                'color'=>[
                    'argb' => 'FFFFFFFF'
                ]
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
            'borders' => [
                'top' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'FF0F4F2C',
                ],
            ],
        ];
        $styleArrayData = [
           'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
        ];
        $spreadsheet->getActiveSheet()->getStyle('A8:L8')->applyFromArray($styleArrayHeader);
        $spreadsheet->getActiveSheet()->getStyle('A9:L' . strval($renglones + 8))->applyFromArray($styleArrayData);

        $spreadsheet->setActiveSheetIndex(0);
        $nombre_archivo = $this->sanitize($instituto->nombre).'.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save($nombre_archivo);
        $mail = Yii::$app->mailer->compose()
            ->setTo(Yii::$app->params['email-notification'])
            ->setFrom(["equipo@blackrobot.mx" => "Oxford TCC"])
            ->setSubject($instituto->nombre." Mock completed")
            ->setHtmlBody($this->renderPartial('_mock_done', [
                'instituto' => $instituto,
            ]))
            ->attach($nombre_archivo);
        if (isset(Yii::$app->params['email-cc'])) {
            $cc = Yii::$app->params['email-cc'];
            $cc = explode(',',$cc);
        }
        array_push($cc, $instituto->email);
        $mail->setCc($cc)
            ->send();
        unlink($nombre_archivo);
        return true;
    }

    private function sanitize($string, $force_lowercase = true, $anal = false) {
        $strip = array("~", "`", "!", "@", "#", "$", "%", "^", "&", "*", "(", ")", "_", "=", "+", "[", "{", "]",
                       "}", "\\", "|", ";", ":", "\"", "'", "&#8216;", "&#8217;", "&#8220;", "&#8221;", "&#8211;", "&#8212;",
                       "â€”", "â€“", ",", "<", ".", ">", "/", "?");
        $clean = trim(str_replace($strip, "", strip_tags($string)));
        $clean = preg_replace('/\s+/', "-", $clean);
        $clean = ($anal) ? preg_replace("/[^a-zA-Z0-9]/", "", $clean) : $clean ;
        return ($force_lowercase) ?
            (function_exists('mb_strtolower')) ?
                mb_strtolower($clean, 'UTF-8') :
                strtolower($clean) :
            $clean;
    }

    function getPastExam($alumno,$alumnoExamen){
        $session = Yii::$app->session;
        $pastExam = $session->get('past_exam');
        if(!$pastExam){
            $examen_pasado = AlumnoExamen::find()->where('alumno_id='.$alumno.' AND id != '.$alumnoExamen)->orderBy(['fecha_realizacion' => SORT_DESC])->limit(1)->one();
            $pastExam = $examen_pasado->examen_id;
        }
        return $pastExam;
    }

    function asignWriting($alumnoExamen, $writing_duration){
        $writingForm = new WritingResueltoForm();
        $seccionTipo = TipoSeccion::find()->where(['clave'=>'WRI'])->one();

        $writing = Seccion::find()->where(['examen_id' => $alumnoExamen->examen_id, 'tipo_seccion_id' => $seccionTipo->id])->one();

        if (!$writing) {
            return false;
        }

        $writingSaved = AluexaReactivos::find()
            ->where(['alumno_examen_id' => $alumnoExamen->id])
            ->andWhere(['is not', 'respuestaWriting', null])
            ->one();
        if($writingSaved){
            $writingForm->texto = $writingSaved->respuestaWriting;
        }
        return [
            'alumnoExamen' => $alumnoExamen,
            'writing' => $writing,
            'writingForm' => $writingForm,
            'tiempo' => $writing_duration
        ];
    }

    function asignExam($alumnoExamen,$alumno,$nuevo_nivel){
        $alumnoExamen->fecha_realizacion = time();
        $alumnoExamen->fecha_realizacion;
        if(!$alumnoExamen->save()){
            $transaction->rollback();
            return false;
        }
        $alumno->nivel_alumno_id = $nuevo_nivel->id;
        if (!$alumno->save()) {
            $transaction->rollback();
            return false;
        }

        $nuevo_examen = new AlumnoExamen();
        $nuevo_examen->alumno_id = $alumno->id;
        $nuevo_examen->tipo_examen_id = $alumnoExamen->tipo_examen_id;
        $nuevo_examen->status = 1;
        if(!$nuevo_examen->save()){
            return false;
        }
        return ['students/solve-exam', 'id' => $nuevo_examen->id];
    }
}
