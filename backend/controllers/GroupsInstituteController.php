<?php
namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\UploadedFile;

use backend\models\forms\GrupoForm;
use backend\models\forms\StudentForm;
use backend\models\forms\ImportGrupoForm;
use app\models\search\GrupoInstitutoSearch;
use app\models\search\AlumnoSearch;
use app\models\Grupo;
use app\models\Examen;
use app\models\Calificaciones;
use app\models\Alumno;
use app\models\AlumnoExamen;
use app\models\Nivel;
use app\models\NivelAlumno;
use app\models\TipoExamen;
use app\models\Seccion;
use common\models\User;
use app\models\AluexaReactivos;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\IOFactory;
use app\models\Instituto;

set_time_limit(500);

class GroupsInstituteController extends Controller
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
                        'actions' => [
                            'index',
                            'grupo',
                            'status-alumno',
                            'examen-alumno',
                            'update-multiple',
                            'delete-multiple',
                            'add-group',
                            'add-student',
                            'save-group',
                            'alumno',
                            'save-student',
                            'export-group',
                            'import-group',
                            'edit-info',
                            'level-alumno',
                            'get-calificaciones',
                            'delete-multiple-groups',
                            'edit-group',
                            'export-groups',
                            'edit-student',
                            'contract-accepted'
                        ],
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

        if($rol == 'ACA' || $rol == 'ALU')
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
        $user = User::findOne(Yii::$app->user->getId());
        $searchModel = new GrupoInstitutoSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $diagnosticType = TipoExamen::find()->where(['clave' => 'DIA'])->one();
        $mockType = TipoExamen::find()->where(['clave' => 'MOC'])->one();

        return $this->render('index', [
            'instituto' => $user->instituto,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'mockType' => $mockType,
            'diagnosticType' =>$diagnosticType,
        ]);
    }

    public function actionGrupo($id)
    {
        $fileModel = new ImportGrupoForm();
        if($fileModel->load(Yii::$app->request->post())){
            $fileModel->grupoFile = UploadedFile::getInstance($fileModel, 'grupoFile');
            if ($fileModel->upload()) {
                if(!$importados = $fileModel->import())
                {
                    return;
                }else{
                    $grupoCorreo = Grupo::findOne($fileModel->id);
                    $instituto = Yii::$app->user->identity->instituto;
                    $cc = Yii::$app->params['email-cc'];
                    $mail = Yii::$app->mailer->compose()
                        ->setTo(Yii::$app->params['email-notification'])
                        ->setFrom(["equipo@blackrobot.mx" => "Oxford TCC"])
                        ->setSubject($instituto->nombre." registered some students")
                        ->setHtmlBody($this->renderPartial('_correo-admin', [
                            'instituto' => $instituto,
                            'grupo' => $grupoCorreo
                        ]
                    ));
                    if($cc){
                        $mail->setCc(explode(',',$cc));
                    }
                    $mail->send();

                    // $session->set('importado', true);
                    return $this->refresh();
                }
            }
        }
        $grupo = Grupo::findOne($id);
        $searchModel = new AlumnoSearch();
        $searchModel->load(Yii::$app->request->queryParams);
        $dataProvider = $searchModel->search($id);
        $examenes = ArrayHelper::map(Examen::find()->all(),'id', 'examenNameLevel');
        $niveles = ArrayHelper::map(NivelAlumno::find()->all(),'id', 'nombre');
        $diagnosticType = TipoExamen::find()->where(['clave' => 'DIA'])->one();
        $mockType = TipoExamen::find()->where(['clave' => 'MOC'])->one();

        $grupoForm = new GrupoForm();
        $grupoForm->nombre = $grupo->grupo;
        $grupoForm->nivel = $grupo->nivel_id;
        $nivelesGrupo = ArrayHelper::map(Nivel::find()->all(), 'id', 'nombre');

        // $importado = false;
        // if ($session->has('importado')){
        //     $importado = true;
        //     $session->remove('importado');
        // }

        return $this->render('grupo',[
            // 'importado'=>$importado,
            'grupo'=>$grupo,
            'dataProvider'=>$dataProvider,
            'filtro' => $searchModel,
            'examenes'=>$examenes,
            'niveles'=>$niveles,
            'fileModel'=>$fileModel,
            'grupoForm'=>$grupoForm,
            'nivelesGrupo'=>$nivelesGrupo,
            'mockType' => $mockType,
            'diagnosticType' => $diagnosticType,
            ]);
    }

    public function actionStatusAlumno()
    {
        $alumno = Alumno::find()->where('alumno.id='.Yii::$app->request->post('id'))->one();
        $accion = $alumno -> actualizaStatus(Yii::$app->request->post('status'));
        return $accion;
    }

    public function actionExamenAlumno()
    {
        $alumno = Alumno::find()->where('alumno.id='.Yii::$app->request->post('id'))->one();
        $accion = $alumno -> actualizaExamen(Yii::$app->request->post('examen'));
        return $accion;
    }

    public function actionLevelAlumno()
    {
        $alumno = Alumno::find()->where('alumno.id='.Yii::$app->request->post('id'))->one();
        $accion = $alumno -> actualizaNivel(Yii::$app->request->post('level'));
        return $accion;
    }

    public function actionUpdateMultiple()
    {
        $selection = (array)Yii::$app->request->post('selection');
        $id = Yii::$app->request->post('grupo-id');
        $examen = Yii::$app->request->post('examen');
        $status = Yii::$app->request->post('status');
        $nivel = Yii::$app->request->post('nivel');
        if($examen == '' && $status == '' && $nivel == '')
        {
            return $this->redirect(['groups-institute/grupo?id='.$id]);
        }
        if(empty($selection)){
            return $this->redirect(['groups-institute/grupo?id=' . $id]);
        }

        if($status)
        {
            foreach($selection as $id_select) {
                $alumno = Alumno::find()->where('alumno.id='.$id_select)->one();
                $alumno->status = $status == 'ACT' ? 1 : 0;
                $alumno->save();
            }
        }
        if($nivel){
            foreach ($selection as $id_select) {
                $alumno = Alumno::find()->where('alumno.id=' . $id_select)->one();
                $alumno->nivel_alumno_id = $nivel;
                $alumno->save();
            }
        }
        if($examen)
        {
            $modificado = false;
            foreach($selection as $id_select) {
                $alumno = Alumno::find()->where('alumno.id='.$id_select)->one();
                $examen_esp = Examen::find()->where('examen.clave="'.$examen.'"')->one();
                $examenes_alumno = $alumno->alumnoExamens;
                foreach($examenes_alumno as $examen_alumno)
                {
                    $examen_alumno->status = 0;
                    if($examen_alumno->examen_id == $examen_esp->id)
                    {
                        $examen_alumno->status = 1;
                        $modificado = true;
                    }
                    $examen_alumno->save();
                }
                if(!$modificado)
                {
                        $junction = new AlumnoExamen();
                        $junction->alumno_id = $alumno->id;
                        $junction->examen_id = $examen_esp->id;
                        $junction->status = 1;
                        $junction->save();
                }
            }
        }
        // var_dump("hola");exit;
        return $this->redirect(['groups-institute/grupo?id='.$id]);
    }

    public function actionAddGroup($id){
        $grupoForm = new GrupoForm();
        // $paises = ArrayHelper::map(Paises::find()
        //      ->all(),'codigo_pais', 'nombre_pais');

        $niveles = ArrayHelper::map(Nivel::find()->all(), 'clave', 'nombre');

        return $this->renderAjax('_add-group', [
            'grupoForm' => $grupoForm,
            'niveles'=>$niveles,
            'id'=>$id,
        ]);
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

    public function actionSaveGroup(){
        $grupoForm = new GrupoForm();

        if ($grupoForm->load(Yii::$app->request->post())) {
                $guardar = $grupoForm->guardar();
                return $guardar;
            }
    }

    public function actionSaveStudent(){
        $studentForm = new StudentForm();

        if ($studentForm->load(Yii::$app->request->post())) {
            $guardar = $studentForm->guardar();
            if($guardar){
                $grupoCorreo = Grupo::findOne($studentForm->id);
                $instituto = Yii::$app->user->identity->instituto;
                $cc = Yii::$app->params['email-cc'];
                $mail = Yii::$app->mailer->compose()
                    ->setTo(Yii::$app->params['email-notification'])
                    ->setFrom(["equipo@blackrobot.mx" => "Oxford TCC"])
                    ->setSubject($instituto->nombre." registered a student")
                    ->setHtmlBody($this->renderPartial('_correo-admin', [
                        'instituto' => $instituto,
                        'grupo' => $grupoCorreo
                    ]));
                if($cc){
                    $mail->setCc(explode(',',$cc));
                }
                $mail->send();
            }
            return $guardar;
        }
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

    public function actionEditStudent(){
        $form = new StudentForm();
        if ($form->load(Yii::$app->request->post())) {
            if ($form->updateData()) {
                Yii::$app->session->setFlash('success', "Student has been updated.");
                return $this->redirect(['groups-institute/alumno', 'id' => $form->id]);
            }
        }
        Yii::$app->session->setFlash('error', "Student can't be updated.");
        return $this->redirect(['groups-institute/alumno', 'id' => $form->id]);
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


    public function actionExportGroup($id){
        $estudiantes = Alumno::find()
            ->where(["alumno.grupo_id" => $id, 'status' => 1])
            ->all();
        $grupo = Grupo::findOne($id);
        $diagnosticType = TipoExamen::find()->where(['clave' => 'DIA'])->one();
        $mockType = TipoExamen::find()->where(['clave' => 'MOC'])->one();

        // nueva hoja de calculo
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()->setCreator('Maarten Balliauw')
            ->setLastModifiedBy('Maarten Balliauw')
            ->setTitle('Office 2007 XLSX Test Document')
            ->setSubject('Office 2007 XLSX Test Document')
            ->setDescription('Test document for Office 2007 XLSX, generated using PHP classes.')
            ->setKeywords('office 2007 openxml php')
            ->setCategory('Test result file');
        $spreadsheet->getActiveSheet()->setTitle('Alumnos');

        //mostrando imagen de logo
        $drawing = new Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('Logo');
        $drawing->setPath(realpath('./images/logoColor.png'));
        $drawing->setHeight(100);
        $drawing->setWorksheet($spreadsheet->getActiveSheet());

        // cargando datos al arreglo
        $data = array();
        if ($type == $diagnosticType->id) {
            $encabezado = ['NAME', 'GRADE', 'CODE', 'PASSWORD', 'DATE', 'TEST', 'LISTENING', 'READING', 'USE OF ENGLISH', 'WRITING', 'PERCENTAGE', 'FINAL LEVEL'];
            array_push($data, $encabezado);
            foreach ($estudiantes as $estudiante) {
                $user = $estudiante->users[0];
                if ($user->acceso) {
                    $datos = [
                        $estudiante->fullName,
                        $estudiante->grupo->grupo,
                        $user->codigo,
                        $user->accesoDec . ' ',
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
                                    $calificacionWri = ($calificaciones->calificacionWriting * 100) / $seccion->puntos_seccion;
                                    $puntos_total = $puntos_total + $seccion->puntos_seccion;
                                } else
                                    $calificacionWri = 0;
                                break;
                        }
                    }

                    $promedio = ($calificacionUse + $calificacionRea + $calificacionLis + $calificacionWri) / 4;
                    if ($alumnoExamenFin->fecha_realizacion) {
                        $fecha = date('d-m-Y', $alumnoExamenFin->fecha_realizacion);
                    } else {
                        $fecha = 'N/A';
                    }

                    array_push(
                        $datos,
                        $fecha,
                        $alumnoExamenFin->examen->tipoExamen->nombre,
                        round($calificacionLis, 2) . '%',
                        round($calificacionRea, 2) . '%',
                        round($calificacionUse, 2) . '%',
                        (int)$calificacionWri . '%',
                        round($promedio, 2) . '%',
                        $estudiante->nivelAlumno->nombre
                    );
                } else {
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
        } else if ($type == $mockType->id) {
            $encabezado = ['NAME', 'GRADE', 'CODE', 'PASSWORD', 'DATE', 'TEST', 'LISTENING', 'READING', 'USE OF ENGLISH', 'PERCENTAGE', 'FINAL LEVEL'];
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
                        'examen.nivel_alumno_id' => $alumno->nivelAlumno->id
                    ])
                    ->one();
                if (!$examenMock) {
                    $nivelHelper = '';
                    if ($alumno->nivelAlumno->nombre == 'A1') {
                        $nivelHelper = NivelAlumno::find()->where('nombre="A2"')->one();
                    } else if ($alumno->nivelAlumno->nombre == 'A2') {
                        $nivelHelper = NivelAlumno::find()->where('nombre="B1"')->one();
                    } else if ($alumno->nivelAlumno->nombre == 'B1') {
                        $nivelHelper = NivelAlumno::find()->where('nombre="B2"')->one();
                    } else if ($alumno->nivelAlumno->nombre == 'B2') {
                        $nivelHelper = NivelAlumno::find()->where('nombre="C1"')->one();
                    } else if ($alumno->nivelAlumno->nombre == 'C1') {
                        $nivelHelper = NivelAlumno::find()->where('nombre="C2"')->one();
                    } else if ($alumno->nivelAlumno->nombre == 'C2') {
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

                    array_push(
                        $datos,
                        date('d-m-Y', $examenMock->fecha_realizacion),
                        $examenMock->examen->tipoExamen->nombre,
                        (int)$calificacionLis . '%',
                        (int)$calificacionRea . '%',
                        (int)$calificacionUse . '%',
                        (int)$promedio . '%',
                        $alumno->nivelAlumno->nombre
                    );
                } else {
                    array_push(
                        $datos,
                        'N/A',
                        'N/A',
                        'N/A',
                        'N/A',
                        'N/A',
                        'N/A',
                        $alumno->nivelAlumno->nombre
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
                'A8'
            );

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
        if ($type == $diagnosticType->id) {
            $spreadsheet->getActiveSheet()->getStyle('A8:L8')->applyFromArray($styleArrayHeader);
            $spreadsheet->getActiveSheet()->getStyle('A9:L' . strval($renglones + 8))->applyFromArray($styleArrayData);
        } else if ($type == $mockType->id) {
            $spreadsheet->getActiveSheet()->getStyle('A8:K8')->applyFromArray($styleArrayHeader);
            $spreadsheet->getActiveSheet()->getStyle('A9:K' . strval($renglones + 8))->applyFromArray($styleArrayData);
        }

        $spreadsheet->setActiveSheetIndex(0);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Students.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        setCookie("downloadStarted", 1, time() + 20, '/', "", false, false);
        $writer->save('php://output');
        exit;
    }

    public function actionDeleteMultiple(){
        $selection = (array)Yii::$app->request->post('selection');
        $id = Yii::$app->request->post('grupo-id');
        var_dum($selection);exit;
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
        return $this->redirect(['groups-institute/index']);
    }

    public function actionEditGroup(){
        $form = new GrupoForm();
        if ($form->load(Yii::$app->request->post())) {
            if($form->updateData()){
                return $this->actionGrupo($form->id);
            }
        }
        Yii::$app->session->setFlash('error', "Data can't be updated.");
        return $this->goBack();
    }


    public function actionExportGroups($id, $type, $file = 'xls'){
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
                            if($alumno_examen->cerrado(1,$estudiante->id) || $calificaciones->calificacionWriting){
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
                        if ($examenMock->examen->nivelAlumno->clave == 'A1' || $examenMock->examen->nivelAlumno->clave == 'A2') {
                            $calificacionUse = $examenMock->calificaciones->promedio_use;
                            $calificacionRea = $examenMock->calificaciones->promedio_reading;
                            $calificacionLis = $examenMock->calificaciones->promedio_listening;
                        } else {
                            $calificacionUse = $examenMock->calificaciones->promedio_use;
                            $calificacionRea = $examenMock->calificaciones->promedio_reading;
                            $calificacionLis = $examenMock->calificaciones->promedio_listening;
                        }
                        $promedio = $examenMock->calificaciones->promedio;
                        $nivel_inicial =  NivelAlumno::findOne($alumno->nivel_inicio_mock_id);
                        $nivel_inicial = $nivel_inicial ? $nivel_inicial->nombre : 'N/A';
                        array_push(
                            $datos,
                            date('d-m-Y', $examenMock->fecha_realizacion),
                            'Mock',
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
        $spreadsheet->getActiveSheet()->getColumnDimension('M')->setWidth(15);
        if(isset($programa) && $programa == 'CLI'){
            $spreadsheet->getActiveSheet()->getColumnDimension('N')->setWidth(15);
        }
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
        $spreadsheet->getActiveSheet()->getStyle('A1:N8')->applyFromArray($noTableStyle);
        $spreadsheet->getActiveSheet()->getStyle('E1:E8')->applyFromArray($styleColumnData);
        if ($type == $diagnosticType->id) {
            $spreadsheet->getActiveSheet()->getStyle('A9:N9')->applyFromArray($styleArrayHeader);
            $spreadsheet->getActiveSheet()->getStyle('A10:M' . strval($renglones + 9))->applyFromArray($styleArrayData);
        } else if ($type == $mockType->id) {
            $spreadsheet->getActiveSheet()->getStyle('A9:N9')->applyFromArray($styleArrayHeader);
            $spreadsheet->getActiveSheet()->getStyle('A10:N' . strval($renglones + 9))->applyFromArray($styleArrayData);
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

    public function actionContractAccepted() {
        if (Yii::$app->request->isPost) {
            $id = Yii::$app->request->post('institute');
            if (isset($id)) {
                $instituto = Instituto::findOne($id);
                $instituto->contractAccepted = 1;
                $instituto->update();
            }
        }
        return $this->redirect(['groups-institute/index']);
    }
}
