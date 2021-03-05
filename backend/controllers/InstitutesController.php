<?php
namespace backend\controllers;
use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

use app\models\search\InstitutoSearch;
use app\models\Instituto;
use app\models\search\GrupoSearch;
use app\models\Grupo;
use app\models\search\AlumnoSearch;
use app\models\Alumno;
use app\models\Examen;
use app\models\AlumnoExamen;
use app\models\Pais;
use app\models\Nivel;
use app\models\NivelAlumno;
use app\models\TipoExamen;
use app\models\Estado;
use app\models\Calificaciones;
use app\models\AluexaReactivos;
use app\models\CicloEscolar;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\IOFactory;
use backend\models\forms\InstitutoForm;
use backend\models\forms\StudentForm;
use backend\models\forms\GrupoForm;
use backend\models\forms\ImportGrupoForm;
use app\models\StatusExamen;
use app\models\Programa;
use app\models\Region;
use common\models\User;
use app\models\Seccion;
use app\models\WritingData;

use kartik\mpdf\Pdf;

set_time_limit(0);

class InstitutesController extends Controller
{
    public $paisesHabilitados = '"MX", "GT", "SV", "HN", "CR", "PA", "BR", "VE", "CO", "EC", "CL", "PE", "AR", "UY", "PY", "PT", "ES", "IT", "SI", "BG", "AM", "UA", "RU", "MN", "CN", "GR", "TR", "IN", "RO", "PR", "EG", "DO", "MA"';
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
                        'actions' => ['index', 'colegio', 'grupo', 'status-alumno', 'status-instituto', 'examen-alumno','pais-instituto','update-multiple','add-institute','add-group','save-group','add-student','save-student', 'alumno', 'export-group', 'save-institute','get-calificaciones', 'delete-multiple-institutes','delete-multiple-groups','edit-group','edit-instituto', 'export-groups', 'export-groups-complete','subpais','get-institutes-searchbar', 'get-students-searchbar','edit-student', 'institutes-export','import-students', 'logout-student','export-certificate','mezcla','export-delivery-format','inactive-institutes','crear-ciclo'],
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

        if($rol == 'ACA' || $rol == 'INS' || $rol == 'ALU')
        {
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
        $searchModel = new InstitutoSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProviderInactive = $searchModel->searchInactiveInstitutes();

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'dataProviderInactive' => $dataProviderInactive,
            'ciclos' => ArrayHelper::map(CicloEscolar::find()->all(), 'id', 'nombre'),
        ]);
    }

    public function actionColegio($id, $ciclo_escolar = 2)
    {
        $institutoForm = new InstitutoForm();
        $paises = ArrayHelper::map(Pais::find()
            ->where('codigo IN (' . $this->paisesHabilitados . ')')
            ->orderBy('nombre')
            ->all(), 'id', 'nombre');
        $colegio = Instituto::find()->where('instituto.id='.$id)->one();
        $diagnosticType = TipoExamen::find()->where(['clave'=>'DIA'])->one();
        $mockType = TipoExamen::find()->where(['clave'=>'MOC'])->one();
        $certificateType = TipoExamen::find()->where(['clave'=>'CER'])->one();
        $programa = false;
        if($colegio->programa_id){
            $programa = Programa::findOne($colegio->programa_id);
        }
        $institutoForm->loadData($colegio);
        $searchModel = new GrupoSearch();
        $dataProvider = $searchModel->search($id, $ciclo_escolar);

        return $this->render('colegio',[
            'colegio'=>$colegio,
            'programa' => $programa,
            'programas' => ArrayHelper::map(Programa::find()->all(),'id', 'nombre'),
            'paises'=>$paises,
            'dataProvider'=>$dataProvider,
            'institutoForm'=>$institutoForm,
            'diagnosticType' => $diagnosticType,
            'mockType' => $mockType,
            'certificateType' => $certificateType,
            'searchModel' => $searchModel,
            'ciclos' => ArrayHelper::map(CicloEscolar::find()->all(), 'id', 'nombre'),
            'ciclo_escolar' => $ciclo_escolar,
            'regiones' => ArrayHelper::map(Region::find()->all(), 'id', 'nombre')
        ]);
    }

    public function actionGrupo($id)
    {
        // $session = Yii::$app->session;
        $fileModel = new ImportGrupoForm;
        if ($fileModel->load(Yii::$app->request->post())) {
            $fileModel->grupoFile = UploadedFile::getInstance($fileModel, 'grupoFile');
            if ($fileModel->upload()) {
                if (!$importados = $fileModel->import()) {
                    return;
                } else {
                    // require_once('../XLS-writer/XLSXWriter.php');
                    // $filename = "Students.xlsx";

                    // header('Content-disposition: attachment; filename="' . \XLSXWriter::sanitize_filename($filename) . '"');
                    // header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
                    // header('Content-Transfer-Encoding: binary');
                    // header('Cache-Control: must-revalidate');
                    // header('Pragma: public');

                    // $writer = new \XLSXWriter();
                    // $styles_header = array(
                    //     'font' => 'Arial Narrow',
                    //     'font-size' => 8,
                    //     'font-style' => 'bold',
                    //     'fill' => '#43c2ff',
                    //     'halign' => 'center',
                    //     'border' => 'left,right,top,bottom',
                    //     'color' => '#fff',
                    //     'valign' => 'center',
                    //     'wrap_text' => true,
                    //     'auto_filter' => true,
                    //     'widths' => [30, 30, 15, 15, 15, ]
                    // );

                    // $styles_contenido = array(
                    //     [ //NOmbre
                    //         'font' => 'Arial Narrow',
                    //         'font-size' => 9,
                    //         'halign' => 'center',
                    //         'border' => 'left,right,top,bottom',
                    //         'color' => '#000',
                    //         'valign' => 'center',
                    //         'wrap_text' => false
                    //     ],
                    //     [ //Correo
                    //         'font' => 'Arial Narrow',
                    //         'font-size' => 9,
                    //         'halign' => 'center',
                    //         'border' => 'left,right,top,bottom',
                    //         'color' => '#000',
                    //         'valign' => 'center',
                    //         'wrap_text' => false
                    //     ],
                    //     [ //Código
                    //         'font' => 'Arial Narrow',
                    //         'font-size' => 9,
                    //         'halign' => 'center',
                    //         'border' => 'left,right,top,bottom',
                    //         'color' => '#000',
                    //         'valign' => 'center',
                    //         'wrap_text' => false
                    //     ],
                    //     [ //Password
                    //         'font' => 'Arial Narrow',
                    //         'font-size' => 9,
                    //         'halign' => 'center',
                    //         'border' => 'left,right,top,bottom',
                    //         'color' => '#000',
                    //         'valign' => 'center',
                    //         'wrap_text' => false
                    //     ],
                    // );
                    // $styles_datos = array(
                    //     [ //NOmbre
                    //         'font' => 'Arial Narrow',
                    //         'font-size' => 9,
                    //         'font-style' => 'bold',
                    //         'fill' => '#43c2ff',
                    //         'halign' => 'center',
                    //         'border' => 'left,right,top,bottom',
                    //         'color' => '#fff',
                    //         'valign' => 'center',
                    //         'wrap_text' => false
                    //     ],
                    //     [ //Apellidos
                    //         'font' => 'Arial Narrow',
                    //         'font-size' => 9,
                    //         'halign' => 'center',
                    //         'border' => 'left,right,top,bottom',
                    //         'color' => '#000',
                    //         'valign' => 'center',
                    //         'wrap_text' => false
                    //     ],
                    // );

                    // $encabezado = [
                    //     'NAME' => 'string',
                    //     'EMAIL' => 'string',
                    //     'CODE' => 'string',
                    //     'PASSWORD' => 'string',
                    // ];

                    // $writer->writeSheetHeader('Students', $encabezado, $styles_header);
                    // foreach ($importados as $importado) {
                    //     $datos = [
                    //         $importado['nombre'],
                    //         $importado['email'],
                    //         $importado['codigo'],
                    //         $importado['password']
                    //     ];
                    //     $writer->writeSheetRow('Students', $datos, $styles_contenido);
                    // }
                    // $writer->writeToStdOut();
                    // exit(0);

                    $grupoCorreo = Grupo::findOne($fileModel->id);
                    // $session->set('importado', true);
                    return $this->refresh();
                }
            }
        }
        $grupo = Grupo::find()->where('grupo.id='.$id)->one();
        $searchModel = new AlumnoSearch();
        $searchModel->load(Yii::$app->request->queryParams);
        $dataProvider = $searchModel->search($id);
        $examenes = ArrayHelper::map(Examen::find()->all(),'id', 'examenNameLevel');
        $tipos_examen = ArrayHelper::map(TipoExamen::find()->all(),'id', 'nombre');
        $tipos_examen[4] = 'Diagnostic V2';
        $tipos_examen[5] = 'Certificate V2';
        $tipos_examen[6] = 'Diagnostic V3';
        $niveles = ArrayHelper::map(NivelAlumno::find()->all(),'id', 'nombre');
        $diagnosticType = TipoExamen::find()->where(['clave' => 'DIA'])->one();
        $mockType = TipoExamen::find()->where(['clave' => 'MOC'])->one();
        $certificateType = TipoExamen::find()->where(['clave' => 'CER'])->one();
        $grupoForm = new GrupoForm();
        $grupoForm->nombre = $grupo->grupo;
        $grupoForm->nivel = $grupo->nivel_id;
        $grupoForm->ciclo_escolar = $grupo->ciclo_escolar_id;
        $nivelesGrupo = ArrayHelper::map(Nivel::find()->all(), 'id', 'nombre');
        $examen_nivel = [
            '1-1' => 'Diagnostic A1',
            '1-2' => 'Diagnostic A2',
            '1-3' => 'Diagnostic B1',
            '1-4' => 'Diagnostic B2',
            '1-5' => 'Diagnostic C1',
            '1-6' => 'Diagnostic C2',
            '2-1' => 'Mock A1',
            '2-2' => 'Mock A2',
            '2-3' => 'Mock B1',
            '2-4' => 'Mock B2',
            '2-5' => 'Mock C1',
            '2-6' => 'Mock C2',
            '3-1' => 'Certificate A1',
            '3-2' => 'Certificate A2',
            '3-3' => 'Certificate B1',
            '3-4' => 'Certificate B2',
            '3-5' => 'Certificate C1',
            '3-6' => 'Certificate C2',
        ];
        // $importado = false;
        // if ($session->has('importado')){
        //     $importado = true;
        //     $session->remove('importado');
        // }

        return $this->render('grupo',[
            // 'importado'=>$importado,
            'grupo' => $grupo,
            'filtro'=>$searchModel,
            'dataProvider' => $dataProvider,
            'examenes' => $examenes,
            'tipos_examen' => $tipos_examen,
            'niveles' => $niveles,
            'fileModel'=>$fileModel,
            'grupoForm'=>$grupoForm,
            'nivelesGrupo'=>$nivelesGrupo,
            'mockType'=>$mockType,
            'diagnosticType'=>$diagnosticType,
            'certificateType'=>$certificateType,
            'examen_nivel' => $examen_nivel,
            'ciclos' => ArrayHelper::map(CicloEscolar::find()->all(), 'id', 'nombre')
        ]);
   }

    public function actionAlumno($id)
    {
        $alumno = Alumno::findOne($id);
        $diagnosticType = TipoExamen::find()->where(['clave'=>'DIA'])->one();
        $mockType = TipoExamen::find()->where(['clave'=>'MOC'])->one();
        $certificateType = TipoExamen::find()->where(['clave'=>'CER'])->one();
        $studentForm = new StudentForm();
        $studentForm->loadData($alumno);
        $alumnoExamens = AlumnoExamen::find()
            ->join('INNER JOIN', 'calificaciones', 'calificaciones.id = alumno_examen.calificaciones_id')
            ->join('INNER JOIN', 'examen', 'examen.id = alumno_examen.examen_id')
            ->where(
                'alumno_examen.alumno_id = '.$alumno->id.
                ' AND examen.nivel_alumno_id = '.$alumno->nivel_alumno_id.
                ' AND calificacionWriting IS NOT NULL')
            ->all();
        $examenes = ArrayHelper::map($alumnoExamens, 'id', 'examen.examenNameNoVersion');

        return $this->render('alumno', [
            'alumno' => $alumno,
            'examenes' => $examenes,
            'studentForm' => $studentForm,
            'mockType' => $mockType->id,
            'diagnosticType' => $diagnosticType->id,
            'certificateType' => $certificateType->id
        ]);
    }

    public function actionGetCalificaciones()
    {
        $id = 0;
        if (Yii::$app->request->isAjax) {
            $id = Yii::$app->request->post('id');
        } else {
            return false;
        }

        $alumnoExamen = AlumnoExamen::findOne($id);
        $secciones = $alumnoExamen->examen->seccions;
        $calificaciones = Calificaciones::findOne($alumnoExamen->id);

        $calificacionUse = 0;
        $calificacionRea = 0;
        $calificacionLis = 0;
        $calificacionWri = $alumnoExamen->calificaciones->calificacionWriting;
        if (!$calificacionWri) {
            $calificacionWri = 0;
        }
        $promedio = 0;

        foreach ($secciones as $seccion) {
            $tipo = $seccion->tipoSeccion->clave;
            switch ($tipo) {
                case "USE":
                    $calificacionUse = ($alumnoExamen->calificaciones->calificacionUse * 100) / $seccion->puntos_seccion;
                    break;
                case 'REA':
                    $calificacionRea = ($alumnoExamen->calificaciones->calificacionReading * 100) / $seccion->puntos_seccion;
                    break;
                case 'LIS':
                    $calificacionLis = ($alumnoExamen->calificaciones->calificacionListening * 100) / $seccion->puntos_seccion;
                    break;
                case 'WRI':
                    $calificacionWri = ($alumnoExamen->calificaciones->calificacionWriting * 100) / $seccion->puntos_seccion;
                    break;
            }
        }

        $promedio = ($calificacionUse + $calificacionRea + $calificacionLis + $calificacionWri) / 4;

        return json_encode([
            'USE' => $calificacionUse,
            'REA' => $calificacionRea,
            'WRI' => $calificacionWri,
            'LIS' => $calificacionLis,
            'PRO' => $promedio,
        ]);
    }

    public function actionStatusAlumno()
    {
        $alumno = Alumno::find()->where('alumno.id='.Yii::$app->request->post('id'))->one();
        $accion = $alumno -> actualizaStatus(Yii::$app->request->post('status'));
        return $accion;
    }

    public function actionStatusInstituto()
    {
        $instituto = Instituto::find()->where('instituto.id='.Yii::$app->request->post('id'))->one();
        $accion = $instituto->actualizaStatus(Yii::$app->request->post('status'));
        if($accion){
            if(Yii::$app->request->post('status') == '1'){
                Yii::$app->mailer->compose()
                    ->setTo($instituto->email)
                    ->setFrom(["equipo@blackrobot.mx" => "Oxford TCC"])
                    ->setSubject("Account approval")
                    ->setHtmlBody($this->renderPartial('_correoAprobacion'))
                    ->send();
            }
        }
        return $accion;

    }
    public function actionExamenAlumno()
    {
        $alumno = Alumno::find()->where('alumno.id='.Yii::$app->request->post('id'))->one();
        $accion = $alumno -> actualizaExamen(Yii::$app->request->post('examen'));
        return $accion;
    }

    public function actionPaisInstituto(){
        $instituto = Instituto::find()->where('instituto.id='.Yii::$app->request->post('id'))->one();
        $accion = $instituto->actualizaPais(Yii::$app->request->post('pais'));
        return $accion;
    }

    public function actionUpdateMultiple()
    {
        $cookie = Yii::$app->controller->id.'-grupo';
        $seleccionados = $_COOKIE[$cookie] ? explode(',',$_COOKIE[$cookie]) : false;
        $alumnosMockAsignado = array();
        $alumnosCerAsignado = array();
        $alumnosExamenNoAsignado = array();
        $alumnosExamenNoTerminado = array();
        $alumnosExamenDiagnostic = array();
        $selection = (array)Yii::$app->request->post('selection');
        if($seleccionados){
            foreach($seleccionados as $seleccionado){
                if($seleccionado != '1'){
                    if(!in_array($seleccionado,$selection)){
                        $selection[] = $seleccionado;
                    }
                }
            }
        }
        $id = Yii::$app->request->post('grupo-id');
        $tipo_examen = intval(Yii::$app->request->post('tipo_examen'));
        $dia_v2 = false;
        if($tipo_examen == 4){
            $tipo_examen = 1;
            $dia_v2 = true;
        }
        $cert_v2 = false;
        if($tipo_examen == 5){
            $tipo_examen = 3;
            $cert_v2 = true;
        }
        $dia_v3 = false;
        if($tipo_examen == 6){
            $tipo_examen = 1;
            $dia_v3 = true;
        }
        $nivel = Yii::$app->request->post('nivel');
        $status = Yii::$app->request->post('status');
        if($nivel && !$tipo_examen){
            Yii::$app->session->setFlash('error', "Can't assign only level.");
            return $this->redirect(['institutes/grupo?id='.$id]);
        }

        $tipoDiagnostic = TipoExamen::find()->where(['clave' => 'DIA'])->one();
        $tipoMock = TipoExamen::find()->where(['clave' => 'MOC'])->one();
        $tipoCer = TipoExamen::find()->where(['clave' => 'CER'])->one();
        $firstLevel = NivelAlumno::find()->where(['clave' => 'A1'])->one();
        $statusExamen = StatusExamen::find()->where(['codigo' => 'PEN'])->one();

        if($tipo_examen == '' && $status == '' && $nivel == '')
        {
            return $this->redirect(['institutes/grupo?id='.$id]);
        }
        if($status)
        {
            foreach($selection as $id_select) {
                if($id_select){
                    $alumno = Alumno::find()->where('alumno.id='.$id_select)->one();
                    $alumno->status = $status == 'ACT' ? 1 : 0;
                    $alumno->save();
                }
            }
        }

        foreach($selection as $id_select) {
            if($id_select){
                $alumno = Alumno::find()->where('alumno.id='.$id_select)->one();
                if($tipo_examen)
                {
                    $mockAsignado = false;
                    $cerAsignado = false;
                    if($tipo_examen == $tipoDiagnostic->id){
                        foreach($alumno->alumnoExamens as $examen_asignado){
                            if($examen_asignado->tipo_examen_id == $tipoMock->id){
                                $mockAsignado = true;
                            }
                            if($examen_asignado->tipo_examen_id == $tipoCer->id){
                                $cerAsignado = true;
                            }
                        }
                        if($mockAsignado || $cerAsignado){
                            array_push($alumnosExamenDiagnostic, $alumno->fullName);
                            continue;
                        }
                    }
                    if($alumno->alumnoExamens && $tipo_examen == $tipoMock->id){
                        $terminado = false;
                        foreach($alumno->alumnoExamens as $examenAsignado){
                            $writingCalificado = AluexaReactivos::find()->where(['alumno_examen_id' => $examenAsignado->id, 'calificado'=>1])->one();
                            if($writingCalificado){
                                $terminado = true;
                            }
                            if(($examenAsignado->tipo_examen_id == $tipoMock->id || $examenAsignado->tipo_examen_id == $tipoCer->id )){
                                if(!$examenAsignado->examen_id){
                                    $examenAsignado->delete();
                                }else{
                                    $examenAsignado->delete();
                                }
                                $terminado = true;
                            }
                            if($examenAsignado->tipo_examen_id == $tipoDiagnostic->id && !$examenAsignado->examen_id)
                            {
                                $examenAsignado->delete();
                                $terminado = true;
                            }
                        }
                        if(!$terminado){
                            array_push($alumnosExamenNoTerminado, $alumno->fullName);
                            continue;
                        }
                    }

                    if($alumno->alumnoExamens && $tipo_examen == $tipoCer->id){
                        $terminado = false;
                        foreach($alumno->alumnoExamens as $examenAsignado){
                            // if(!$examenAsignado->fecha_realizacion){
                            //     $examenAsignado->fecha_realizacion = time();
                            //     $examenAsignado->save();
                            // }
                            if($examenAsignado->tipo_examen_id == $tipoDiagnostic->id)
                            {
                                if(!$examenAsignado->examen_id){
                                    $examenAsignado->fecha_realizacion = time();
                                    $examenAsignado->save();
                                }
                                $terminado = true;
                            }else if($examenAsignado->tipo_examen_id == $tipoMock->id){
                                if($examenAsignado->calificaciones){
                                    $terminado = true;
                                }else{
                                    $examen = Examen::find()->where('status = 1 AND tipo_examen_id = '.$examenAsignado->tipo_examen_id)->one();
                                    $examenAsignado->examen_id = $examen->id;
                                    $examenAsignado->fecha_realizacion = time();
                                    $examenAsignado->save();
                                    $terminado = true;
                                }
                            }else if($examenAsignado->tipo_examen_id == $tipoCer->id){
                                WritingData::deleteAll('alumno_examen_id = '.$examenAsignado->id);
                                $examenAsignado->delete();
                                $terminado = true;
                            }
                        }
                        if(!$terminado){
                            array_push($alumnosCerAsignado, $alumno->fullName);
                            continue;
                        }
                    }

                    $examenAsignado = AlumnoExamen::find()->where(['alumno_id'=>$alumno->id, 'tipo_examen_id' => $tipoDiagnostic->id ])->one();
                    $examenAsignadoMock = AlumnoExamen::find()->where(['alumno_id'=>$alumno->id, 'tipo_examen_id' => $tipoMock->id ])->one();
                    if($examenAsignado){
                        foreach($alumno->alumnoExamens as $examen_realizado){
                            if($examen_realizado->tipo_examen_id == $tipo_examen){
                                $calificaciones = $examen_realizado->calificaciones;
                                if($calificaciones){
                                    $calificaciones->delete();
                                } else{
                                    $examen_realizado->delete();
                                }
                            }
                        }
                    }
                    if($tipo_examen == $tipoCer->id){
                        foreach($alumno->alumnoExamens as $examen_realizado){
                            if($examen_realizado->tipo_examen_id == $tipo_examen){
                                $calificaciones = $examen_realizado->calificaciones;
                                if($calificaciones){
                                    $calificaciones->delete();
                                } else{
                                    $examen_realizado->delete();
                                }
                            }
                        }
                    }

                    $alumno->status_examen_id = $statusExamen->id;
                    if($dia_v2){
                        $alumno->diagnostic_v2 = 1;
                        $alumno->diagnostic_v3 = NULL;
                    }
                    if($dia_v3){
                        $alumno->diagnostic_v2 = NULL;
                        $alumno->diagnostic_v3 = 1;
                    }
                    if($cert_v2){
                        $alumno->certificate_v2 = 1;
                    }else{
                        $alumno->certificate_v2 = NULL;
                    }
                    $alumno->update();

                    $alumnoExamen =  new AlumnoExamen();
                    $alumnoExamen->alumno_id = $alumno->id;
                    $alumnoExamen->tipo_examen_id = $tipo_examen;
                    $alumnoExamen->status = 1;
                    if(!$alumnoExamen->save()){
                        return $this->redirect(['institutes/grupo?id=' . $id]);
                    }
                }
                if($nivel){
                    $sin_nivel = NivelAlumno::find()->where('clave="NO"')->one();
                    if($tipo_examen){
                        if($tipoDiagnostic->id == $tipo_examen){
                            $alumno->nivel_alumno_id = $nivel;
                            if(!$alumno->nivel_mock_id){
                                $alumno->nivel_mock_id = $sin_nivel->id;
                                $alumno->nivel_inicio_mock_id = $sin_nivel->id;
                            }
                        }else if($tipoMock->id == $tipo_examen){
                            if(!$alumno->nivel_alumno_id){
                                $alumno->nivel_alumno_id = $sin_nivel->id;
                            }
                            $alumno->nivel_mock_id = $nivel;
                            $alumno->nivel_inicio_mock_id = $nivel;
                        }else if($tipoCer->id == $tipo_examen){
                            if(!$alumno->nivel_alumno_id){
                                $alumno->nivel_alumno_id = $sin_nivel->id;
                            }
                            if(!$alumno->nivel_mock_id){
                                $alumno->nivel_mock_id = $sin_nivel->id;
                                $alumno->nivel_inicio_mock_id = $sin_nivel->id;
                            }
                            $alumno->nivel_certificate_id = $nivel;
                        }
                    }else{
                        $alumno->nivel_alumno_id = $nivel;
                        $alumno->nivel_mock_id = $sin_nivel->id;
                        $alumno->nivel_inicio_mock_id = $sin_nivel->id;
                    }
                    if(!$alumno->save()){
                         return $this->redirect(['institutes/grupo?id=' . $id]);
                    }
                }
            }
        }
        if(!empty($alumnosExamenDiagnostic)){
            Yii::$app->session->setFlash('error', "Can't assign Diagnostic to this students: " . implode(', ',$alumnosExamenDiagnostic));
        }
        if(!empty($alumnosExamenNoTerminado)){
            Yii::$app->session->setFlash('error', "This students haven't finished Diagnostic Exam: " . implode(', ',$alumnosExamenNoTerminado));
        }
        if(!empty($alumnosMockAsignado)){
            Yii::$app->session->setFlash('error', "Can't assign Mock to this students: " . implode(', ', $alumnosMockAsignado));
        }
        if(!empty($alumnosCerAsignado)){
            Yii::$app->session->setFlash('error', "Can't assign Certificate or Mock to this students: " . implode(', ', $alumnosCerAsignado));
        }
        setcookie($cookie,'');
        return $this->redirect(['institutes/grupo?id='.$id]);
    }

    public function actionAddInstitute(){
        $institutoForm = new InstitutoForm();

        $paises = ArrayHelper::map(Pais::find()
            ->where('codigo IN (' . $this->paisesHabilitados . ')')
            ->orderBy('nombre')
            ->all(), 'id', 'nombre');
        $programas = ArrayHelper::map(Programa::find()->all(), 'id', 'nombre');

        return $this->renderAjax('_add-institute', [
            'institutoForm' => $institutoForm,
            'paises'=>$paises,
            'programas'=>$programas,
        ]);
    }

    public function actionSaveInstitute(){
        $institutoForm = new InstitutoForm();
        if ($institutoForm->load(Yii::$app->request->post()) && $institutoForm->guardar()) {
            Yii::$app->session->setFlash('success', 'Success at saving institute');
        } else {
            Yii::$app->session->setFlash('error', isset($institutoForm->errorMessage) ? $institutoForm->errorMessage : "Error at data loading");
        }
        return $this->actionIndex();
    }

    public function actionAddGroup($id){
        $grupoForm = new GrupoForm();

        $niveles = ArrayHelper::map(Nivel::find()->all(), 'clave', 'nombre');

        return $this->renderAjax('_add-group', [
            'grupoForm' => $grupoForm,
            'niveles'=>$niveles,
            'id'=>$id,
        ]);
    }

   public function actionSaveGroup(){
        $grupoForm = new GrupoForm();

        if ($grupoForm->load(Yii::$app->request->post())) {
                $guardar = $grupoForm->guardar();
                return $guardar;
            }
    }

    public function actionAddStudent($id){
        $studentForm = new StudentForm();

        $niveles = ArrayHelper::map(NivelAlumno::find()->all(),'clave', 'nombre');

        return $this->renderAjax('_add-student', [
                'studentForm' => $studentForm,
                'niveles'=>$niveles,
                'id'=>$id,
            ]);
    }

    public function actionSaveStudent(){
        $studentForm = new StudentForm();

        if ($studentForm->load(Yii::$app->request->post())) {
            $guardar = $studentForm->guardar();
            return $guardar;
        }
    }

    public function actionExportGroup($id, $type, $file = "xls"){
        if(Yii::$app->user->identity->tipoUsuario->clave == 'INS'){
            if($grupo->instituto_id != Yii::$app->user->identity->instituto->id){
                return $this->redirect(\Yii::$app->urlManager->createUrl("site/index"));
            }
        }
        $estudiantes = Alumno::find()
            ->where(["alumno.grupo_id" => $id, 'status' => 1])
            ->all();
        $grupo = Grupo::findOne($id);
        $instituto = Instituto::findOne($grupo->instituto_id);
        $diagnosticType = TipoExamen::find()->where(['clave' => 'DIA'])->one();
        $mockType = TipoExamen::find()->where(['clave' => 'MOC'])->one();
        $certificateType = TipoExamen::find()->where(['clave' => 'CER'])->one();

        // nueva hoja de calculo
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()->setCreator('Oxford TCC')
            ->setLastModifiedBy('Oxford TCC')
            ->setTitle('Group Report');
        $spreadsheet->getActiveSheet()->setTitle('Alumnos');

        //mostrando imagen de logo
        $drawing = new Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('Logo');
        $drawing->setPath(realpath('./images/oxford_education_logo.png'));
        $drawing->setHeight(100);
        $drawing->setCoordinates('A1');
        $drawing->setWorksheet($spreadsheet->getActiveSheet());

        $drawing2 = new Drawing();
        $drawing2->setName('Logo');
        $drawing2->setDescription('Logo');
        $drawing2->setPath(realpath('./images/logoColor.png'));
        $drawing2->setHeight(100);
        $drawing2->setCoordinates('B1');
        $drawing2->setWorksheet($spreadsheet->getActiveSheet());

        switch ($type) {
            case $diagnosticType->id:
                $texto_titulo = ['', 'DIAGNOSTIC RESULTS'];
                break;
            case $mockType->id:
                $texto_titulo = ['', 'MOCK RESULTS'];
                break;
            case $certificateType->id:
                $texto_titulo = ['', 'CERTIFICATE RESULTS'];
                break;
        }

        $currentMonth = date('M Y');
        $instituteAddress = $instituto->direccion;
        $title = [
            $texto_titulo,
            [],
            ['School name:', "{$instituto->nombre}, {$currentMonth}"],
            ['Address:', "{$instituteAddress->calle} {$instituteAddress->numero_int}, {$instituteAddress->colonia}, {$instituteAddress->estado->estadonombre}, {$instituteAddress->pais->nombre}"],
            ['Postcode:', "{$instituteAddress->codigo_postal}"],
            ['Phone number:', "{$instituto->telefono}"],
            ['Contact name:', isset($instituto->profesors[0]) ? $instituto->profesors[0]->nombre : 'N/A'],
            ['Email:', "{$instituto->email}"],
        ];
        $spreadsheet->getActiveSheet()
            ->fromArray(
                $title,
                null,
                'E1'
            );


        // cargando datos al arreglo
        $data = array();
        if($type == $diagnosticType->id){
            $encabezado = ['NAME', 'GROUP', 'CODE', 'PASSWORD', 'DATE', 'TEST', 'LISTENING', 'READING', 'USE OF ENGLISH', 'WRITING', 'PERCENTAGE', 'FINAL LEVEL'];
            array_push($data, $encabezado);
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
                $fecha = null;
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
                        if($alumno_examen->cerrado(1,$estudiante->id) || !is_null($calificaciones->calificacionWriting)){
                            array_push(
                                    $datos,
                                    $fecha,
                                    'DIAGNOSTIC',
                                    floor($calificaciones->promedio_listening) . '%',
                                    floor($calificaciones->promedio_reading) . '%',
                                    floor($calificaciones->promedio_use) . '%',
                                    floor($calificaciones->promedio_writing) . '%',
                                    floor($calificaciones->promedio) . '%',
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
                        $calificacionWri = $calificaciones->promedio_writing;
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
                        if ($examen->tipoExamen->clave == 'DIA') {
                            $react = AluexaReactivos::find()->where(['alumno_examen_id' => $examen->id, 'calificado' => 1])->one();
                            if ($react) {
                                $id = $examen->id;
                                break;
                            }
                        }
                    }
                    if ($id) {
                        $alumnoExamenFin = AlumnoExamen::findOne($id);
                        $examenFin = $alumnoExamenFin->examen;
                        $secciones = $examenFin->seccions;
                        $calificaciones = $alumnoExamenFin->calificaciones;
                        if($calificaciones->promedio_importado == null){
                            $calificacionUse = $calificaciones->promedio_listening;
                            $calificacionRea = $calificaciones->promedio_reading;
                            $calificacionLis = $calificaciones->promedio_use;
                            $calificacionWri = $calificaciones->promedio_writing;
                            $promedio = $calificaciones->promedio;
                        }else{
                            $calificacionUse = $calificaciones->calificacionUse;
                            $calificacionRea = $calificaciones->calificacionReading;
                            $calificacionLis = $calificaciones->calificacionListening;
                            $calificacionWri = $calificaciones->promedio_writing;
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
        } else if($type == $mockType->id){
            $encabezado = ['NAME', 'GROUP', 'CODE', 'PASSWORD', 'DATE', 'TEST', 'EXAM LEVEL', 'LISTENING', 'READING', 'USE OF ENGLISH', 'PERCENTAGE', 'SUGGESTED LEVEL'];
            array_push($data, $encabezado);
            foreach ($grupo->alumnosActivos as $alumno) {
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
                    $examenMock = AlumnoExamen::find()
                        ->joinWith('examen')
                        ->where([
                            'alumno_id' => $alumno->id,
                            'examen.tipo_examen_id' => $mockType->id,
                            'examen.nivel_alumno_id' => $alumno->nivel_inicio_mock_id
                        ])
                        ->one();
                }
                if ($examenMock && $examenMock->calificaciones) {
                    $calificaciones = $examenMock->calificaciones;
                    $nivel_inicial =  NivelAlumno::findOne($alumno->nivel_inicio_mock_id);
                    $nivel_inicial = $nivel_inicial ? $nivel_inicial->nombre : 'N/A';
                    array_push(
                        $datos,
                        date('d-m-Y', $examenMock->fecha_realizacion),
                        'Mock',
                        $nivel_inicial,
                        floor($calificaciones->promedio_listening) . '%',
                        floor($calificaciones->promedio_reading) . '%',
                        floor($calificaciones->promedio_use) . '%',
                        floor($calificaciones->promedio) . '%',
                        $alumno->nivelMock->nombre ? $alumno->nivelMock->nombre : 'N/A'
                    );
                } else {
                    $nivel_inicial =  NivelAlumno::findOne($alumno->nivel_inicio_mock_id);
                    $nivel_inicial = $nivel_inicial ? $nivel_inicial->nombre : 'N/A';
                    array_push(
                        $datos,
                        'N/A',
                        'Mock',
                        $nivel_inicial,
                        'N/A',
                        'N/A',
                        'N/A',
                        'N/A',
                        'N/A'
                    );
                }
                array_push($data, $datos);
            }
        } else if($type == $certificateType->id){
            $programa = $instituto->programa->clave;
            if($programa == 'CLI'){
                $encabezado = ['NAME', 'GROUP', 'CODE', 'PASSWORD', 'DATE', 'TEST', 'EXAM LEVEL', 'LISTENING', 'READING', 'USE OF ENGLISH', 'WRITING', 'SPEAKING', 'PERCENTAGE', 'FINAL LEVEL'];
            }else{
                $encabezado = ['NAME', 'GROUP', 'CODE', 'PASSWORD', 'DATE', 'TEST', 'EXAM LEVEL', 'LISTENING', 'READING', 'USE OF ENGLISH', 'WRITING', 'PERCENTAGE', 'FINAL LEVEL'];
            }
            array_push($data, $encabezado);
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
                $calificacionUse = 0;
                $calificacionRea = 0;
                $calificacionLis = 0;
                $calificacionWri = 0;
                $id = 0;
                $examenes = $estudiante->alumnoExamens;
                $examenCert = AlumnoExamen::find()
                    ->joinWith('examen')
                    ->where([
                        'alumno_id' => $estudiante->id,
                        'examen.tipo_examen_id' => $certificateType->id
                    ])
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
                            $nivel_inicial,
                            ($nivel_inicial != null && $nivel_inicial != 'N/A') ? $nivel_inicial : $nivel_certificate,
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

        // imprimiendo datos del arreglo
        $renglones = count($data) - 1;
        // var_dump($renglones);exit;
        $spreadsheet->getActiveSheet()
            ->fromArray(
                $data,
                null,
                'A9'
            );

        //creando estilos
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

        $styleSheetTitle = [
            'font' => [
                'bold' => true,
                'size' => 20
            ]
        ];
        $styleColumnData = [
            'font' => [
                'bold' => true,
                'color'=>[
                    'argb' => 'FF0F4F2C'
                ]
            ],
        ];
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
        $noTableStyle = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_NONE,
                ],
            ],
        ];
        $spreadsheet->getActiveSheet()->getStyle('F1')->applyFromArray($styleSheetTitle);
        $spreadsheet->getActiveSheet()->getStyle('A1:L8')->applyFromArray($noTableStyle);
        $spreadsheet->getActiveSheet()->getStyle('E1:E8')->applyFromArray($styleColumnData);
        if ($type == $diagnosticType->id) {
            $spreadsheet->getActiveSheet()->getStyle('A9:L9')->applyFromArray($styleArrayHeader);
            $spreadsheet->getActiveSheet()->getStyle('A10:L' . strval($renglones + 9))->applyFromArray($styleArrayData);
        } else if ($type == $mockType->id) {
            $spreadsheet->getActiveSheet()->getStyle('A9:L9')->applyFromArray($styleArrayHeader);
            $spreadsheet->getActiveSheet()->getStyle('A10:L' . strval($renglones + 9))->applyFromArray($styleArrayData);
        } else if ($type == $certificateType->id) {
            $spreadsheet->getActiveSheet()->getColumnDimension('M')->setWidth(15);
            if($programa == 'CLI'){
                $spreadsheet->getActiveSheet()->getStyle('A9:N9')->applyFromArray($styleArrayHeader);
                $spreadsheet->getActiveSheet()->getStyle('A10:N' . strval($renglones + 9))->applyFromArray($styleArrayData);
                $spreadsheet->getActiveSheet()->getStyle('A1:N8')->applyFromArray($noTableStyle);
                $spreadsheet->getActiveSheet()->getColumnDimension('N')->setWidth(15);
            }else{
                $spreadsheet->getActiveSheet()->getStyle('A9:M9')->applyFromArray($styleArrayHeader);
                $spreadsheet->getActiveSheet()->getStyle('A10:M' . strval($renglones + 9))->applyFromArray($styleArrayData);
                $spreadsheet->getActiveSheet()->getStyle('A1:M8')->applyFromArray($noTableStyle);
            }
        }
        $spreadsheet->setActiveSheetIndex(0);
        switch ($file) {
            case 'pdf':
                header('Content-Type: application/pdf');
                header('Content-Disposition: attachment;filename="Institute.pdf"');
                $writer = IOFactory::createWriter($spreadsheet, 'Mpdf')
                    ->setTempDir(Yii::getAlias('@runtime') . '/cache/mpdf-tmp');
                break;
            case 'xls':
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment;filename="Institute.xlsx"');
                $spreadsheet->getActiveSheet()->setShowGridlines(false);
                $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
                break;
        }
        header('Cache-Control: max-age=0');
        setCookie("downloadStarted", 1, time() + 20, '/', "", false, false);
        $writer->save('php://output');
        exit;
    }

    public function actionDeleteMultipleInstitutes(){
        $selection = (array)Yii::$app->request->post('selection');

        if(empty($selection)){
            Yii::$app->session->setFlash('error', 'No institutes selected to edit');
        } else{
            if (Yii::$app->request->post('action') == "delete") {
                foreach($selection as $selected){
                    $instituto = Instituto::findOne($selected);
                    $instituto->borrado = 1;
                    $instituto->update();
                    $grupos = $instituto->grupos;
                    foreach ($grupos as $grupo) {
                        $grupo->status = 0;
                        $grupo->update();
                        $alumnos = $grupo->alumnos;
                        foreach ($alumnos as $alumno) {
                            $alumno->status = 0;
                            $alumno->update();
                        }
                    }
                }
            } else if (Yii::$app->request->post('action') == "cancel") {
                foreach($selection as $selected){
                    $instituto = Instituto::findOne($selected);
                    $instituto->status = $instituto::STATUS_CANCELLED;
                    $instituto->update();
                }
            }
        }
        return $this->redirect(['institutes/index']);
    }

    public function actionDeleteMultipleGroups()
    {
        $selection = (array)Yii::$app->request->post('selection');

        if (empty($selection)) {
            return;
        } else {
            foreach ($selection as $selected) {
                $grupo = Grupo::findOne($selected);
                $grupo->status = 0;
                $grupo->update();

                $alumnos = $grupo->alumnos;
                foreach ($alumnos as $alumno) {
                    $alumno->status = 0;
                    $alumno->update();
                }
            }
        }
        return $this->redirect(['institutes/colegio', 'id' => $grupo->instituto->id, 'ciclo_escolar' => Yii::$app->request->post('ciclo_escolar')]);
    }

    public function actionEditGroup(){
        $form = new GrupoForm();
        if ($form->load(Yii::$app->request->post())) {
            if ($form->updateData()) {
                return $this->actionGrupo($form->id);
            }
        }
        Yii::$app->session->setFlash('error', "Data can't be updated.");
        return $this->goBack();
    }

    public function actionEditInstituto(){
        $form = new InstitutoForm();
        if ($form->load(Yii::$app->request->post())) {
            if ($form->updateData()) {
                return $this->actionColegio($form->id);
            }
        }
        Yii::$app->session->setFlash('error', "Data can't be updated.");
        return $this->goBack();
    }

    public function actionExportGroups($id, $type, $file = 'xls')
    {
        if(Yii::$app->user->identity->tipoUsuario->clave == 'INS'){
            if($id != Yii::$app->user->identity->instituto->id){
                return $this->redirect(\Yii::$app->urlManager->createUrl("site/index"));
            }
        }
        $instituto = Instituto::findOne($id);
        $diagnosticType = TipoExamen::find()->where(['clave'=>'DIA'])->one();
        $mockType = TipoExamen::find()->where(['clave'=>'MOC'])->one();
        $certificateType = TipoExamen::find()->where(['clave' => 'CER'])->one();

        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()->setCreator('Oxford TCC')
            ->setLastModifiedBy('Oxford TCC')
            ->setTitle('Groups Report');
        $spreadsheet->getActiveSheet()->setTitle('Instituto');

        $drawing = new Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('Logo');
        $drawing->setPath(realpath('./images/oxford_education_logo.png'));
        $drawing->setHeight(100);
        $drawing->setCoordinates('A1');
        $drawing->setWorksheet($spreadsheet->getActiveSheet());

        $drawing2 = new Drawing();
        $drawing2->setName('Logo');
        $drawing2->setDescription('Logo');
        $drawing2->setPath(realpath('./images/logoColor.png'));
        $drawing2->setHeight(100);
        $drawing2->setCoordinates('B1');
        $drawing2->setWorksheet($spreadsheet->getActiveSheet());

        switch ($type) {
            case $diagnosticType->id:
                $texto_titulo = ['', 'DIAGNOSTIC RESULTS'];
                break;
            case $mockType->id:
                $texto_titulo = ['', 'MOCK RESULTS'];
                break;
            case $certificateType->id:
                $texto_titulo = ['', 'CERTIFICATE RESULTS'];
                break;
        }

        $currentMonth = date('M Y');
        $instituteAddress = $instituto->direccion;
        $title = [
            $texto_titulo,
            [],
            ['School name:', "{$instituto->nombre}, {$currentMonth}"],
            ['Address:', "{$instituteAddress->calle} {$instituteAddress->numero_int}, {$instituteAddress->colonia}, {$instituteAddress->estado->estadonombre}, {$instituteAddress->pais->nombre}"],
            ['Postcode:', "{$instituteAddress->codigo_postal}"],
            ['Phone number:', "{$instituto->telefono}"],
            ['Contact name:', isset($instituto->profesors[0]) ? $instituto->profesors[0]->nombre : 'N/A'],
            ['Email:', "{$instituto->email}"],
        ];
        $spreadsheet->getActiveSheet()
            ->fromArray(
                $title,
                null,
                'E1'
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
                                array_push(
                                    $datos,
                                    $fecha,
                                    'DIAGNOSTIC',
                                    floor($calificaciones->promedio_listening) . '%',
                                    floor($calificaciones->promedio_reading) . '%',
                                    floor($calificaciones->promedio_use) . '%',
                                    floor($calificaciones->promedio_writing) . '%',
                                    floor($calificaciones->promedio) . '%',
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
                            if ($examen->tipoExamen->clave == 'DIA') {
                                $react = AluexaReactivos::find()
                                    ->where([
                                        'alumno_examen_id' => $examen->id,
                                        'calificado' => 1,
                                    ])->one();
                                if ($react) {
                                    $id = $examen->id;
                                    break;
                                }
                            }
                        }
                        if ($id) {
                            $alumnoExamenFin = AlumnoExamen::findOne($id);
                            $examenFin = $alumnoExamenFin->examen;
                            $secciones = $examenFin->seccions;
                            $calificaciones = $alumnoExamenFin->calificaciones;
                            if($calificaciones->promedio_importado == null){
                                $calificacionUse = $calificaciones->promedio_listening;
                                $calificacionRea = $calificaciones->promedio_reading;
                                $calificacionLis = $calificaciones->promedio_use;
                                $calificacionWri = $calificaciones->promedio_writing;
                                $promedio = $calificaciones->promedio;
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
        } else if ($type == $mockType->id){
            $encabezado = ['NAME', 'GRADE', 'CODE', 'PASSWORD', 'DATE', 'TEST', 'EXAM LEVEL', 'LISTENING', 'READING', 'USE OF ENGLISH', 'PERCENTAGE', 'SUGGESTED LEVEL'];
            array_push($data, $encabezado);
            $grupos = $instituto->activeAndCurrentGroups;
            foreach ($grupos as $grupo) {
                foreach ($grupo->alumnosActivos as $alumno) {
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
                        $calificaciones = $examenMock->calificaciones;
                        $nivel_inicial =  NivelAlumno::findOne($alumno->nivel_inicio_mock_id);
                        $nivel_inicial = $nivel_inicial ? $nivel_inicial->nombre : 'N/A';
                        array_push(
                            $datos,
                            date('d-m-Y', $examenMock->fecha_realizacion),
                            'Mock',
                            $nivel_inicial,
                            floor($calificaciones->promedio_listening) . '%',
                            floor($calificaciones->promedio_reading) . '%',
                            floor($calificaciones->promedio_use) . '%',
                            floor($calificaciones->promedio) . '%',
                            $alumno->nivelMock->nombre ? $alumno->nivelMock->nombre : 'N/A'
                        );
                    } else {
                        $nivel_inicial =  NivelAlumno::findOne($alumno->nivel_inicio_mock_id);
                        $nivel_inicial = $nivel_inicial ? $nivel_inicial->nombre : 'N/A';
                        array_push(
                            $datos,
                            'N/A',
                            'Mock',
                            $nivel_inicial,
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
        } else if($type == $certificateType->id){
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
                'A9'
            );

        $renglones = count($data) - 1;

         //creando estilos
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

        $styleSheetTitle = [
            'font' => [
                'bold' => true,
                'size' => 20
            ]
        ];
        $styleColumnData = [
            'font' => [
                'bold' => true,
                'color'=>[
                    'argb' => 'FF0F4F2C'
                ]
            ],
        ];
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
        $noTableStyle = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_NONE,
                ],
            ],
        ];
        $spreadsheet->getActiveSheet()->getStyle('F1')->applyFromArray($styleSheetTitle);
        $spreadsheet->getActiveSheet()->getStyle('A1:L8')->applyFromArray($noTableStyle);
        $spreadsheet->getActiveSheet()->getStyle('E1:E8')->applyFromArray($styleColumnData);
        if ($type == $diagnosticType->id) {
            $spreadsheet->getActiveSheet()->getStyle('A9:L9')->applyFromArray($styleArrayHeader);
            $spreadsheet->getActiveSheet()->getStyle('A10:L' . strval($renglones + 9))->applyFromArray($styleArrayData);
        } else if ($type == $mockType->id) {
            $spreadsheet->getActiveSheet()->getStyle('A9:L9')->applyFromArray($styleArrayHeader);
            $spreadsheet->getActiveSheet()->getStyle('A10:L' . strval($renglones + 9))->applyFromArray($styleArrayData);
        } else if ($type == $certificateType->id) {
            $spreadsheet->getActiveSheet()->getColumnDimension('M')->setWidth(15);
            if($programa == 'CLI'){
                $spreadsheet->getActiveSheet()->getStyle('A9:N9')->applyFromArray($styleArrayHeader);
                $spreadsheet->getActiveSheet()->getStyle('A10:N' . strval($renglones + 9))->applyFromArray($styleArrayData);
                $spreadsheet->getActiveSheet()->getStyle('A1:N8')->applyFromArray($noTableStyle);
                $spreadsheet->getActiveSheet()->getColumnDimension('N')->setWidth(15);
            }else{
                $spreadsheet->getActiveSheet()->getStyle('A9:M9')->applyFromArray($styleArrayHeader);
                $spreadsheet->getActiveSheet()->getStyle('A10:M' . strval($renglones + 9))->applyFromArray($styleArrayData);
                $spreadsheet->getActiveSheet()->getStyle('A1:M8')->applyFromArray($noTableStyle);
            }
        }

        switch ($file) {
            case 'pdf':
                header('Content-Type: application/pdf');
                header('Content-Disposition: attachment;filename="Institute.pdf"');
                $writer = IOFactory::createWriter($spreadsheet, 'Mpdf')
                    ->setTempDir(Yii::getAlias('@runtime') . '/cache/mpdf-tmp');
                break;
            case 'xls':
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment;filename="Institute.xlsx"');
                $spreadsheet->getActiveSheet()->setShowGridlines(false);
                $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
                break;
        }
        header('Cache-Control: max-age=0');
        setCookie("downloadStarted", 1, time() + 20, '/', "", false, false);
        $writer->save('php://output');
        exit;
    }

    public function actionExportGroupsComplete($id, $ciclo_escolar){
        ini_set('memory_limit', '-1');
        $instituto = Instituto::findOne($id);
        $niveles = ['A1', 'A2', 'B1', 'B2', 'C1', 'C2'];

        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()->setCreator('Oxford TCC')
            ->setLastModifiedBy('Maarten Balliauw')
            ->setTitle('Office 2007 XLSX Test Document')
            ->setSubject('Office 2007 XLSX Test Document')
            ->setDescription('Test document for Office 2007 XLSX, generated using PHP classes.')
            ->setKeywords('office 2007 openxml php')
            ->setCategory('Test result file');
        $spreadsheet->getActiveSheet()->setTitle('Instituto');

        $drawing = new Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('Logo');
        $drawing->setPath(realpath('./images/logoColor.png'));
        $drawing->setHeight(100);
        $drawing->setWorksheet($spreadsheet->getActiveSheet());

        $title = array();
        $texto_titulo = ['DIAGNOSTIC RESULTS'];
        $nombre_instituto = [$instituto->nombre . ', ' . strtoupper(date('M Y'))];
        array_push($title, $texto_titulo);
        array_push($title, $nombre_instituto);
        $spreadsheet->getActiveSheet()
            ->fromArray(
                $title,
                null,
                'E4'
            );

        $encabezadoNiveles = [
            null, null, null, null, 'A1', null, null, null, null,
            null, null, null, null, 'A2', null, null, null, null,
            null, null, null, null, 'B1', null, null, null, null,
            null, null, null, null, 'B2', null, null, null, null,
            null, null, null, null, 'C1', null, null, null, null,
            null, null, null, null, 'C2', null, null, null, null,
            null, null, null, 'MOCK', null, null, null,
            null, null, null, null, null, 'CERTIFICATE', null, null, null, null,
            null,'Final Levels', null
        ];
        $spreadsheet->getActiveSheet()
            ->fromArray(
                $encabezadoNiveles,
                null,
                'F7'
            );

        $data = array();
        $encabezado = [
            'NAME', 'GROUP', 'CODE', 'PASSWORD','DATE',
            'LISTENING POINTS','LISTENING SCORE', 'READING POINTS','READING SCORE','USE OF ENGLISH POINTS', 'USE OF ENGLISH POINTS SCORE', 'WRITING POINTS','WRITING SCORE', 'PERCENTAGE',
            'LISTENING POINTS','LISTENING SCORE', 'READING POINTS','READING SCORE','USE OF ENGLISH POINTS', 'USE OF ENGLISH POINTS SCORE', 'WRITING POINTS','WRITING SCORE', 'PERCENTAGE',
            'LISTENING POINTS','LISTENING SCORE', 'READING POINTS','READING SCORE','USE OF ENGLISH POINTS', 'USE OF ENGLISH POINTS SCORE', 'WRITING POINTS','WRITING SCORE', 'PERCENTAGE',
            'LISTENING POINTS','LISTENING SCORE', 'READING POINTS','READING SCORE','USE OF ENGLISH POINTS', 'USE OF ENGLISH POINTS SCORE', 'WRITING POINTS','WRITING SCORE', 'PERCENTAGE',
            'LISTENING POINTS','LISTENING SCORE', 'READING POINTS','READING SCORE','USE OF ENGLISH POINTS', 'USE OF ENGLISH POINTS SCORE', 'WRITING POINTS','WRITING SCORE', 'PERCENTAGE',
            'LISTENING POINTS','LISTENING SCORE', 'READING POINTS','READING SCORE','USE OF ENGLISH POINTS', 'USE OF ENGLISH POINTS SCORE', 'WRITING POINTS','WRITING SCORE', 'PERCENTAGE',
            'LISTENING POINTS','LISTENING SCORE', 'READING POINTS','READING SCORE','USE OF ENGLISH POINTS', 'USE OF ENGLISH POINTS SCORE', 'PERCENTAGE',
            'LISTENING POINTS','LISTENING SCORE', 'READING POINTS','READING SCORE','USE OF ENGLISH POINTS', 'USE OF ENGLISH POINTS SCORE', 'WRITING POINTS','WRITING SCORE', 'SPEAKING SCORE', 'PERCENTAGE',
            'DIA', 'MOC', 'CER'
        ];
        array_push($data, $encabezado);

        $grupos = Grupo::find()->where(['instituto_id' => $instituto->id, 'status' => 1, 'ciclo_escolar_id' => $ciclo_escolar])->all();
        foreach ($grupos as $grupo) {
            $estudiantes = Alumno::find()->where(['grupo_id'=>$grupo->id, 'status'=>1])->all();
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
                $fechaHelper = false;
                foreach($niveles as $nivel){
                    $calificacionUse = 0;
                    $calificacionRea = 0;
                    $calificacionLis = 0;
                    $calificacionWri = 0;
                    $tipoDiagnostic = TipoExamen::find()->where('clave="DIA"')->one();
                    $nivelObj = NivelAlumno::find()->where(['clave'=>$nivel])->one();
                    $alumnoExamenRealizado = AlumnoExamen::find()
                    ->joinWith('examen')
                    ->where([
                        'nivel_alumno_id'=>$nivelObj,
                        'alumno_id'=>$estudiante->id,
                        'alumno_examen.tipo_examen_id'=>$tipoDiagnostic->id
                        ])
                    ->one();

                    if(!$fechaHelper){
                        if(isset($alumnoExamenRealizado->fecha_realizacion)){
                            array_push($datos, date('d-m-Y',$alumnoExamenRealizado->fecha_realizacion));
                            $fechaHelper = true;
                        }else{
                            array_push($datos, 'N/A');
                            $fechaHelper = true;
                        }
                    }

                    if(!isset($alumnoExamenRealizado)){
                        array_push($datos,
                            'N/A',
                            'N/A',
                            'N/A',
                            'N/A',
                            'N/A',
                            'N/A',
                            'N/A',
                            'N/A',
                            'N/A'
                        );
                    } else {
                        $examen = $alumnoExamenRealizado->examen;
                        $calificaciones = $alumnoExamenRealizado->calificaciones;
                        if($calificaciones){
                            $calificacionUse = $calificaciones->promedio_use;
                            $calificacionRea = $calificaciones->promedio_reading;
                            $calificacionLis = $calificaciones->promedio_listening;
                            $calificacionWri = $calificaciones->promedio_writing;
                            $promedio = $calificaciones->promedio;
                            if($calificacionWri !== null){
                                if($calificaciones->promedio_importado == null){
                                    $fecha = date('d-m-Y', $alumnoExamenRealizado->fecha_realizacion);

                                    array_push(
                                        $datos,
                                        (($calificaciones->calificacionListening != 0) ? $calificaciones->calificacionListening : '0'),
                                        (($calificacionLis != 0) ? $calificacionLis / 100 : '0'),
                                        (($calificaciones->calificacionReading != 0) ? $calificaciones->calificacionReading : '0'),
                                        (($calificacionRea != 0) ? $calificacionRea / 100 : '0'),
                                        (($calificaciones->calificacionUse != 0) ? $calificaciones->calificacionUse : '0'),
                                        (($calificacionUse != 0) ? $calificacionUse / 100 : '0'),
                                        (($calificaciones->calificacionWriting != 0) ? $calificaciones->calificacionWriting : '0'),
                                        (($calificacionWri != 0) ? (int) $calificacionWri / 100 : '0'),
                                        (($promedio != 0) ? $promedio / 100 : '0')
                                    );
                                }else{
                                    array_push(
                                        $datos,
                                        'N/A',
                                        $calificaciones->calificacionListening ? $calificaciones->calificacionListening/100 : '0',
                                        'N/A',
                                        $calificaciones->calificacionReading ? $calificaciones->calificacionReading/100 : '0',
                                        'N/A',
                                        $calificaciones->calificacionUse ? $calificaciones->calificacionUse/100 : '0',
                                        'N/A',
                                        $calificaciones->calificacionWriting ? $calificaciones->calificacionWriting/100 : '0',
                                        $calificaciones->promedio_importado ? $calificaciones->promedio_importado/100 : '0'
                                    );
                                }
                            } else {
                                if($alumnoExamenRealizado->fecha_realizacion){
                                    $fecha = date('d-m-Y', $alumnoExamenRealizado->fecha_realizacion);
                                } else{
                                    $fecha = 'N/A';
                                }

                                array_push(
                                    $datos,
                                    (($calificaciones->calificacionListening != 0) ? $calificaciones->calificacionListening : '0'),
                                    (($calificacionLis != 0) ? $calificacionLis / 100 : '0'),
                                    (($calificaciones->calificacionReading != 0) ? $calificaciones->calificacionReading : '0'),
                                    (($calificacionRea != 0) ? $calificacionRea / 100 : '0'),
                                    (($calificaciones->calificacionUse != 0) ? $calificaciones->calificacionUse : '0'),
                                    (($calificacionUse != 0) ? $calificacionUse / 100 : '0'),
                                    'N/A',
                                    'N/A',
                                    (($promedio != 0) ? $promedio / 100 : '0')
                                );
                            }
                        } else{
                            array_push(
                                $datos,
                                'N/A',
                                'N/A',
                                'N/A',
                                'N/A',
                                'N/A',
                                'N/A',
                                'N/A',
                                'N/A',
                                'N/A'
                            );
                        }
                    }
                }
                $examenMock = AlumnoExamen::find()
                    ->join('INNER JOIN', 'tipo_examen', 'tipo_examen.id = alumno_examen.tipo_examen_id')
                    ->where('alumno_examen.alumno_id = '.$estudiante->id.' AND tipo_examen.clave="MOC"')
                    ->one();
                if(!$examenMock){
                    array_push($datos, 'N/A','N/A','N/A','N/A','N/A','N/A','N/A');
                }else{
                    $calificaciones = $examenMock->calificaciones;
                    if(!$calificaciones){
                        array_push($datos, 'N/A','N/A','N/A','N/A','N/A','N/A','N/A');
                    }else{
                        array_push(
                            $datos,
                            $calificaciones->calificacionListening,
                            $calificaciones->promedio_listening / 100,
                            $calificaciones->calificacionReading,
                            $calificaciones->promedio_reading / 100,
                            $calificaciones->calificacionUse,
                            $calificaciones->promedio_use / 100,
                            $calificaciones->promedio / 100
                        );
                    }
                }

                $examenCertificate = AlumnoExamen::find()
                    ->join('INNER JOIN', 'tipo_examen', 'tipo_examen.id = alumno_examen.tipo_examen_id')
                    ->where('alumno_examen.alumno_id = '.$estudiante->id.' AND tipo_examen.clave="CER"')
                    ->one();

                if(!$examenCertificate){
                    array_push($datos, 'N/A');
                    array_push($datos, 'N/A');
                    array_push($datos, 'N/A');
                    array_push($datos, 'N/A');
                    array_push($datos, 'N/A');
                    array_push($datos, 'N/A');
                    array_push($datos, 'N/A');
                    array_push($datos, 'N/A');
                    array_push($datos, 'N/A');
                    array_push($datos, 'N/A');
                }else{
                    $calificaciones = $examenCertificate->calificaciones;
                    if(isset($calificaciones) && ($calificaciones->calificacionWriting === null && $calificaciones->calificacionSpeaking === null) || $examenCertificate->examen_id === null){
                        array_push($datos, 'N/A');
                        array_push($datos, 'N/A');
                        array_push($datos, 'N/A');
                        array_push($datos, 'N/A');
                        array_push($datos, 'N/A');
                        array_push($datos, 'N/A');
                        array_push($datos, 'N/A');
                        array_push($datos, 'N/A');
                        array_push($datos, isset($calificaciones->calificacionSpeaking) ? $calificaciones->calificacionSpeaking / 100 : 'N/A');
                        array_push($datos, 'N/A');
                    }else{
                        $examen = $examenCertificate->examen;
                        array_push($datos, $calificaciones->calificacionListening);
                        array_push($datos, $calificaciones->promedio_listening / 100);
                        array_push($datos, $calificaciones->calificacionReading);
                        array_push($datos, $calificaciones->promedio_reading / 100);
                        array_push($datos, $calificaciones->calificacionUse);
                        array_push($datos, $calificaciones->promedio_use / 100);
                        array_push($datos, $calificaciones->calificacionWriting);
                        array_push($datos, $calificaciones->promedio_writing / 100);
                        array_push($datos, isset($calificaciones->calificacionSpeaking) ? $calificaciones->calificacionSpeaking / 100 : 'N/A');
                        array_push($datos, $calificaciones->promedio / 100);
                    }
                }

                array_push($datos, $estudiante->nivelAlumno->nombre);
                array_push($datos, $estudiante->nivelMock->nombre ? $estudiante->nivelMock->nombre : 'N/A');
                array_push($datos, isset($estudiante->nivelCertificate->nombre) ? $estudiante->nivelCertificate->nombre : 'N/A');
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
        $spreadsheet->getActiveSheet()->getDefaultColumnDimension()->setWidth(20);

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
        $styleArrayEncabezadoNiveles = [
            'borders' => [
                'outline' => [
                    'borderStyle'=> \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
                'color'=>[
                    'argb'=> 'FFFFFFFF'
                ]
            ],
            'font' => [
                'bold' => true,
                'color' => [
                    'argb' => 'FFFFFFFF'
                ]
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
        ];
        $spreadsheet->getActiveSheet()->getStyle('F7:N7')->applyFromArray($styleArrayEncabezadoNiveles);
        $spreadsheet->getActiveSheet()->getStyle('O7:W7')->applyFromArray($styleArrayEncabezadoNiveles);
        $spreadsheet->getActiveSheet()->getStyle('X7:AF7')->applyFromArray($styleArrayEncabezadoNiveles);
        $spreadsheet->getActiveSheet()->getStyle('AG7:AO7')->applyFromArray($styleArrayEncabezadoNiveles);
        $spreadsheet->getActiveSheet()->getStyle('AP7:AX7')->applyFromArray($styleArrayEncabezadoNiveles);
        $spreadsheet->getActiveSheet()->getStyle('AY7:BG7')->applyFromArray($styleArrayEncabezadoNiveles);
        $spreadsheet->getActiveSheet()->getStyle('BH7:BN7')->applyFromArray($styleArrayEncabezadoNiveles);
        $spreadsheet->getActiveSheet()->getStyle('BO7:BX7')->applyFromArray($styleArrayEncabezadoNiveles);
        $spreadsheet->getActiveSheet()->getStyle('BY7:CA7')->applyFromArray($styleArrayEncabezadoNiveles);
        $spreadsheet->getActiveSheet()->getStyle('F7:N7')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FF28753f');
        $spreadsheet->getActiveSheet()->getStyle('O7:W7')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FF1b6034');
        $spreadsheet->getActiveSheet()->getStyle('X7:AF7')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FF105129');
        $spreadsheet->getActiveSheet()->getStyle('AG7:AO7')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FF193f20');
        $spreadsheet->getActiveSheet()->getStyle('AP7:AX7')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FF173720');
        $spreadsheet->getActiveSheet()->getStyle('AY7:BG7')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FF162a1e');
        $spreadsheet->getActiveSheet()->getStyle('BH7:BN7')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FF28753f');
        $spreadsheet->getActiveSheet()->getStyle('BO7:BX7')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FF28753f');
        $spreadsheet->getActiveSheet()->getStyle('BY7:CA7')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FF1b6034');

        $spreadsheet->getActiveSheet()->getStyle('E4:E5')->applyFromArray($styleArrayTitle);
        $spreadsheet->getActiveSheet()->getRowDimension('4')->setRowHeight(22);
        $spreadsheet->getActiveSheet()->getRowDimension('5')->setRowHeight(22);
        $spreadsheet->getActiveSheet()->getStyle('A8:CA8')->applyFromArray($styleArrayHeader);
        $spreadsheet->getActiveSheet()->getStyle('A9:CA' . strval($renglones + 8))->applyFromArray($styleArrayData);
        $spreadsheet->getActiveSheet()->getStyle('F9:CA' . strval($renglones + 8))->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER);

        $spreadsheet->getActiveSheet()->getStyle('G9:G' . strval($renglones + 8))->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE_00);
        $spreadsheet->getActiveSheet()->getStyle('I9:I' . strval($renglones + 8))->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE_00);
        $spreadsheet->getActiveSheet()->getStyle('K9:K' . strval($renglones + 8))->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE_00);
        $spreadsheet->getActiveSheet()->getStyle('M9:M' . strval($renglones + 8))->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE);
        $spreadsheet->getActiveSheet()->getStyle('N9:N' . strval($renglones + 8))->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE_00);
        $spreadsheet->getActiveSheet()->getStyle('P9:P' . strval($renglones + 8))->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE_00);
        $spreadsheet->getActiveSheet()->getStyle('R9:R' . strval($renglones + 8))->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE_00);
        $spreadsheet->getActiveSheet()->getStyle('T9:T' . strval($renglones + 8))->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE_00);
        $spreadsheet->getActiveSheet()->getStyle('V9:V' . strval($renglones + 8))->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE);
        $spreadsheet->getActiveSheet()->getStyle('W9:W' . strval($renglones + 8))->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE_00);
        $spreadsheet->getActiveSheet()->getStyle('Y9:Y' . strval($renglones + 8))->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE_00);
        $spreadsheet->getActiveSheet()->getStyle('AA9:AA' . strval($renglones + 8))->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE_00);
        $spreadsheet->getActiveSheet()->getStyle('AC9:AC' . strval($renglones + 8))->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE_00);
        $spreadsheet->getActiveSheet()->getStyle('AE9:AE' . strval($renglones + 8))->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE);
        $spreadsheet->getActiveSheet()->getStyle('AF9:AF' . strval($renglones + 8))->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE_00);
        $spreadsheet->getActiveSheet()->getStyle('AH9:AH' . strval($renglones + 8))->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE_00);
        $spreadsheet->getActiveSheet()->getStyle('AJ9:AJ' . strval($renglones + 8))->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE_00);
        $spreadsheet->getActiveSheet()->getStyle('AL9:AL' . strval($renglones + 8))->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE_00);
        $spreadsheet->getActiveSheet()->getStyle('AN9:AN' . strval($renglones + 8))->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE);
        $spreadsheet->getActiveSheet()->getStyle('AO9:AO' . strval($renglones + 8))->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE_00);
        $spreadsheet->getActiveSheet()->getStyle('AQ9:AQ' . strval($renglones + 8))->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE_00);
        $spreadsheet->getActiveSheet()->getStyle('AS9:AS' . strval($renglones + 8))->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE_00);
        $spreadsheet->getActiveSheet()->getStyle('AU9:AU' . strval($renglones + 8))->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE_00);
        $spreadsheet->getActiveSheet()->getStyle('AW9:AW' . strval($renglones + 8))->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE);
        $spreadsheet->getActiveSheet()->getStyle('AX9:AX' . strval($renglones + 8))->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE_00);
        $spreadsheet->getActiveSheet()->getStyle('AZ9:AZ' . strval($renglones + 8))->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE_00);
        $spreadsheet->getActiveSheet()->getStyle('BB9:BB' . strval($renglones + 8))->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE_00);
        $spreadsheet->getActiveSheet()->getStyle('BD9:BD' . strval($renglones + 8))->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE_00);
        $spreadsheet->getActiveSheet()->getStyle('BF9:BF' . strval($renglones + 8))->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE);
        $spreadsheet->getActiveSheet()->getStyle('BG9:BG' . strval($renglones + 8))->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE_00);
        $spreadsheet->getActiveSheet()->getStyle('BI9:BI' . strval($renglones + 8))->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE_00);
        $spreadsheet->getActiveSheet()->getStyle('BK9:BK' . strval($renglones + 8))->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE_00);
        $spreadsheet->getActiveSheet()->getStyle('BM9:BM' . strval($renglones + 8))->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE_00);
        $spreadsheet->getActiveSheet()->getStyle('BN9:BN' . strval($renglones + 8))->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE_00);
        $spreadsheet->getActiveSheet()->getStyle('BP9:BP' . strval($renglones + 8))->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE_00);
        $spreadsheet->getActiveSheet()->getStyle('BR9:BR' . strval($renglones + 8))->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE_00);
        $spreadsheet->getActiveSheet()->getStyle('BT9:BT' . strval($renglones + 8))->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE_00);
        $spreadsheet->getActiveSheet()->getStyle('BV9:BV' . strval($renglones + 8))->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE_00);
        $spreadsheet->getActiveSheet()->getStyle('BW9:BW' . strval($renglones + 8))->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE_00);
        $spreadsheet->getActiveSheet()->getStyle('BX9:BX' . strval($renglones + 8))->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE_00);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Institute.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        setCookie("downloadStarted", 1, time() + 20, '/', "", false, false);
        $writer->save('php://output');
        exit(0);
    }

    public function actionSubpais()
    {
        $out = [];
        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];
            if ($parents != null) {
                $pais_id = $parents[0];
                $estados = Estado::find()->where(['pais_id'=>$pais_id])->all();
                foreach($estados as $estado){
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

    public function actionGetInstitutesSearchbar($ciclo_escolar, $q){
        $data = [];
        $institutos = Instituto::find()
            ->leftJoin('grupo', 'instituto.id = grupo.instituto_id')
            ->where([
                'instituto.status' => 1,
                'borrado' => 0,
//                'grupo.status' => 1,
                'grupo.ciclo_escolar_id' => $ciclo_escolar
            ])
            ->andFilterWhere(['like','nombre', $q])
            ->groupBy('instituto.id')
            ->limit(15)
            ->all();
        if($institutos){
            foreach($institutos as $instituto){
                $subdata = [];
                $subdata['nombre'] = $instituto->nombre;
                $subdata['id'] = $instituto->id;
                $subdata['tipo'] = 'institute';
                array_push($data, $subdata);
            }
            return Json::encode($data);
        }
    }

    public function actionGetStudentsSearchbar($ciclo_escolar, $q){
        $data = [];
        $alumnos = Alumno::find()
            ->join('INNER JOIN', 'user', 'user.alumno_id = alumno.id')
            ->leftJoin('grupo', 'grupo.id = alumno.grupo_id')
            ->leftJoin('instituto', 'instituto.id = grupo.instituto_id')
            ->where([
                'alumno.status' => 1,
                'grupo.ciclo_escolar_id' => $ciclo_escolar,
                'grupo.status' => 1,
                'instituto.status' => 1,
                'instituto.borrado' => 0
            ])
            ->andFilterWhere([
                'or',
                ['like','CONCAT(TRIM(alumno.nombre)," ",TRIM(alumno.apellidos))', $q],
                ['like','user.codigo', $q]
                ])
            ->limit(15)
            ->all();
        if($alumnos){
            foreach($alumnos as $alumno){
                $subdata = [];
                $subdata['nombre'] = $alumno->fullNameInstitute;
                $subdata['id'] = $alumno->id;
                $subdata['tipo'] = 'student';
                array_push($data, $subdata);
            }
            return Json::encode($data);
        }
    }

    public function actionEditStudent(){
        $form = new StudentForm();
        if ($form->load(Yii::$app->request->post())) {
            if ($form->updateData()) {
                Yii::$app->session->setFlash('success', "Data has been updated.");
                return $this->redirect(['institutes/alumno', 'id' => $form->id]);
            }
        }
        Yii::$app->session->setFlash('error', "Data can't be updated.");
        return $this->redirect(['institutes/alumno', 'id' => $form->id]);
    }

    // public function actionInstitutesExport(){
    //     ini_set('memory_limit', '-1');
    //     ini_set('max_execution_time', 0);
    //     ini_set('max_input_time', 0);
    //     $spreadsheet = new Spreadsheet();
    //     $spreadsheet->getProperties()->setCreator('Oxford TCC')
    //         ->setLastModifiedBy('Oxford TCC')
    //         ->setKeywords('office 2007 openxml php');
    //     $spreadsheet->getActiveSheet()->setTitle('Institutes');
    //
    //     $drawing = new Drawing();
    //     $drawing->setName('Logo');
    //     $drawing->setDescription('Logo');
    //     $drawing->setPath(realpath('./images/logoColor.png'));
    //     $drawing->setHeight(100);
    //     $drawing->setWorksheet($spreadsheet->getActiveSheet());
    //
    //     $datos_tabla = array();
    //     $encabezado = [
    //         'ESCUELA', 'PROGRAMA', 'NOMBRE DE CONTACTO', 'EMAIL', 'TELEFONO', 'PAIS', 'ESTADO', 'CIUDAD', 'NUMERO TOTAL DE ALUMNOS', 'FECHA DE DIAGNOSTICO', 'FECHA ENTREGA DE RESULTADOS DIAG', 'ALUMNOS POR TERMINAR DIAG', 'FECHA DE MOCK', 'FECHA ENTREGA DE RESULTADOS MOCK', 'ALUMNOS POR TERMINAR MOCK', 'FECHA DE CERTIFICATE', 'FECHA ENTREGA DE RESULTADOS CERTIFICATE', 'ALUMNOS POR TERMINAR CERTIFICATE','STATUS PROCESO', 'STATUS PARTICULAR',
    //     ];
    //     array_push($datos_tabla, $encabezado);
    //
    //     $diagnosticType = TipoExamen::find()->where(['clave'=>'DIA'])->one();
    //     $mockType = TipoExamen::find()->where(['clave'=>'MOC'])->one();
    //     $certificateType = TipoExamen::find()->where(['clave'=>'CER'])->one();
    //
    //     $institutos = Instituto::find()->where(['borrado' => 0, 'status' => 1])->all();
    //     $institutos = new Instituto();
    //     $institutos = $institutos->datosReporte();
    //     foreach($institutos as $instituto){
    //         $datos_renglon = [];
    //         $numero_alumnos = 0;
    //         $fecha_examen_diag = 0;
    //         $alumnos_pen_diag = 0;
    //         $fecha_examen_mock = 0;
    //         $alumnos_pen_mock = 0;
    //         $fecha_examen_cert = 0;
    //         $alumnos_pen_cert = 0;
    //         array_push($datos_renglon,
    //             trim($instituto->nombre),
    //             ($instituto->nombre_programa ? $instituto->nombre_programa : 'N/A'),
    //             $instituto->nombre_profesor,
    //             $instituto->email,
    //             $instituto->telefono,
    //             trim($instituto->pais),
    //             $instituto->estado,
    //             $instituto->ciudad
    //         );
    //         $grupos_activos = $instituto->gruposActivos();
    //         foreach($grupos_activos as $grupo){
    //             $alumos_activos = $grupo->alumnosActivos();
    //             $numero_alumnos = $numero_alumnos + count($alumos_activos);
    //             foreach($alumos_activos as $alumno){
    //                 $ultimo_examen_dia = AlumnoExamen::find()
    //                     ->select(['alumno_examen.fecha_realizacion', 'calificaciones.calificacionWriting AS calificacionWriting'])
    //                     ->joinWith('calificaciones')
    //                     ->where(['and',
    //                     ['=','alumno_id', $alumno->id],
    //                     ['=','tipo_examen_id', $diagnosticType->id],
    //                     ['is not','fecha_realizacion', null],
    //                     ['is not','calificaciones.calificacionWriting', null]
    //                     ])
    //                     ->orderBy(['fecha_realizacion' => SORT_DESC])
    //                     ->limit(1)
    //                     ->one();
    //                 if($ultimo_examen_dia){
    //                     if($ultimo_examen_dia->calificacionWriting && $ultimo_examen_dia->fecha_realizacion > $fecha_examen_diag){
    //                         $fecha_examen_diag = $ultimo_examen_dia->fecha_realizacion;
    //                     }
    //                 } else {
    //                     $alumnos_pen_diag++;
    //                 }
    //
    //                 $ultimo_examen_mock = AlumnoExamen::find()
    //                     ->where(['and',
    //                     ['=','alumno_id', $alumno->id],
    //                     ['=','tipo_examen_id', $mockType->id],
    //                     ['is not','fecha_realizacion', null]
    //                     ])
    //                     ->orderBy(['fecha_realizacion' => SORT_DESC])
    //                     ->limit(1)
    //                     ->one();
    //                 if($ultimo_examen_mock){
    //                     if($ultimo_examen_mock->fecha_realizacion > $fecha_examen_mock){
    //                         $fecha_examen_mock = $ultimo_examen_mock->fecha_realizacion;
    //                     }
    //                 } else {
    //                     $alumnos_pen_mock++;
    //                 }
    //
    //                 $ultimo_examen_cert = AlumnoExamen::find()
    //                     ->select(['alumno_examen.fecha_realizacion', 'calificaciones.calificacionWriting AS calificacionWriting'])
    //                     ->joinWith('calificaciones')
    //                     ->where(['and',
    //                     ['=','alumno_id', $alumno->id],
    //                     ['=','tipo_examen_id', $certificateType->id],
    //                     ['is not','fecha_realizacion', null],
    //                     ])
    //                     ->orderBy(['fecha_realizacion' => SORT_DESC])
    //                     ->limit(1)
    //                     ->one();
    //                 if($ultimo_examen_cert){
    //                     if($ultimo_examen_cert->calificacionWriting !== null && $ultimo_examen_cert->fecha_realizacion > $fecha_examen_cert){
    //                         $fecha_examen_cert = $ultimo_examen_cert->fecha_realizacion;
    //                     }
    //                 } else {
    //                     $alumnos_pen_cert++;
    //                 }
    //             }
    //         }
    //
    //         array_push($datos_renglon,
    //             $numero_alumnos,
    //             ($fecha_examen_diag > 0 ? date('d-M-Y',$fecha_examen_diag) : 'N/A'),
    //             $instituto->finalizacion_diagnostic ? date('d-M-Y',$instituto->finalizacion_diagnostic) : 'N/A',
    //             $alumnos_pen_diag,
    //             ($fecha_examen_mock > 0 ? date('d-M-Y',$fecha_examen_mock) : 'N/A'),
    //             $instituto->finalizacion_mock ? date('d-M-Y',$instituto->finalizacion_mock) : 'N/A',
    //             $alumnos_pen_mock,
    //             ($fecha_examen_cert > 0 ? date('d-M-Y',$fecha_examen_cert) : 'N/A'),
    //             'N/A',
    //             $alumnos_pen_cert,
    //             '',
    //             '',
    //             ''
    //         );
    //
    //         array_push($datos_tabla, $datos_renglon);
    //     }
    //
    //     $renglones = count($datos_tabla) - 1;
    //
    //     $spreadsheet->getActiveSheet()
    //         ->fromArray(
    //             $datos_tabla,
    //             null,
    //             'A8',
    //             true
    //         );
    //
    //     $spreadsheet->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
    //     $spreadsheet->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
    //     $spreadsheet->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
    //     $spreadsheet->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
    //     $spreadsheet->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
    //     $spreadsheet->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
    //     $spreadsheet->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
    //     $spreadsheet->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
    //     $spreadsheet->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
    //     $spreadsheet->getActiveSheet()->getColumnDimension('J')->setWidth(20);
    //     $spreadsheet->getActiveSheet()->getColumnDimension('K')->setWidth(30);
    //     $spreadsheet->getActiveSheet()->getColumnDimension('L')->setWidth(30);
    //     $spreadsheet->getActiveSheet()->getColumnDimension('M')->setWidth(30);
    //     $spreadsheet->getActiveSheet()->getColumnDimension('N')->setWidth(30);
    //     $spreadsheet->getActiveSheet()->getColumnDimension('O')->setWidth(30);
    //     $spreadsheet->getActiveSheet()->getColumnDimension('P')->setWidth(30);
    //     $spreadsheet->getActiveSheet()->getColumnDimension('Q')->setWidth(30);
    //     $spreadsheet->getActiveSheet()->getColumnDimension('R')->setWidth(30);
    //     $spreadsheet->getActiveSheet()->getColumnDimension('S')->setWidth(30);
    //     $spreadsheet->getActiveSheet()->getColumnDimension('T')->setWidth(30);
    //
    //     $styleArrayHeader = [
    //         'font' => [
    //             'bold' => true,
    //             'color' => [
    //                 'argb' => 'FFFFFFFF'
    //             ],
    //         ],
    //         'alignment' => [
    //             'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
    //         ],
    //         'borders' => [
    //             'top' => [
    //                 'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
    //             ],
    //         ],
    //         'fill' => [
    //             'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
    //             'startColor' => [
    //                 'argb' => 'FF0F4F2C',
    //             ],
    //         ],
    //     ];
    //     $styleArrayData = [
    //         'borders' => [
    //             'allBorders' => [
    //                 'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
    //             ],
    //         ],
    //         'alignment' => [
    //             'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
    //         ],
    //     ];
    //
    //     $spreadsheet->getActiveSheet()->getStyle('A8:T8')->applyFromArray($styleArrayHeader);
    //     $spreadsheet->getActiveSheet()->getStyle('A9:T'.strval($renglones+8))->applyFromArray($styleArrayData);
    //
    //     header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    //     header('Content-Disposition: attachment;filename="Institutes.xlsx"');
    //     header('Cache-Control: max-age=0');
    //
    //     $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
    //     setCookie("downloadStarted", 1, time() + 20, '/', "", false, false);
    //     $writer->save('php://output');
    //     exit(0);
    // }

    public function actionInstitutesExport($ciclo_escolar = null){
        if (!isset($cisclo_escolar)) {
            $currentPeriod = CicloEscolar::find()
                ->where(['status' => 1])
                ->one();
            $ciclo_escolar = $currentPeriod->id;
        }
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()->setCreator('Oxford TCC')
            ->setLastModifiedBy('Oxford TCC')
            ->setKeywords('office 2007 openxml php');
        $spreadsheet->getActiveSheet()->setTitle('Institutes');

        $drawing = new Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('Logo');
        $drawing->setPath(realpath('./images/logoColor.png'));
        $drawing->setHeight(100);
        $drawing->setWorksheet($spreadsheet->getActiveSheet());

        $datos_tabla = array();
        $encabezado = [
            'ESCUELA', 'PROGRAMA', 'NOMBRE DE CONTACTO', 'EMAIL', 'TELEFONO', 'PAIS', 'ESTADO', 'CIUDAD', 'NUMERO TOTAL DE ALUMNOS', 'FECHA DE DIAGNOSTICO', 'FECHA ENTREGA DE RESULTADOS DIAG', 'ALUMNOS POR TERMINAR DIAG', 'FECHA DE MOCK', 'FECHA ENTREGA DE RESULTADOS MOCK', 'ALUMNOS POR TERMINAR MOCK', 'FECHA DE CERTIFICATE', 'FECHA ENTREGA DE RESULTADOS CERTIFICATE', 'ALUMNOS POR TERMINAR CERTIFICATE','STATUS PROCESO', 'STATUS PARTICULAR',
        ];
        array_push($datos_tabla, $encabezado);

        $diagnosticType = TipoExamen::find()->where(['clave'=>'DIA'])->one();
        $mockType = TipoExamen::find()->where(['clave'=>'MOC'])->one();
        $certificateType = TipoExamen::find()->where(['clave'=>'CER'])->one();

        $institutos = Instituto::find()->where(['borrado' => 0, 'status' => 1, 'pruebas' => 0])->all();
        $institutos = new Instituto();
        $institutos = $institutos->datosReporte($ciclo_escolar);
        foreach($institutos as $instituto){
            $datos_renglon = [];
            $numero_alumnos = 0;
            $fecha_examen_diag = 0;
            $alumnos_pen_diag = 0;
            $fecha_examen_mock = 0;
            $alumnos_pen_mock = 0;
            $fecha_examen_cert = 0;
            $alumnos_pen_cert = 0;
            $diagnostic_total = 0;
            $diagnostic_done = 0;
            array_push($datos_renglon,
                trim($instituto->nombre),
                ($instituto->nombre_programa ? $instituto->nombre_programa : 'N/A'),
                $instituto->nombre_profesor,
                $instituto->email,
                $instituto->telefono,
                trim($instituto->pais),
                $instituto->estado,
                $instituto->ciudad
            );
            // $grupos_activos = $instituto->gruposActivos();
            $numero_alumnos = Alumno::find()
                ->join('INNER JOIN','grupo', 'grupo.id = alumno.grupo_id')
                ->where([
                    'and',
                    ['=','alumno.status', 1],
                    ['=','grupo.status', 1],
                    ['=','grupo.instituto_id', $instituto->id],
                    ['grupo.ciclo_escolar_id' => $ciclo_escolar],
                ])
                ->count();
            $ultimo_examen_dia = AlumnoExamen::find()
                ->select(['alumno_examen.fecha_realizacion', 'calificaciones.calificacionWriting AS calificacionWriting'])
                ->join('INNER JOIN', 'alumno', 'alumno.id = alumno_examen.alumno_id')
                ->join('INNER JOIN', 'grupo', 'grupo.id = alumno.grupo_id')
                ->join('INNER JOIN', 'calificaciones','calificaciones.id = alumno_examen.calificaciones_id')
                ->where(['and',
                    ['=','alumno.status', 1],
                    ['=','grupo.instituto_id', $instituto->id],
                    ['grupo.ciclo_escolar_id' => $ciclo_escolar],
                    ['=','tipo_examen_id', $diagnosticType->id],
                    ['is not','fecha_realizacion', null],
                    ['is not','calificaciones.calificacionWriting', null]
                ])
                ->orderBy(['fecha_realizacion' => SORT_DESC])
                ->limit(1)
                ->one();
            $diagnostic_total = count(AlumnoExamen::find()
                ->select(['DISTINCT(alumno_examen.alumno_id)'])
                ->join('INNER JOIN', 'alumno', 'alumno.id = alumno_examen.alumno_id')
                ->join('INNER JOIN', 'grupo', 'grupo.id = alumno.grupo_id')
                ->join('LEFT JOIN', 'calificaciones','calificaciones.id = alumno_examen.calificaciones_id')
                ->where(['and',
                    ['=','alumno.status', 1],
                    ['=','grupo.status', 1],
                    ['grupo.ciclo_escolar_id' => $ciclo_escolar],
                    ['=','grupo.instituto_id', $instituto->id],
                    ['=','tipo_examen_id', $diagnosticType->id]
                ])
                ->all());
            $diagnostic_done = count(AlumnoExamen::find()
                ->select(['DISTINCT(alumno_examen.alumno_id)'])
                ->join('INNER JOIN', 'alumno', 'alumno.id = alumno_examen.alumno_id')
                ->join('INNER JOIN', 'grupo', 'grupo.id = alumno.grupo_id')
                ->join('INNER JOIN', 'calificaciones','calificaciones.id = alumno_examen.calificaciones_id')
                ->where(['and',
                    ['=','alumno.status', 1],
                    ['=','grupo.status', 1],
                    ['grupo.ciclo_escolar_id' => $ciclo_escolar],
                    ['=','grupo.instituto_id', $instituto->id],
                    ['=','tipo_examen_id', $diagnosticType->id],
                    ['is not','fecha_realizacion', null],
                    ['is not','calificaciones.calificacionWriting', null]
                ])
                ->all());
            $fecha_examen_diag = $ultimo_examen_dia->fecha_realizacion;
            $alumnos_pen_diag = $numero_alumnos-$diagnostic_done;

            $ultimo_examen_mock = AlumnoExamen::find()
                ->join('INNER JOIN', 'alumno', 'alumno.id = alumno_examen.alumno_id')
                ->join('INNER JOIN', 'grupo', 'grupo.id = alumno.grupo_id')
                ->join('INNER JOIN', 'calificaciones','calificaciones.id = alumno_examen.calificaciones_id')
                ->where(['and',
                    ['=','alumno.status', 1],
                    ['=','grupo.instituto_id', $instituto->id],
                    ['grupo.ciclo_escolar_id' => $ciclo_escolar],
                    ['=','tipo_examen_id', $mockType->id],
                    ['is not','fecha_realizacion', null]
                ])
                ->orderBy(['fecha_realizacion' => SORT_DESC])
                ->limit(1)
                ->one();
            $mock_total = AlumnoExamen::find()
                ->select(['DISTINCT(alumno_examen.alumno_id)'])
                ->join('INNER JOIN', 'alumno', 'alumno.id = alumno_examen.alumno_id')
                ->join('INNER JOIN', 'grupo', 'grupo.id = alumno.grupo_id')
                ->join('LEFT JOIN', 'calificaciones','calificaciones.id = alumno_examen.calificaciones_id')
                ->where(['and',
                    ['=','alumno.status', 1],
                    ['=','grupo.status', 1],
                    ['=','grupo.instituto_id', $instituto->id],
                    ['grupo.ciclo_escolar_id' => $ciclo_escolar],
                    ['=','tipo_examen_id', $mockType->id]
                ])
                ->count();
            $mock_done = AlumnoExamen::find()
                ->select(['DISTINCT(alumno_examen.alumno_id)'])
                ->join('INNER JOIN', 'alumno', 'alumno.id = alumno_examen.alumno_id')
                ->join('INNER JOIN', 'grupo', 'grupo.id = alumno.grupo_id')
                ->join('INNER JOIN', 'calificaciones','calificaciones.id = alumno_examen.calificaciones_id')
                ->where(['and',
                    ['=','alumno.status', 1],
                    ['=','grupo.status', 1],
                    ['=','grupo.instituto_id', $instituto->id],
                    ['grupo.ciclo_escolar_id' => $ciclo_escolar],
                    ['=','tipo_examen_id', $mockType->id],
                    ['is not','fecha_realizacion', null]
                ])
                ->count();
            $fecha_examen_mock = $ultimo_examen_mock->fecha_realizacion;
            $alumnos_pen_mock = $mock_total-$mock_done;

            $ultimo_examen_cert = AlumnoExamen::find()
                ->select(['alumno_examen.fecha_realizacion', 'calificaciones.calificacionWriting AS calificacionWriting'])
                ->join('INNER JOIN', 'alumno', 'alumno.id = alumno_examen.alumno_id')
                ->join('INNER JOIN', 'grupo', 'grupo.id = alumno.grupo_id')
                ->join('INNER JOIN', 'calificaciones','calificaciones.id = alumno_examen.calificaciones_id')
                ->where(['and',
                    ['=','alumno.status', 1],
                    ['=','grupo.instituto_id', $instituto->id],
                    ['grupo.ciclo_escolar_id' => $ciclo_escolar],
                    ['=','tipo_examen_id', $certificateType->id],
                    ['is not','fecha_realizacion', null],
                ])
                ->orderBy(['fecha_realizacion' => SORT_DESC])
                ->limit(1)
                ->one();
            $cert_total = AlumnoExamen::find()
                ->select(['DISTINCT(alumno_examen.alumno_id)'])
                ->join('INNER JOIN', 'alumno', 'alumno.id = alumno_examen.alumno_id')
                ->join('INNER JOIN', 'grupo', 'grupo.id = alumno.grupo_id')
                ->join('LEFT JOIN', 'calificaciones','calificaciones.id = alumno_examen.calificaciones_id')
                ->where(['and',
                    ['=','alumno.status', 1],
                    ['=','grupo.status', 1],
                    ['=','grupo.instituto_id', $instituto->id],
                    ['grupo.ciclo_escolar_id' => $ciclo_escolar],
                    ['=','tipo_examen_id', $certificateType->id]
                ])
                ->count();
            $cert_done = AlumnoExamen::find()
                ->select(['DISTINCT(alumno_examen.alumno_id)'])
                ->join('INNER JOIN', 'alumno', 'alumno.id = alumno_examen.alumno_id')
                ->join('INNER JOIN', 'grupo', 'grupo.id = alumno.grupo_id')
                ->join('INNER JOIN', 'calificaciones','calificaciones.id = alumno_examen.calificaciones_id')
                ->where(['and',
                    ['=','alumno.status', 1],
                    ['=','grupo.status', 1],
                    ['=','grupo.instituto_id', $instituto->id],
                    ['grupo.ciclo_escolar_id' => $ciclo_escolar],
                    ['=','tipo_examen_id', $certificateType->id],
                    ['is not','fecha_realizacion', null],
                ])
                ->count();
            $fecha_examen_cert = $ultimo_examen_cert->fecha_realizacion;
            $alumnos_pen_cert = $cert_total-$cert_done;

            array_push($datos_renglon,
                $numero_alumnos,
                ($fecha_examen_diag > 0 ? date('d-M-Y',$fecha_examen_diag) : 'N/A'),
                $instituto->finalizacion_diagnostic ? date('d-M-Y',$instituto->finalizacion_diagnostic) : 'N/A',
                $alumnos_pen_diag,
                ($fecha_examen_mock > 0 ? date('d-M-Y',$fecha_examen_mock) : 'N/A'),
                $instituto->finalizacion_mock ? date('d-M-Y',$instituto->finalizacion_mock) : 'N/A',
                $alumnos_pen_mock,
                ($fecha_examen_cert > 0 ? date('d-M-Y',$fecha_examen_cert) : 'N/A'),
                'N/A',
                $alumnos_pen_cert,
                '',
                '',
                ''
            );

            array_push($datos_tabla, $datos_renglon);
        }

        $renglones = count($datos_tabla) - 1;

        $spreadsheet->getActiveSheet()
            ->fromArray(
                $datos_tabla,
                null,
                'A8',
                true
            );

        $spreadsheet->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('J')->setWidth(20);
        $spreadsheet->getActiveSheet()->getColumnDimension('K')->setWidth(30);
        $spreadsheet->getActiveSheet()->getColumnDimension('L')->setWidth(30);
        $spreadsheet->getActiveSheet()->getColumnDimension('M')->setWidth(30);
        $spreadsheet->getActiveSheet()->getColumnDimension('N')->setWidth(30);
        $spreadsheet->getActiveSheet()->getColumnDimension('O')->setWidth(30);
        $spreadsheet->getActiveSheet()->getColumnDimension('P')->setWidth(30);
        $spreadsheet->getActiveSheet()->getColumnDimension('Q')->setWidth(30);
        $spreadsheet->getActiveSheet()->getColumnDimension('R')->setWidth(30);
        $spreadsheet->getActiveSheet()->getColumnDimension('S')->setWidth(30);
        $spreadsheet->getActiveSheet()->getColumnDimension('T')->setWidth(30);

        $styleArrayHeader = [
            'font' => [
                'bold' => true,
                'color' => [
                    'argb' => 'FFFFFFFF'
                ],
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

        $spreadsheet->getActiveSheet()->getStyle('A8:T8')->applyFromArray($styleArrayHeader);
        $spreadsheet->getActiveSheet()->getStyle('A9:T'.strval($renglones+8))->applyFromArray($styleArrayData);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Institutes.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        setCookie("downloadStarted", 1, time() + 20, '/', "", false, false);
        $writer->save('php://output');
        exit(0);
    }

    public function actionImportStudents(){
        ini_set('memory_limit', '-1');
        $connection = \Yii::$app->db;
        $transaction = $connection->beginTransaction();
        $inicio = 20;
        $nombre = 1;
        $apellidos = 1;
        $correo = 2;
        $grupo_importar = 4;
        $fecha_realizacion = 5;
        $listening_importar = 46;
        $reading_importar = 47;
        $use_importar = 48;
        $writing_importar = 49;
        $promedio_importar = 52;
        $nivel_importar = 53;
        $nivel_mock = 55;
        $instituto_id = 260;

        $codigo_escuela = 'CMS2018';

        $grupos_guardados = [];
        $niveles = [
            1 => 'A1',
            2 => 'A2',
            3 => 'B1',
            4 => 'B2',
            5 => 'C1',
            6 => 'C2',
            8 => 'N/A',
        ];

        $examenes = [
            'A1' => 8,
            'A2' => 9,
            'B1' => 10,
            'B2' => 11,
            'C1' => 12,
            'C2' => 13,
        ];

        $writings = [
            'A1' => 496,
            'A2' => 502,
            'B1' => 501,
            'B2' => 507,
            'C1' => 492,
            'C2' => 497,
        ];

        require('../XLS-reader/php-excel-reader/excel_reader2.php');
        require('../XLS-reader/SpreadsheetReader.php');
        $registros = new \SpreadsheetReader(\Yii::getalias('@webroot/importacion/calificaciones.xlsx'));
        foreach($registros as $i => $registro){
            if($i >= $inicio){
                /* Obtiene Grupo */
                if(in_array(trim($registro[$grupo_importar]),$grupos_guardados)){
                    $grupo = array_search(trim($registro[$grupo_importar]),$grupos_guardados);
                }else{
                    $grupo = new Grupo();
                    $grupo->nivel_id = 1;
                    $grupo->instituto_id = $instituto_id;
                    $grupo->status = 1;
                    $grupo->grupo = trim($registro[$grupo_importar]);
                    if(!$grupo->save()){
                        $transaction->rollback();
                        var_dump('Error al crear grupo '.$grupo->grupo);exit;
                    }
                    $grupos_guardados[$grupo->id] = $grupo->grupo;
                    $grupo = $grupo->id;
                }
                /* Obtiene Grupo */

                /*Crea Alumno*/
                $nivel_alumno = array_search(trim($registro[$nivel_importar]),$niveles);
                $alumno = new Alumno();
                $alumno->grupo_id = $grupo;
                $alumno->nivel_alumno_id = $nivel_alumno;
                if($nivel_mock && $registro[$nivel_mock]){
                    $nivel_mock_alumno = array_search(trim($registro[$nivel_mock]),$niveles);
                    $alumno->nivel_mock_id = $nivel_mock_alumno;
                }
                if(!mb_detect_encoding(trim($registro[$nombre]),'UTF-8', true)){
                    $alumno->nombre = utf8_encode(trim($registro[$nombre]));
                }else{
                    $alumno->nombre = trim($registro[$nombre]);
                }
                // if(!mb_detect_encoding(trim($registro[$apellidos]),'UTF-8', true)){
                //     $alumno->apellidos = utf8_encode(trim($registro[$apellidos]));
                // }else{
                //     $alumno->apellidos = trim($registro[$apellidos]);
                // }

                $alumno->apellidos = '_';
                $alumno->status = 1;
                $alumno->correo = trim($registro[$correo]);
                if($nivel_mock && trim($registro[$nivel_mock])){
                    $alumno->status_examen_id = 1;
                }
                if(!$alumno->save()){
                    $transaction->rollback();
                    var_Dump($registro[$nivel_mock]);exit;
                    var_dump($alumno->getErrors());exit;
                    var_dump(trim($registro[$nivel_importar]));exit;
                    var_dump($alumno->getErrors());exit;
                    var_dump('Error al crear alumno '.$alumno->nombre.' '.$alumno->apellidos);exit;
                }
                /*Crea Alumno*/

                /*Crea User*/
                $password = $this->randomString(6);
                $codigo = $this->codigoUsuario($codigo_escuela);
                $user = new User();
                $user->username = $this->randomString(10);
                $user->email = trim($registro[$correo]);
                $user->setPassword($password);
                $user->generateAuthKey();
                $user->alumno_id = $alumno->id;
                $user->codigo = $codigo;
                $user->tipo_usuario_id = 4;

                $encrypt_method = "AES-256-CBC";
                $secret_key = \Yii::$app->params['hash'];
                $key = hash('sha256', $secret_key);
                $iv = openssl_random_pseudo_bytes(16);
                $encrypted = openssl_encrypt($password, $encrypt_method, $key, 0, $iv);
                $user->acceso = $encrypted;
                $user->iv = bin2hex($iv);
                if (!$user->save()){
                    $transaction->rollback();
                    return false;
                }
                /*Crea User*/
                /*Crea AlumnoExamen*/
                if(trim($registro[$fecha_realizacion]) && $nivel_alumno != 8){
                    $fecha = new \DateTime(trim($registro[$fecha_realizacion]));
                    $alumno_examen = new AlumnoExamen();
                    $alumno_examen->alumno_id = $alumno->id;
                    $alumno_examen->tipo_examen_id = 1;
                    $alumno_examen->status = 1;
                    $alumno_examen->fecha_realizacion = $fecha->getTimestamp();
                    $alumno_examen->examen_id = $examenes[$registro[$nivel_importar]];
                    if(!$alumno_examen->save()){
                        $transaction->rollback();
                        return false;
                    }
                    //ASIGNAR aluexa_reactivo
                    $respuesta = new AluexaReactivos();
                    $respuesta->alumno_examen_id = $alumno_examen->id;
                    $respuesta->reactivo_id = $writings[$registro[$nivel_importar]];
                    $respuesta->respuestaWriting = "IMPORT";
                    $respuesta->calificado = 1;
                    if(!$respuesta->save()){
                        var_dump($respuesta->getErrors());
                        var_dump($registro[$nivel_importar]);
                        var_Dump($writings[$registro[$nivel_importar]]);Exit;
                        $transaction->rollback();
                        return false;
                    }

                    if($nivel_mock && trim($registro[$nivel_mock])){
                        $alumno_examen_mock = new AlumnoExamen();
                        $alumno_examen_mock->alumno_id = $alumno->id;
                        $alumno_examen_mock->tipo_examen_id = 2;
                        $alumno_examen_mock->status = 1;
                        if(!$alumno_examen_mock->save()){
                            $transaction->rollback();
                            return false;
                        }
                    }
                }
                /*Crea AlumnoExamen*/

                /*Crea Calificaciones*/
                if($nivel_alumno != 8){
                    $listening = floor(trim(preg_replace("/[^0-9.]/", "", $registro[$listening_importar])));
                    $reading = floor(trim(preg_replace("/[^0-9.]/", "", $registro[$reading_importar])));
                    $use = floor(trim(preg_replace("/[^0-9.]/", "", $registro[$use_importar])));
                    $writing = floor(trim(preg_replace("/[^0-9.]/", "", $registro[$writing_importar])));
                    $promedio_importado = floor(trim(preg_replace("/[^0-9.]/", "", $registro[$promedio_importar])));

                    $calificaciones = new Calificaciones();
                    $calificaciones->calificacionUse = $use;
                    $calificaciones->calificacionReading = $reading;
                    $calificaciones->calificacionListening = $listening;
                    $calificaciones->calificacionWriting = $writing;
                    $calificaciones->promedio_importado = $promedio_importado;
                    if(!$calificaciones->save()){
                        $transaction->rollback();
                        return false;
                    }
                    $alumno_examen->calificaciones_id = $calificaciones->id;
                    if(!$alumno_examen->save()){
                        $transaction->rollback();
                        return false;
                    }
                }
                /*Crea Calificaciones*/


            }
        }
        // $transaction->rollback();
        // var_dump($grupos_guardados);exit;
        $transaction->commit();
        exit;
    }

    private function codigoUsuario($codigo_escuela){
        return $codigo_escuela.'-'.$this->randomString(4);
    }

    private function randomString($length){
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $string = '';
        $max = strlen($chars) - 1;
        for ($i = 0; $i < $length; $i++) {
             $string .= $chars[rand(0, $max)];
        }
        return $string;
    }

    public function actionLogoutStudent($id){
        $user = User::find()->where('alumno_id='.$id)->one();
        if($user){
            $user->sesion_info = null;
            if($user->save()){
                Yii::$app->session->setFlash('success', "Student session closed correctly.");
            }else{
                Yii::$app->session->setFlash('error', "There was an error closing the student session.");
            }
        }
        return $this->redirect(['institutes/alumno', 'id' => $id]);
    }

    public function actionExportCertificate($seleccion,$tipo){
        // header("Content-Type: text/html; charset=iso-8859-1 ");
        $certificateType = TipoExamen::find()->where(['clave'=>'CER'])->one();
        $sin_certificate = array();
        $certificados = [];
        if($tipo == 'ALU'){
            $alumnos = explode(',', $seleccion);
            if(!is_array($alumnos) && !count($alumnos)){
                return 0;
            }
            foreach($alumnos as $alumno){
                if($alumno){
                    $alumno = Alumno::findOne($alumno);
                    $datos_certificate = AlumnoExamen::find()
                        ->select([
                            'CONCAT(alumno.nombre," ",alumno.apellidos) AS nombre_alumno',
                            'instituto.nombre AS instituto', 'estado.estadonombre AS estado',
                            'pais.nombre AS pais', 'calificaciones.calificacionUse AS calificacioUse',
                            'calificaciones.calificacionReading AS calificacionReading',
                            'calificaciones.calificacionListening AS calificacionListening',
                            'calificaciones.calificacionWriting AS calificacionWriting',
                            'calificaciones.calificacionSpeaking AS calificacionSpeaking',
                            'nivel_alumno.nombre AS nivel'
                        ])
                        ->join('INNER JOIN', 'alumno', 'alumno.id = alumno_examen.alumno_id')
                        ->join('INNER JOIN', 'nivel_alumno', 'nivel_alumno.id = alumno.nivel_certificate_id')
                        ->join('INNER JOIN', 'grupo', 'grupo.id = alumno.grupo_id')
                        ->join('INNER JOIN', 'instituto', 'instituto.id = grupo.instituto_id')
                        ->join('INNER JOIN', 'direccion', 'direccion.id = instituto.direccion_id')
                        ->join('INNER JOIN', 'estado', 'estado.id = direccion.estado_id')
                        ->join('INNER JOIN', 'pais', 'pais.id = estado.pais_id')
                        ->join('INNER JOIN', 'calificaciones', 'calificaciones.id = alumno_examen.calificaciones_id')
                        ->where('alumno.id = '.$alumno->id.' AND alumno_examen.tipo_examen_id = '.$certificateType->id.' AND calificaciones.calificacionSpeaking IS NOT NULL')
                        ->one();
                    if(!$datos_certificate){
                        $sin_certificate[] = $alumno->nombre.' '.$alumno->apellidos;
                        continue;
                    }
                    $certificados[] = $datos_certificate;
                }
            }
        }else if($tipo == 'GRU'){
            $grupos = explode(',', $seleccion);
            if(!is_array($grupos) && !count($grupos)){
                return 0;
            }
            foreach($grupos as $grupo){
                if($grupo){
                    $grupo = Grupo::findOne($grupo);
                    foreach($grupo->alumnos as $alumno){
                        $datos_certificate = AlumnoExamen::find()
                            ->select([
                                'CONCAT(alumno.nombre," ",alumno.apellidos) AS nombre_alumno',
                                'instituto.nombre AS instituto', 'estado.estadonombre AS estado',
                                'pais.nombre AS pais', 'calificaciones.calificacionUse AS calificacioUse',
                                'calificaciones.calificacionReading AS calificacionReading',
                                'calificaciones.calificacionListening AS calificacionListening',
                                'calificaciones.calificacionWriting AS calificacionWriting',
                                'calificaciones.calificacionSpeaking AS calificacionSpeaking',
                                'nivel_alumno.nombre AS nivel'
                            ])
                            ->join('INNER JOIN', 'alumno', 'alumno.id = alumno_examen.alumno_id')
                            ->join('INNER JOIN', 'nivel_alumno', 'nivel_alumno.id = alumno.nivel_certificate_id')
                            ->join('INNER JOIN', 'grupo', 'grupo.id = alumno.grupo_id')
                            ->join('INNER JOIN', 'instituto', 'instituto.id = grupo.instituto_id')
                            ->join('INNER JOIN', 'direccion', 'direccion.id = instituto.direccion_id')
                            ->join('INNER JOIN', 'estado', 'estado.id = direccion.estado_id')
                            ->join('INNER JOIN', 'pais', 'pais.id = estado.pais_id')
                            ->join('INNER JOIN', 'calificaciones', 'calificaciones.id = alumno_examen.calificaciones_id')
                            ->where('alumno.id = '.$alumno->id.' AND alumno_examen.tipo_examen_id = '.$certificateType->id.' AND calificaciones.calificacionSpeaking IS NOT NULL')
                            ->one();
                        if(!$datos_certificate){
                            $sin_certificate[] = $alumno->nombre.' '.$alumno->apellidos;
                            continue;
                        }
                        $certificados[] = $datos_certificate;
                    }
                }
            }
        }
        $content = html_entity_decode($this->renderPartial('_certificate_pdf',[
            'certificados' => $certificados,
            'total_certificados' => count($certificados)
        ]));
        $pdf = new Pdf([
            'mode' => Pdf::MODE_CORE,
            'format' => Pdf::FORMAT_A4,
            'orientation' => Pdf::ORIENT_PORTRAIT,
            'destination' => Pdf::DEST_BROWSER,
            'content' => $content,
            'cssFile' => Yii::$app->basePath.'/web/css/certificate.css',
            'options' => ['title' => 'Certificate'],
            'methods' => [
            ]
        ]);

        return $pdf->render();
        return 1;
    }

    public function actionMezcla(){
        $connection = \Yii::$app->db;
        $transaction = $connection->beginTransaction();
        $pending = ['11210','11212'];
        $aprobados = ['11161','11195','11205','11215'];
        $alumnos = [
            '11150, 10104', '11151, 10105', '11152, 10106', '11153, 10107', '11154, 10108', '11155, 10109', '11156, 10110', '11157, 10111', '11158, 10112', '11159, 10113', '11160, 10114', '11161, 10115', '11162, 10116', '11163, 10117', '11164, 10118', '11165, 10119', '11166, 10120', '11167, 10121', '11168, 10122', '11169, 10123', '11170, 10124', '11171, 10125', '11172, 10126', '11173, 10127', '11174, 10128', '11175, 10129', '11176, 10130', '11177, 10131', '11178, 10132', '11179, 10133', '11180, 10134', '11181, 10135', '11182, 10136', '11183, 10137', '11184, 10138', '11185, 10139', '11186, 10140', '11187, 10141', '11188, 10142', '11189, 10143', '11190, 10144', '11191, 10145', '11192, 10146', '11193, 10147', '11194, 10148', '11195, 10149', '11196, 10150', '11197, 10151', '11198, 10152', '11199, 10153', '11200, 10154', '11201, 10155', '11202, 10156', '11203, 10157','11204, 10158', '11205, 10159', '11206, 10160', '11207, 10161', '11208, 10162', '11209, 10163', '11210, 10164', '11211, 10165', '11212, 10166', '11213, 10167', '11214, 10168', '11215, 10169', '11216, 10170', '11217, 10171', '11218, 10172', '11219, 10173', '11220, 10174', '11221, 10175', '11222, 10176', '11223, 10177', '11224, 10178', '11225, 10179', '11226, 10180', '11227, 10181', '11228, 10182', '11229, 10183', '11230, 10184', '11231, 10185', '11232, 10186', '11233, 10187', '11234, 10188', '11235, 10189', '11236, 10190','11204, 10158', '11205, 10159', '11206, 10160', '11207, 10161', '11208, 10162', '11209, 10163', '11211, 10165', '11213, 10167', '11214, 10168', '11215, 10169', '11216, 10170', '11217, 10171', '11218, 10172', '11219, 10173', '11220, 10174', '11221, 10175', '11222, 10176', '11223, 10177', '11224, 10178', '11225, 10179', '11226, 10180', '11227, 10181', '11228, 10182', '11229, 10183', '11230, 10184', '11231, 10185', '11232, 10186', '11233, 10187', '11234, 10188', '11235, 10189', '11236, 10190'
        ];
        foreach($alumnos as $ids){
            $ids = explode(',',$ids);
            $diagnostic = trim($ids[0]);
            $mock = trim($ids[1]);
            $alumno = Alumno::findOne($diagnostic);
            $examenes_mock = AlumnoExamen::find()->where('alumno_id='.$mock)->all();
            foreach($examenes_mock as $examen){
                $examen_realizado = Examen::findOne($examen->examen_id);
                $examen->alumno_id = $diagnostic;
                if(!$examen->save()){
                    $transaction->rollback();
                    $alumno = Alumno::findOne($diagnostic);
                    var_dump('Error al actualizar alumno '.$alumno->nombre.' '.$alumno->apellidos);
                    exit;
                }
                $alumno->nivel_inicio_mock_id = $examen_realizado->nivel_alumno_id;
                if(in_array($diagnostic,$pending)){
                    $alumno->status_examen_id = 1;
                }else{
                    $alumno->status_examen_id = 4;
                }
                if(!in_array($diagnostic,$aprobados) && $examen_realizado->nivel_alumno_id > 1){
                    $alumno->nivel_mock_id = $examen_realizado->nivel_alumno_id-1;
                }else{
                    $alumno->nivel_mock_id = $examen_realizado->nivel_alumno_id;
                }
                if(!$alumno->save()){
                    $transaction->rollback();
                    var_dump('Error al actualizar status de alumno '.$alumno->nombre.' '.$alumno->apellidos);
                    exit;
                }
            }
        }
        $transaction->commit();
        var_Dump('EXITO');exit;
    }

    public function actionExportDeliveryFormat($id){
        $instituto = Instituto::findOne($id);
        $direccion = $instituto->direccion;
        $content = html_entity_decode($this->renderPartial('_delivery_pdf',[
            'instituto' => $instituto,
            'direccion' => $direccion
        ]));
        $pdf = new Pdf([
            'mode' => Pdf::MODE_CORE,
            'format' => Pdf::FORMAT_A4,
            'orientation' => Pdf::ORIENT_PORTRAIT,
            'destination' => Pdf::DEST_DOWNLOAD,
            'content' => $content,
            'filename' => 'DeliveryFormat.pdf',
            'cssFile' => Yii::$app->basePath.'/web/css/delivery.css',
            'options' => ['title' => 'Delivery Format'],
            'methods' => [
            ]
        ]);

        return $pdf->render();
    }

    public function actionInactiveInstitutes() {
        $searchModel = new InstitutoSearch();
        $dataProviderInactive = $searchModel->searchInactiveInstitutes();
        $dataProviderCancelled = $searchModel->searchCancelledInstitutes();

        return $this->render('inactive-institutes', [
            'searchModel' => $searchModel,
            'dataProviderInactive' => $dataProviderInactive,
            'dataProviderCancelled' => $dataProviderCancelled
        ]);
    }

    //Action para crear nuevo ciclo escolar
    public function actionCrearCiclo($nombre, $status = 0){
        $connection = \Yii::$app->db;
        $transaction = $connection->beginTransaction();
        $ciclo = new CicloEscolar();
        $ciclo->nombre = $nombre;
        $ciclo->status = $status;
        if(!$ciclo->save()){
            $transaction->rollback();
            var_dump("ERROR AL CREAR CICLO");
        }
        $transaction->commit();
        var_dump("EXITO");exit;
    }
}
