<?php
namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use app\models\search\WritingQuestionsSearch;
use app\models\search\ScoredQuestionsSearch;
use app\models\search\ScoredSpeakingSearch;
use app\models\AluexaReactivos;
use app\models\Instituto;
use app\models\Grupo;
use app\models\AlumnoExamen;
use app\models\Alumno;
use app\models\Academico;
use app\models\Calificaciones;
use app\models\StatusExamen;
use app\models\Seccion;
use app\models\TipoExamen;
use app\models\NivelAlumno;
use app\models\WritingData;
use app\models\CicloEscolar;
use backend\models\forms\WritingScoreForm;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\IOFactory;
use app\models\search\SpeakingSearch;
use backend\models\forms\ScoreSpeakingForm;

class ScoreExamController extends Controller
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
                        'actions' => ['index','index-v2','writing','writing-v2','grade-exam','grade-exam-v2', 'next-exam', 'history', 'history-v2','review-writing','speaking','datos-speaking', 'guarda-speaking', 'speaking-history', 'new-speaking', 'set-institute-speaking', 'score-new-speaking', 'clear-institute-speaking', 'section-speaking', 'delete-section-speaking', 'get-section-speaking'],
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

        if ($rol == 'INS' || $rol == 'ADM' || $rol == 'ALU') {
            return $this->redirect(\Yii::$app->urlManager->createUrl("site/index"));
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
        $searchModel = new WritingQuestionsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams,'v1');

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider
        ]);
    }

    public function actionIndexV2()
    {
        $searchModel = new WritingQuestionsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams,'v2');

        return $this->render('index_v2', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider
        ]);
    }

    public function actionWriting($id){
        $aluexa = AluexaReactivos::findOne($id);
        if($aluexa->alumnoExamen->timedout)
            $timedOut = true;
        else
            $timedOut = false;

        return $this->render('writing',[
            'aluexa' => $aluexa,
            'timedOut' => $timedOut
        ]);
    }
    public function actionWritingV2($id){
        $aluexa = AluexaReactivos::findOne($id);
        if($aluexa->alumnoExamen->timedout)
            $timedOut = true;
        else
            $timedOut = false;

        $words = [
            'A1' => 40,
            'A2' => 60,
            'B1' => 100,
            'B2' => 150,
            'C1' => 200,
            'C2' => 250
        ];
        $writing_data = WritingData::find()->where([
            'alumno_examen_id' => $aluexa->alumno_examen_id,
            'reactivo_id' => $aluexa->reactivo_id
        ])->one();

        return $this->render('writing_v2',[
            'aluexa' => $aluexa,
            'timedOut' => $timedOut,
            'words' => $words,
            'writing_data' => $writing_data,
            'imagenes' => $aluexa->reactivo->imagenes()
        ]);
    }

    public function actionGradeExamV2(){
        if (Yii::$app->request->isAjax) {
            $connection = \Yii::$app->db;
            $transaction = $connection->beginTransaction();
            $total = Yii::$app->request->post('total');
            $writing_data = WritingData::findOne(Yii::$app->request->post('writing_data'));
            $aluexa_reactivo = AluexaReactivos::find()->where('alumno_examen_id='.$writing_data->alumno_examen_id.' AND reactivo_id = '.$writing_data->reactivo_id)->one();
            if($aluexa_reactivo){
                $aluexa_reactivo->calificado = 1;
                if(!$aluexa_reactivo->save()){
                    $transaction->rollback();
                    return 0;
                }
            }
            $writing_data->grade = $total;
            if(!$writing_data->save()){
                $transaction->rollback();
                return 0;
            }
            $calificados = WritingData::find()->where('alumno_examen_id = '.$writing_data->alumno_examen_id.' AND grade IS NOT NULL')->all();
            if(count($calificados) == 2){
                $grades = 0;
                foreach ($calificados as $calificado) {
                    $grades += $calificado->grade;
                }
                $promedio = $grades / 2;
                $alumno_examen = $aluexa_reactivo->alumnoExamen;
                $calificaciones = $alumno_examen->calificaciones;
                $calificaciones->calificacionWriting = round($promedio, 0, PHP_ROUND_HALF_DOWN);
                $calificaciones->promedio_writing = round($promedio, 0, PHP_ROUND_HALF_DOWN);
                $calificaciones->academico_id = Yii::$app->user->identity->academico->id;
                $calificaciones->fecha_calificacion = time();
                if(!$calificaciones->save()){
                    $transaction->rollback();
                    return 0;
                }

                $programa = $alumno_examen->alumno->grupo->instituto->programa;
                $alumno = $alumno_examen->alumno;

                if($calificaciones->calificacionSpeaking !== null || $programa->clave != 'CLI'){
                    $statusExamen = StatusExamen::find()->where(['codigo' => 'FIN'])->one();
                    $promedio = $calificaciones->calcularPromedio();
                    $calificaciones->update();
                    $alumno->nivel_inicio_certificate_id = $alumno->nivel_certificate_id;
                    if($promedio >= 50 && $promedio <= 59.00){
                        $nivel = NivelAlumno::findOne($alumno->nivel_certificate_id);
                        if($nivel->nombre == 'A1' || $nivel->nombre == 'N/A'){
                            $nuevo_nivel = NivelAlumno::find()->where('clave="DP"')->one();
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
                        $alumno->nivel_certificate_id = $nuevo_nivel->id;
                    }else if($promedio >= 0 && $promedio <= 49.99){
                        $nuevo_nivel = NivelAlumno::find()->where('clave="DP"')->one();
                        $alumno->nivel_certificate_id = $nuevo_nivel->id;
                    }
                }else{
                    $statusExamen = StatusExamen::find()->where(['codigo' => 'SPE'])->one();
                }
                $alumno->status_examen_id = $statusExamen->id;
                if(!$alumno->save()){
                    $this->transaction->rollback();
                    return 0;
                }
            }
            $transaction->commit();
            return 1;

        }
    }

    public function actionGradeExam(){
        $form = new WritingScoreForm();
        if ($form->load(Yii::$app->request->post()) && $form->guardar()) {
            $aluexaReactivo = AluexaReactivos::findOne($form->id);
            $instituto = $aluexaReactivo->alumnoExamen->alumno->grupo->instituto;
            $examType = $aluexaReactivo->alumnoExamen->examen->tipoExamen;
            if ($this->checkForFinishedExamsOnInstitute($instituto, $examType)) {
                $instituto->setFinalizacion($examType, time());
                $instituto->update();
                $this->exportGroups($instituto->id, $examType->id);
                $this->sendEmailWithReport($instituto, $examType);
            }
            if (Yii::$app->request->isAjax) {
                return true;
            } else {
                return $this->redirect(['score-exam/index']);
            }
        }
        if (Yii::$app->request->isAjax) {
            return false;
        } else {
            return $this->redirect(['score-exam/index']);
        }
    }

    public function actionNextExam(){
        $searchModel = new WritingQuestionsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams,'v1');

        $models = $dataProvider->models;
        if($models){
            return $this->render('writing', [
                'aluexa' => $models[0]
            ]);
        }else{
            return $this->redirect(['score-exam/index']);
        }
    }

    public function exportGroups($id, $type)
    {
        $instituto = Instituto::findOne($id);
        $diagnosticType = TipoExamen::find()->where(['clave'=>'DIA'])->one();
        $certificateType = TipoExamen::find()->where(['clave' => 'CER'])->one();

        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()->setCreator('Oxford TCC')
            ->setLastModifiedBy('Oxford TCC')
            ->setTitle('Groups Report');
        $spreadsheet->getActiveSheet()->setTitle('Instituto');

        $drawing = new Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('Logo');
        $drawing->setPath(realpath('./images/logoColor.png'));
        $drawing->setHeight(100);
        $drawing->setWorksheet($spreadsheet->getActiveSheet());

        $title = array();
        switch ($type) {
            case $diagnosticType->id:
                $texto_titulo = ['DIAGNOSTIC RESULTS'];
                break;
            case $certificateType->id:
                $texto_titulo = ['Certificate RESULTS'];
                break;
        }
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

        if($type == $diagnosticType->id){
            $encabezado = ['NAME', 'GROUP', 'CODE', 'PASSWORD', 'DATE', 'TEST', 'LISTENING', 'READING', 'USE OF ENGLISH', 'WRITING', 'PERCENTAGE', 'FINAL LEVEL'];
            array_push($data, $encabezado);

            $grupos = $instituto->activeAndCurrentGroups;
            foreach ($grupos as $grupo) {
                $estudiantes = $grupo->alumnosActivos;
                foreach ($estudiantes as $estudiante) {
                    $user = $estudiante->users[0];
                    if ($user->acceso) {
                        $datos = [
                            $estudiante->fullName,
                            $estudiante->grupo->grupo,
                            $user->codigo,
                            $user->accesoDec.' ',
                        ];
                    } else {
                        $datos = [
                            $estudiante->fullName,
                            $estudiante->grupo->grupo,
                            $user->codigo,
                            'Not exist'
                        ];
                    }

                    $puntos_total = 0;
                    $calificaciones = new Calificaciones();
                    $calificaciones = $calificaciones->nivelDiagnostic($estudiante->id);
                    $calificacionUse = null;
                    $calificacionRea = null;
                    $calificacionLis = null;
                    $calificacionWri = null;
                    if($calificaciones && date( "Y",$calificaciones->fecha) > 2018){
                        $examen_realizado = AlumnoExamen::find()->where('calificaciones_id = '.$calificaciones->id)->one();
                        $fecha = date('d-m-Y', $examen_realizado->fecha_realizacion);
                        if($calificaciones->promedio_importado == null){
                            $alumno_examen = new AlumnoExamen();
                            if($alumno_examen->cerrado(1,$estudiante->id) || isset($calificaciones->calificacionWriting)){
                                $secciones = new Seccion();
                                $secciones = $secciones->puntos($calificaciones->examen);
                                $calificacionUse = ($calificaciones->calificacionUse * 100) / $secciones['USE'];
                                $calificacionRea = ($calificaciones->calificacionReading * 100) / $secciones['REA'];
                                $calificacionLis = ($calificaciones->calificacionListening * 100) / $secciones['LIS'];
                                // $calificacionWri = ($calificaciones->calificacionWriting * 100) / $secciones['WRI'];
                                $calificacionWri = $calificaciones->promedio_writing;
                                $promedio = ($calificacionUse + $calificacionRea  + $calificacionLis + $calificacionWri) / 4;
                                array_push(
                                        $datos,
                                        $fecha,
                                        'DIAGNOSTIC',
                                        floor($calificacionLis) . '%',
                                        floor($calificacionRea) . '%',
                                        floor($calificacionUse) . '%',
                                        (int) $calificacionWri . '%',
                                        floor($promedio) . '%',
                                        $estudiante->nivelAlumno->nombre
                                    );
                            }else{
                                array_push(
                                    $datos,
                                    'N/A',
                                    'N/A',
                                    'N/A',
                                    'N/A',
                                    'N/A',
                                    'N/A',
                                    'N/A',
                                    $estudiante->nivelAlumno->nombre
                                );
                            }
                        }else{
                            $calificacionUse = $calificaciones->calificacionUse;
                            $calificacionRea = $calificaciones->calificacionReading;
                            $calificacionLis = $calificaciones->calificacionListening;
                            $calificacionWri = $calificaciones->calificacionWriting;
                            $promedio = $calificaciones->promedio_importado;
                            array_push(
                                    $datos,
                                    $fecha,
                                    'DIAGNOSTIC',
                                    floor($calificacionLis) . '%',
                                    floor($calificacionRea) . '%',
                                    floor($calificacionUse) . '%',
                                    (int) $calificacionWri . '%',
                                    floor($promedio) . '%',
                                    $estudiante->nivelAlumno->nombre
                                );
                        }
                    }else{
                        $id = 0;
                        $examenes = $estudiante->alumnoExamens;
                        foreach ($examenes as $examen) {
                            $react = AluexaReactivos::find()->where(['alumno_examen_id' => $examen->id, 'calificado' => 1])->one();
                            if ($react) {
                                $id = $examen->id;
                                break;
                            }
                        }
                        if ($id) {
                            $alumnoExamenFin = AlumnoExamen::findOne($id);
                            $examenFin = $alumnoExamenFin->examen;
                            $secciones = $examenFin->seccions;
                            $calificaciones = $alumnoExamenFin->calificaciones;
                            if($calificaciones->promedio_importado == null){
                                foreach ($secciones as $seccion) {
                                    $tipo = $seccion->tipoSeccion->clave;
                                    switch ($tipo) {
                                        case "USE":
                                            if ($seccion->puntos_seccion > 0) {
                                                $calificacionUse = ($calificaciones->calificacionUse * 100) / $seccion->puntos_seccion;
                                                $puntos_total = $puntos_total + $seccion->puntos_seccion;
                                            } else
                                                $calificacionUse = 0;
                                            break;
                                        case 'REA':
                                            if ($seccion->puntos_seccion > 0) {
                                                $calificacionRea = ($calificaciones->calificacionReading * 100) / $seccion->puntos_seccion;
                                                $puntos_total = $puntos_total + $seccion->puntos_seccion;
                                            } else
                                                $calificacionRea = 0;
                                            break;
                                        case 'LIS':
                                            if ($seccion->puntos_seccion > 0) {
                                                $calificacionLis = ($calificaciones->calificacionListening * 100) / $seccion->puntos_seccion;
                                                $puntos_total = $puntos_total + $seccion->puntos_seccion;
                                            } else
                                                $calificacionLis = 0;
                                            break;
                                        case 'WRI':
                                            if ($seccion->puntos_seccion > 0) {
                                                // $calificacionWri = ($calificaciones->calificacionWriting * 100) / $seccion->puntos_seccion;
                                                $calificacionWri = $calificaciones->promedio_writing;
                                                $puntos_total = $puntos_total + $seccion->puntos_seccion;
                                            } else
                                                $calificacionWri = 0;
                                            break;
                                    }
                                }

                                $promedio = ($calificacionUse + $calificacionRea + $calificacionLis + $calificacionWri) / 4;
                            }else{
                                $calificacionUse = $calificaciones->calificacionUse;
                                $calificacionRea = $calificaciones->calificacionReading;
                                $calificacionLis = $calificaciones->calificacionListening;
                                $calificacionWri = $calificaciones->calificacionWriting;
                                $promedio = $calificaciones->promedio_importado;
                            }
                            if($alumnoExamenFin->fecha_realizacion){
                                $fecha = date('d-m-Y', $alumnoExamenFin->fecha_realizacion);
                            } else{
                                $fecha = 'N/A';
                            }

                            array_push(
                                $datos,
                                $fecha,
                                'DIAGNOSTIC',
                                floor($calificacionLis) . '%',
                                floor($calificacionRea) . '%',
                                floor($calificacionUse) . '%',
                                (int) $calificacionWri . '%',
                                floor($promedio) . '%',
                                $estudiante->nivelAlumno->nombre
                            );
                        }
                    }

                    if (!$fecha){
                        array_push(
                            $datos,
                            'N/A',
                            'N/A',
                            'N/A',
                            'N/A',
                            'N/A',
                            'N/A',
                            'N/A',
                            $estudiante->nivelAlumno->nombre
                        );
                    }
                    array_push($data, $datos);
                }
            }
        } else if ($type == $certificateType->id) {
            $programa = $instituto->programa->clave;
            if($programa == 'CLI'){
                $encabezado = ['NAME', 'GRADE', 'CODE', 'PASSWORD', 'DATE', 'TEST', 'EXAM LEVEL', 'LISTENING', 'READING', 'USE OF ENGLISH', 'WRITING', 'SPEAKING', 'PERCENTAGE', 'FINAL LEVEL'];
            }else{
                $encabezado = ['NAME', 'GRADE', 'CODE', 'PASSWORD', 'DATE', 'TEST', 'EXAM LEVEL', 'LISTENING', 'READING', 'USE OF ENGLISH', 'WRITING', 'PERCENTAGE', 'FINAL LEVEL'];
            }
            array_push($data, $encabezado);
            $grupos = $instituto->activeAndCurrentGroups;
            foreach ($grupos as $grupo) {
                foreach ($grupo->alumnosActivos as $estudiante) {
                    $user = $estudiante->users[0];
                    if ($user->acceso) {
                        $datos = [
                            $estudiante->fullName,
                            $estudiante->grupo->grupo,
                            $user->codigo,
                            $user->accesoDec.' ',
                        ];
                    } else {
                        $datos = [
                            $estudiante->fullName,
                            $estudiante->grupo->grupo,
                            $user->codigo,
                            'Not exist'
                        ];
                    }

                    $puntos_total = 0;
                    $calificacionUse = 0;
                    $calificacionRea = 0;
                    $calificacionLis = 0;
                    $calificacionWri = 0;
                    $id = 0;
                    $examenes = $estudiante->alumnoExamens;
                    $examenCert = AlumnoExamen::find()
                        ->join('INNER JOIN', 'tipo_examen', 'tipo_examen.id = alumno_examen.tipo_examen_id')
                        ->where('alumno_examen.alumno_id = '.$estudiante->id.' AND tipo_examen.clave="CER"')
                        ->one();
                    $calificaciones = $examenCert->calificaciones;
                    if($calificaciones && $calificaciones->calificacionWriting !== null && (($calificaciones->calificacionSpeaking !== null && $programa == 'CLI') || $programa != 'CLI')){
                        $examen = $examenCert->examen;
                        $calificacionLis = $calificaciones->promedio_listening;
                        $calificacionRea = $calificaciones->promedio_reading;
                        $calificacionUse = $calificaciones->promedio_use;
                        $calificacionWri = $calificaciones->promedio_writing;
                        $promedio = $calificaciones->promedio;
                        $calificacionSpeaking = $calificaciones->calificacionSpeaking;
                        if($examenCert->fecha_realizacion){
                            $fecha = date('d-m-Y', $examenCert->fecha_realizacion);
                        } else{
                            $fecha = 'N/A';
                        }

                        $nivel_inicial = $estudiante->nivelCertificateInicial->nombre;
                        if(!$nivel_inicial){
                            $nivel_inicial = $examenCert->examen->nivelAlumno->nombre;
                        }
                        $nivel_certificate = $estudiante->nivelCertificate->nombre;
                        if($programa == 'CLI'){
                            array_push(
                                $datos,
                                $fecha,
                                $examenCert->examen->tipoExamen->nombre,
                                $nivel_inicial,
                                floor($calificacionLis) . '%',
                                floor($calificacionRea) . '%',
                                floor($calificacionUse) . '%',
                                (int) $calificacionWri . '%',
                                $calificacionSpeaking !== null ? (int) $calificacionSpeaking . '%' : 'N/A',
                                floor($promedio) . '%',
                                ($nivel_certificate != null) ? $nivel_certificate : $nivel_inicial
                            );
                        }else{
                            array_push(
                                $datos,
                                $fecha,
                                $examenCert->examen->tipoExamen->nombre,
                                $nivel_inicial,
                                floor($calificacionLis) . '%',
                                floor($calificacionRea) . '%',
                                floor($calificacionUse) . '%',
                                (int) $calificacionWri . '%',
                                floor($promedio) . '%',
                                ($nivel_certificate != null) ? $nivel_certificate : $nivel_inicial
                            );
                        }

                    }else{
                        if($programa == 'CLI')
                            array_push(
                                $datos,
                                'N/A',
                                'Certificate',
                                $estudiante->nivelCertificate->nombre ? $estudiante->nivelCertificate->nombre : 'N/A',
                                'N/A',
                                'N/A',
                                'N/A',
                                'N/A',
                                'N/A',
                                'N/A',
                                'N/A'
                            );
                        else
                            array_push(
                                $datos,
                                'N/A',
                                'Certificate',
                                $estudiante->nivelCertificate->nombre ? $estudiante->nivelCertificate->nombre : 'N/A',
                                'N/A',
                                'N/A',
                                'N/A',
                                'N/A',
                                'N/A',
                                'N/A'
                            );
                    }
                    array_push($data, $datos);
                }
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
        $spreadsheet->setActiveSheetIndex(0);
        $styleArrayHeader = [
            'font' => [
                'bold' => true,
                'color' => [
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
        $styleArrayTitle = [
            'font' => [
                'size' => '20'
            ],

        ];
        $spreadsheet->getActiveSheet()->getStyle('E4:E5')->applyFromArray($styleArrayTitle);
        $spreadsheet->getActiveSheet()->getRowDimension('4')->setRowHeight(22);
        $spreadsheet->getActiveSheet()->getRowDimension('5')->setRowHeight(22);
        if ($type == $diagnosticType->id) {
            $spreadsheet->getActiveSheet()->getStyle('A8:L8')->applyFromArray($styleArrayHeader);
            $spreadsheet->getActiveSheet()->getStyle('A9:L' . strval($renglones + 8))->applyFromArray($styleArrayData);
        } else if ($type == $certificateType->id) {
            $spreadsheet->getActiveSheet()->getColumnDimension('M')->setWidth(15);
            if($programa == 'CLI'){
                $spreadsheet->getActiveSheet()->getStyle('A8:N8')->applyFromArray($styleArrayHeader);
                $spreadsheet->getActiveSheet()->getStyle('A9:N' . strval($renglones + 9))->applyFromArray($styleArrayData);
                $spreadsheet->getActiveSheet()->getColumnDimension('N')->setWidth(15);
            }else{
                $spreadsheet->getActiveSheet()->getStyle('A8:M8')->applyFromArray($styleArrayHeader);
                $spreadsheet->getActiveSheet()->getStyle('A9:M' . strval($renglones + 9))->applyFromArray($styleArrayData);
            }
        }
        $writer = new Xlsx($spreadsheet);
        $writer->save('report.xlsx');
    }

    public function actionHistory(){
        $searchModel = new ScoredQuestionsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('history', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider
        ]);
    }
    public function actionHistoryV2(){
        $searchModel = new ScoredQuestionsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, true);
        return $this->render('history', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider
        ]);
    }

    public function actionReviewWriting($id){
        $aluexa = AluexaReactivos::findOne($id);
        $academico = Academico::findOne($aluexa->alumnoExamen->calificaciones->academico_id);
        return $this->render('review_writing',[
            'aluexa' => $aluexa,
            'academico' => $academico
        ]);
    }

    public function actionSpeaking(){
        $searchModel = new SpeakingSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('speaking', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider
        ]);
    }

    public function actionDatosSpeaking(){
        $alumno_examen = AlumnoExamen::find()->where('id='.Yii::$app->request->post('id'))->one();
        return $this->renderPartial('_datos-speaking', [
            'alumno_examen' => $alumno_examen
        ]);
    }

    public function actionGuardaSpeaking(){
        $connection = \Yii::$app->db;
        $transaction = $connection->beginTransaction();
        $alumno_examen = AlumnoExamen::findOne(Yii::$app->request->post('id'));
        if($alumno_examen->calificaciones){
            $calificaciones = Calificaciones::findOne($alumno_examen->calificaciones->id);
        }else{
            $calificaciones = new Calificaciones();
        }
        $calificaciones->calificacionSpeaking = Yii::$app->request->post('puntos');
        $calificaciones->academico_speaking_id = Yii::$app->user->identity->academico->id;
        $calificaciones->fecha_calificacion_speaking = time();
        if(!$calificaciones->save()){
            $transaction->rollback();
            return 0;
        }
        if(!$alumno_examen->calificaciones){
            $alumno_examen->calificaciones_id = $calificaciones->id;
            if(!$alumno_examen->save()){
                $transaction->rollback();
                return 0;
            }
        }
        if($alumno_examen->alumno->statusExamen->codigo == 'SPE'){
            $alumno = Alumno::findOne($alumno_examen->alumno_id);
            $promedio = $calificaciones->calcularPromedio();
            $calificaciones->update();
            $alumno->nivel_inicio_certificate_id = $alumno->nivel_certificate_id;
            if($promedio >= 50 && $promedio <= 59.00){
                $nivel = NivelAlumno::findOne($alumno->nivel_certificate_id);
                if($nivel->nombre == 'A1' || $nivel->nombre == 'N/A'){
                    $nuevo_nivel = NivelAlumno::find()->where('clave="DP"')->one();
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
                $alumno->nivel_certificate_id = $nuevo_nivel->id;
            }else if($promedio >= 0 && $promedio <= 49.99){
                $nuevo_nivel = NivelAlumno::find()->where('clave="DP"')->one();
                $alumno->nivel_certificate_id = $nuevo_nivel->id;
            }
            $status_examen = StatusExamen::find()->where('codigo="FIN"')->one();
            $alumno->status_examen_id = $status_examen->id;
            if(!$alumno->save()){
                $transaction->rollback();
                return 0;
            }
        }
        $transaction->commit();
        return 1;
    }

    public function actionSpeakingHistory(){
        $searchModel = new ScoredSpeakingSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('speaking_history', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider
        ]);
    }

    public function checkForFinishedExamsOnInstitute($institute, $examType) {
        $examenes_terminados = true;
        foreach ($institute->activeAndCurrentGroups as $grupo) {
            foreach ($grupo->alumnosActivos as $alumno) {
                foreach ($alumno->alumnoExamens as $alumnoExamen) {
                    $studentLevel = $examType->clave == 'DIA' ? $alumno->nivel_alumno_id : $alumno->nivel_inicio_certificate_id;
                    if ($alumnoExamen->tipo_examen_id == $examType->id && $alumnoExamen->examen->nivel_alumno_id == $studentLevel){
                        $reactivoWriting = AluexaReactivos::find()
                            ->where([
                                'alumno_examen_id' => $alumnoExamen->id,
                                'calificado' => 1
                            ])
                            ->andWhere(['is not', 'respuestaWriting', null])
                            ->one();
                        if (!$reactivoWriting) $examenes_terminados = false;
                    }
                }
            }
        }
        return $examenes_terminados;
    }

    public function sendEmailWithReport($institute, $examType) {
        $mail = Yii::$app->mailer->compose()
            ->setTo(Yii::$app->params['email-notification'])
            ->setFrom(["equipo@blackrobot.mx" => "Oxford TCC"])
            ->setSubject("{$institute->nombre} {$examType->nombre} completed")
            ->setHtmlBody($this->renderPartial('_correo-admin', [
                'instituto' => $institute,
                'examType' => $examType
            ]))
            ->attach(Url::to(['@web/report.xlsx'], true));
        if (isset(Yii::$app->params['email-cc'])) {
            $cc = Yii::$app->params['email-cc'];
            $cc = explode(',',$cc);
        }
        array_push($cc, $institute->email);
        $mail->setCc($cc)
            ->send();
    }

    public function actionNewSpeaking() {
        $session = Yii::$app->session;
        $institute_id = $session->get('speakingInstitute');
        $ciclo = $session->get('ciclo');
        if (isset($institute_id)) {
            $institute = Instituto::findOne($institute_id);
            $ciclo_model = CicloEscolar::findOne($ciclo);
            $instituteStudents = Alumno::find()
                ->leftJoin('alumno_examen', 'alumno_examen.alumno_id = alumno.id')
                ->leftJoin('calificaciones', 'calificaciones.id = alumno_examen.calificaciones_id')
                ->leftJoin('tipo_examen', 'tipo_examen.id = alumno_examen.tipo_examen_id')
                ->leftJoin('grupo', 'grupo.id = alumno.grupo_id')
                ->leftJoin('ciclo_escolar', 'ciclo_escolar.id = grupo.ciclo_escolar_id')
                ->leftJoin('instituto', 'instituto.id = grupo.instituto_id')
                ->where([
                    'instituto.id' => $institute->id,
                    'grupo.status' => 1,
//                    'ciclo_escolar.status' => 1,
                    'grupo.ciclo_escolar_id' => $ciclo,
                    'alumno.status' => 1,
                    'tipo_examen.clave' => 'CER',
                    'calificacionSpeaking' => null
                ])
                ->groupBy('alumno.id')
                ->all();
            return $this->render('new_speaking', [
                'institute' => $institute,
                'instituteStudents' => ArrayHelper::map($instituteStudents, 'idWithCertificateLevel', 'fullName', 'nivelCertificate.clave'),
                'speakingModel' => new ScoreSpeakingForm(),
                'ciclo' => $ciclo_model,
            ]);
        } else {
            return $this->render('speaking_institute', [
                'institutes' => ArrayHelper::map(
                    Instituto::find()->where(['status' => 1, 'borrado' => 0])->all(),
                    'id',
                    'nombre'
                ),
                'ciclos' => ArrayHelper::map(CicloEscolar::find()->all(), 'id', 'nombre'),
            ]);
        }
    }

    public function actionSetInstituteSpeaking() {
        if (Yii::$app->request->isPost) {
            $institute_id = Yii::$app->request->post('institute');
            $ciclo = Yii::$app->request->post('ciclo');
            if (isset($institute_id)) {
                $session = Yii::$app->session;
                $session->set('speakingInstitute', $institute_id);
                $session->set('ciclo', $ciclo);
            }
        }
        return $this->redirect(['score-exam/new-speaking']);
    }

    public function actionScoreNewSpeaking() {
        if (Yii::$app->request->isAjax) {
            $noErrors = true;
            $count = count(Yii::$app->request->post('ScoreSpeakingForm', []));
            $speakings = [];
            for ($i = 0; $i < $count; $i++) {
                array_push($speakings, new ScoreSpeakingForm());
            }
            if (ScoreSpeakingForm::loadMultiple($speakings, Yii::$app->request->post()) && ScoreSpeakingForm::validateMultiple($speakings)) {
                foreach ($speakings as $speaking) {
                    if (!$speaking->saveSpeakingScoreDetails()) {
                        Yii::$app->session->setFlash('error', "Error at updating student: {$speaking->student_name}");
                        $noErrors = false;
                    }
                }
            }
            if ($noErrors) {
                Yii::$app->session->setFlash('success', "Students had been updated");
            }
            return $this->redirect(['score-exam/new-speaking']);
        }
    }

    public function actionClearInstituteSpeaking() {
        $session = Yii::$app->session;
        if ($session->has('speakingInstitute')) {
            $session->remove('speakingInstitute');
        }
        return $this->redirect(['score-exam/new-speaking']);
    }
    
    public function actionSectionSpeaking(){
        
        $speaking_form = new scoreSpeakingForm();
        if (!$speaking_form->saveSection(Yii::$app->request->post())){
            return false;
        }
        return true;
    }
    
    public function actionDeleteSectionSpeaking(){
        
        $speaking_form = new scoreSpeakingForm();
        if (!$speaking_form->deleteSection(Yii::$app->request->post())){
            return false;
        }
        return true;
    }
    
    public function actionGetSectionSpeaking(){
        $tipo_examen = TipoExamen::find()->where(['clave' => 'CER'])->one();
        $examen = AlumnoExamen::find()->where(['alumno_id' => Yii::$app->request->post(), 'tipo_examen_id' => $tipo_examen])->one();
        if(!$examen->calificaciones->calificaciones_spe){
            return false;
        }
        return json_encode(explode(',',$examen->calificaciones->calificaciones_spe), JSON_FORCE_OBJECT);
    }
}
