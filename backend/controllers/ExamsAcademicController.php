<?php
namespace backend\controllers;
use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\UploadedFile;

use app\models\Examen;
use app\models\search\ExamenAcademicoSearch;
use app\models\TipoExamen;
use app\models\AlumnoExamen;
use app\models\NivelAlumno;
use app\models\Variante;
use app\models\Articulo;
use app\models\Audio;
use app\models\Reactivo;
use app\models\Alumno;

use backend\models\forms\ExamenForm;
use app\models\Seccion;
use app\models\TipoSeccion;
use app\models\Respuesta;
use backend\models\forms\ListeningMockForm;
use backend\models\forms\ReadingMockForm;
use backend\models\forms\ListeningCertificateForm;
use backend\models\forms\ReadingCertificateForm;
use backend\models\forms\WritingCertificateForm;
use backend\models\forms\SeccionForm;
use yii\helpers\Html;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\IOFactory;
use yii\helpers\Url;

class ExamsAcademicController extends Controller
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
                            'index','view-exam','add-exam-form', 'add-exam','status-examen',
                            'update-reading-form','update-reading',
                            'update-audio-form','update-audio',
                            'delete','delete-multiple','add-listening-mock','add-reading-mock',
                            'export-levels', 'update-section-form', 'update-section', 'view-speaking'
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
        if($rol == 'INS' || $rol == 'ALU')
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
        $tipo = "";
        $searchModel = new ExamenAcademicoSearch();
        // var_dump(Yii::$app->request->queryParams["type"]);exit;
        if(!array_key_exists('type', Yii::$app->request->queryParams)){
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        }else{
            $dataProvider = $searchModel->searchType(Yii::$app->request->queryParams['type'],Yii::$app->request->queryParams);
            $tipo = Yii::$app->request->queryParams['type'];
        }

        return $this->render('index', [
            'tipo'=>$tipo,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider
        ]);
    }

    public function actionViewExam($id)
    {
        $examForm = new ExamenForm();
        $listeningMock = new ListeningMockForm();
        $readingMock = new ReadingMockForm();
        $listeningCertificate = new ListeningCertificateForm();
        $readingCertificate = new ReadingCertificateForm();
        $writingCertificate = new WritingCertificateForm();
        if($examForm->load(Yii::$app->request->post()) && $examForm->actualizar()){
            return $this->refresh();
        }else if($listeningMock->load(Yii::$app->request->post()) && $listeningMock->guardar()){
            return $this->refresh();
        }else if($readingMock->load(Yii::$app->request->post()) && $readingMock->guardar()){
            return $this->refresh();
        }else if($listeningCertificate->load(Yii::$app->request->post()) && $listeningCertificate->guardar()){
            return $this->refresh();
        }else if($readingCertificate->load(Yii::$app->request->post()) && $readingCertificate->guardar()){
            return $this->refresh();
        }else if($writingCertificate->load(Yii::$app->request->post()) && $writingCertificate->guardar()){
            return $this->refresh();
        }else{
            $examen = Examen::findOne($id);
            $readings = [];
            $listenings = [];
            $articulos = [];
            $audios = [];
            $writings = [];
            $seccion_writing = Seccion::find()->where('tipo_seccion_id = 4 AND examen_id='.$examen->id)->one();
            if($examen->tipoExamen->clave == 'MOC'){
                $cantidadWritings = 0;
            }else{
                $cantidadWritings = count(Reactivo::find()
                    ->where('seccion_id = '.$seccion_writing->id.' AND status = 1')
                    ->all());
            }
            $tipo_reading = TipoSeccion::find()->where('clave="REA"')->one();
            $tipo_listening = TipoSeccion::find()->where('clave="LIS"')->one();
            $tipo_writing = TipoSeccion::find()->where('clave="WRI"')->one();
            $secciones_reading = Seccion::find()->where('tipo_seccion_id = '.$tipo_reading->id.' AND examen_id='.$examen->id)->all();
            $secciones_listening = Seccion::find()->where('tipo_seccion_id = '.$tipo_listening->id.' AND examen_id='.$examen->id)->all();
            $articulos = false;
            $audios = false;
            $mockType = TipoExamen::find()->where(['clave'=>'MOC'])->one();
            $cerType = TipoExamen::find()->where(['clave'=>'CER'])->one();
            if((count($secciones_reading) < 2 || count($secciones_listening) < 2) && $examen->tipo_examen_id == $mockType->id){
                $examenes = Examen::find()->where(['status'=>1, 'tipo_examen_id'=>$mockType->id, 'nivel_alumno_id' =>$examen->nivel_alumno_id])->all();
                foreach($examenes as $examenMoc){
                    foreach($examenMoc->seccions as $seccion){
                        if($seccion->tipoSeccion->clave == 'REA'){
                            if(count($secciones_reading) < 2){
                                $articulos[$seccion->id] = $seccion->reactivos[0]->articulo->titulo;
                            }
                        }
                        else if($seccion->tipoSeccion->clave == 'LIS'){
                            if(count($secciones_listening) < 2){
                                $audios[$seccion->id] = $seccion->reactivos[0]->audio->nombre;
                            }
                        }
                    }
                }
            }else if ((count($secciones_reading) < 2 || count($secciones_listening) < 2) || $cantidadWritings < 2 && $examen->tipo_examen_id == $cerType->id){
                $examen = Examen::findOne($id);
                if($examen->certificate_v2 == 1){
                    $examenes = Examen::find()->where(['status'=>1, 'tipo_examen_id'=>$cerType->id, 'nivel_alumno_id' =>$examen->nivel_alumno_id, 'certificate_v2' => 1])->all();
                }else{
                    $examenes = Examen::find()->where(['status'=>1, 'tipo_examen_id'=>$cerType->id, 'nivel_alumno_id' =>$examen->nivel_alumno_id])->all();
                }
                foreach($examenes as $examenCer){
                    foreach($examenCer->seccions as $seccion){
                        if($seccion->tipoSeccion->clave == 'REA'){
                            if(count($secciones_reading) < 2){
                                $articulos[$seccion->id] = $seccion->examen->variante->nombre.' - '.$seccion->reactivos[0]->articulo->titulo;
                            }
                        }
                        else if($seccion->tipoSeccion->clave == 'LIS'){
                            if(count($secciones_listening) < 2){
                                $audios[$seccion->id] = $seccion->examen->variante->nombre.' - '.$seccion->reactivos[0]->audio->nombre;
                            }
                        }
                        else if($seccion->tipoSeccion->clave == 'WRI'){
                            if($cantidadWritings < 2){
                                $activo = Reactivo::find()
                                    ->where('seccion_id = '.$seccion->id.' AND status = 1')
                                    ->all();
                                $writings[$activo[0]->id] = $activo[0]->seccion->examen->variante->nombre;
                            }
                        }
                    }
                }
            }
            $tipos = ArrayHelper::map(TipoExamen::find()->all(), 'id', 'nombre');
            $niveles = ArrayHelper::map(NivelAlumno::find()->all(), 'id', 'nombre');
            $versiones = ArrayHelper::map(Variante::find()->all(), 'id', 'nombre');
            $total_duration = $examen->english_duration+$examen->reading_duration+$examen->listening_duration+$examen->writing_duration;
            $examForm->cargar($id);
            $listeningMock->examen = $id;
            $readingMock->examen = $id;
            $listeningCertificate->examen = $id;
            $readingCertificate->examen = $id;
            $writingCertificate->examen = $id;
            Url::remember();
            return $this->render('view-exam',[
                'examen'=>$examen,
                'examForm'=>$examForm,
                'tipos'=>$tipos,
                'niveles'=>$niveles,
                'versiones'=>$versiones,
                'total' => $total_duration,
                'audios'=>$audios,
                'articulos'=>$articulos,
                'listeningMock' => $listeningMock,
                'readingMock' => $readingMock,
                'listeningCertificate' => $listeningCertificate,
                'readingCertificate' => $readingCertificate,
                'writingCertificate' => $writingCertificate,
                'writings' => $writings
            ]);
        }
    }

    public function actionUpdateSectionForm($seccion)
    {
        $model = new SeccionForm();
        $model->loadDataFromSection($seccion);
        return $this->renderAjax('_update-section', [
            'model' => $model
        ]);
    }

    public function actionUpdateSection()
    {
        $model = new SeccionForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->updateSection()) {
                Yii::$app->session->setFlash('success', "Success at update section");
            } else {
                Yii::$app->session->setFlash('error', "Error at update section");
            }
        }
        return $this->redirect(Url::previous());
    }

    public function actionAddExamForm(){
        $examForm = new ExamenForm();
        $examForm->diagnostic_v2 = 2;
        $examForm->certificate_v2 = 1;
        $tipos = ArrayHelper::map(TipoExamen::find()->all(), 'id', 'nombre');
        $niveles = ArrayHelper::map(NivelAlumno::find()->all(), 'id', 'nombre');
        $versiones = ArrayHelper::map(Variante::find()->all(), 'id', 'nombre');

        return $this->renderAjax('_add-exam',[
            'examForm'=>$examForm,
            'tipos'=>$tipos,
            'niveles'=>$niveles,
            'versiones'=>$versiones,
        ]);
    }

    public function actionAddExam(){
        $addExam = new ExamenForm();

        if ($addExam->load(Yii::$app->request->post())) {
            $guardar = $addExam->guardar();
            return $this->actionIndex();
        }
        return $this->actionIndex();
    }

    public function actionStatusExamen()
    {
        $examen = Examen::find()->where('examen.id=' . Yii::$app->request->post('id'))->one();
        $accion = $examen->actualizaStatus(Yii::$app->request->post('status'));
        return $accion;
    }

    public function actionUpdateReadingForm($id){
        $articulo = Articulo::findOne($id);
        return $this->renderAjax('_update_reading',[
            'articulo' => $articulo
        ]);
    }

    public function actionUpdateReading(){
        $articulo =  Articulo::findOne(Yii::$app->request->post('id'));
        $articulo->titulo = Yii::$app->request->post('titulo');
        $articulo->texto = Yii::$app->request->post('texto');
        if(Yii::$app->request->post('imagen') != 'undefined'){
            $archivo = UploadedFile::getInstanceByName('imagen');
            $ruta = Yii::getAlias('@backend/web/readings');
            if (!file_exists($ruta)) {
                if (!mkdir($ruta)) {
                    return false;
                }
            }
            $timestamp = time();
            $nombre_archivo = $timestamp . preg_replace("/[^a-z0-9\.]/", "", strtolower($archivo->name));
            if (!file_exists($ruta . '/' . $nombre_archivo)) {
                if (!$archivo->saveAs($ruta . '/' . $nombre_archivo, false)) {
                    exit;
                    return false;
                }
            }

            $articulo->imagen = 'readings/' . $nombre_archivo;
        }
        if(!$articulo->save()){
            return 0;
        }
        return $articulo->imagen ? Html::img('@web/'.$articulo->imagen,['class' => 'imagen-reading']) : 1;
    }

    public function actionUpdateAudioForm($id){
        $audio = Audio::findOne($id);
        return $this->renderAjax('_update_audio',[
            'audio' => $audio
        ]);
    }

    public function actionUpdateAudio(){
        $audio =  Audio::findOne(Yii::$app->request->post('id'));
        $audio->nombre = Yii::$app->request->post('titulo');
        if(Yii::$app->request->post('audio') != 'undefined'){
            $archivo = UploadedFile::getInstanceByName('audio');

            $ruta = Yii::getAlias('@backend/web/audios');
            $ruta_frontend = Yii::getAlias('@frontend/web/audios');
            if (!file_exists($ruta)) {
                if (!mkdir($ruta)) {
                    return false;
                }
            }

            if (!file_exists($ruta_frontend)) {
                if (!mkdir($ruta_frontend)) {
                    return false;
                }
            }

            $timestamp = time();
            $nombre_archivo = $timestamp . preg_replace("/[^a-z0-9\.]/", "", strtolower($archivo->name));

            if (!file_exists($ruta . '/' . $nombre_archivo)) {
                if (!$archivo->saveAs($ruta . '/' . $nombre_archivo, false)) {
                    exit;
                    return false;
                }
                if (!$archivo->saveAs($ruta_frontend . '/' . $nombre_archivo, true)) {
                    exit;
                    return false;
                }
            }

            $audio->audio = 'audios/' . $nombre_archivo;
        }
        if(!$audio->save()){
            return 0;
        }
        return 1;
    }

    public function actionDelete($id){
        $connection = \Yii::$app->db;
        $transaction = $connection->beginTransaction();
        $examen = Examen::findOne($id);
        $examen->status = 0;
        if(!$examen->save()){
            $transaction->rollback();
            Yii::$app->session->setFlash('error', "There was an error deleting the exam.");
            return $this->redirect(Yii::$app->urlManager->createUrl("exams-academic/index"));
        }
        $reactivos = Reactivo::find()
            ->join('INNER JOIN', 'seccion', 'seccion.id = reactivo.seccion_id')
            ->where('seccion.examen_id ='.$id)
            ->all();
        foreach ($reactivos as $reactivo) {
            $reactivo->status = 0;
            if(!$reactivo->save()){
                $transaction->rollback();
                Yii::$app->session->setFlash('error', "There was an error deleting the exam.");
                return $this->redirect(Yii::$app->urlManager->createUrl("exams-academic/index"));
            }
        }
        $transaction->commit();
        Yii::$app->session->setFlash('success', "The exam was deleted correctly.");
        return $this->redirect(Yii::$app->urlManager->createUrl("exams-academic/index"));
    }

    public function actionDeleteMultiple(){
        $selection = (array)Yii::$app->request->post('selection');
        foreach($selection as $examen_id){
            $connection = \Yii::$app->db;
            $transaction = $connection->beginTransaction();
            $examen = Examen::findOne($examen_id);
            $examen->status = 0;
            if(!$examen->save()){
                $transaction->rollback();
                Yii::$app->session->setFlash('error', "There was an error deleting the exam.");
                return $this->redirect(Yii::$app->urlManager->createUrl("exams-academic/index"));
            }
            $reactivos = Reactivo::find()
                ->join('INNER JOIN', 'seccion', 'seccion.id = reactivo.seccion_id')
                ->where('seccion.examen_id ='.$examen_id)
                ->all();
            foreach ($reactivos as $reactivo) {
                $reactivo->status = 0;
                if(!$reactivo->save()){
                    $transaction->rollback();
                    Yii::$app->session->setFlash('error', "There was an error deleting the exam.");
                    return $this->redirect(Yii::$app->urlManager->createUrl("exams-academic/index"));
                }
            }
            $transaction->commit();
            Yii::$app->session->setFlash('success', "The exam was deleted correctly.");
            return $this->redirect(Yii::$app->urlManager->createUrl("exams-academic/index"));
        }
    }

    public function actionViewSpeaking() {
        return $this->render('view-speaking', [
            'levels' => ArrayHelper::map(NivelAlumno::find()->where(['not in', 'clave', ['NO', 'DP']])->all(), 'nombre', 'clave')
        ]);
    }

    public function actionExportLevels($tipo='DIA'){
        $tipo_examen = TipoExamen::find()->where('clave="'.$tipo.'"')->one();
        $niveles = NivelAlumno::find()->where('clave!="NO"')->all();
        $total_alumnos = 0;
        $totales= [];
        foreach($niveles as $nivel){
            $puntos_seccion = [];
            $secciones = Seccion::find()
                ->select([
                    'examen_id AS examen_id',
                    'puntos_seccion AS puntos_seccion',
                    'clave AS clave'
                ])
                ->from('examen')
                ->join('INNER JOIN','seccion','seccion.examen_id = examen.id')
                ->join('INNER JOIN','tipo_seccion','tipo_seccion.id = seccion.tipo_seccion_id')
                ->where('examen.status = 1 AND examen.tipo_examen_id='.$tipo_examen->id.' AND examen.nivel_alumno_id='.$nivel->id)
                ->all();
            foreach($secciones as $seccion){
                $puntos_seccion[$seccion->examen_id][$seccion->clave] = $seccion->puntos_seccion;
            }
            if($tipo == 'DIA'){
                $alumnos_nivel = Alumno::find()
                    ->select([
                        'alumno_examen.examen_id AS examen',
                        'calificaciones.calificacionUse AS calificacionUse',
                        'calificaciones.calificacionReading AS calificacionReading',
                        'calificaciones.calificacionListening AS calificacionListening',
                        'calificaciones.calificacionWriting AS calificacionWriting',
                        'calificaciones.promedio_importado AS promedio_importado',
                    ])
                    ->join('INNER JOIN','alumno_examen', 'alumno_examen.alumno_id = alumno.id')
                    ->join('INNER JOIN','calificaciones', 'calificaciones.id = alumno_examen.calificaciones_id')
                    ->join('INNER JOIN','examen', 'examen.id = alumno_examen.examen_id')
                    ->where(
                        'alumno.status=1 AND alumno.nivel_alumno_id='.$nivel->id.
                        ' AND alumno_examen.tipo_examen_id = '.$tipo_examen->id.
                        ' AND examen.nivel_alumno_id = '.$nivel->id.
                        ' AND calificaciones.calificacionWriting IS NOT NULL'
                        )
                    ->all();
            }else if($tipo == 'MOC'){
                $alumnos_nivel = Alumno::find()
                    ->select([
                        'alumno_examen.examen_id AS examen',
                        'calificaciones.calificacionUse AS calificacionUse',
                        'calificaciones.calificacionReading AS calificacionReading',
                        'calificaciones.calificacionListening AS calificacionListening',
                        'calificaciones.promedio_importado AS promedio_importado',
                    ])
                    ->join('INNER JOIN','alumno_examen', 'alumno_examen.alumno_id = alumno.id')
                    ->join('INNER JOIN','calificaciones', 'calificaciones.id = alumno_examen.calificaciones_id')
                    ->join('INNER JOIN','examen', 'examen.id = alumno_examen.examen_id')
                    ->where(
                        'alumno.status=1 AND alumno.nivel_alumno_id='.$nivel->id.
                        ' AND alumno_examen.tipo_examen_id = '.$tipo_examen->id.
                        ' AND examen.nivel_alumno_id = '.$nivel->id
                        )
                    ->all();
            }
            $total_alumnos += $totales[$nivel->nombre]['alumnos'] = count($alumnos_nivel);
            $promedios = 0;
            foreach($alumnos_nivel as $alumno){
                if($tipo == 'DIA'){
                    if($alumno->promedio_importado){
                        $promedios += $alumno->promedio_importado;
                    }else{
                    $promedios +=
                        ((($alumno->calificacionUse*100)/$puntos_seccion[$alumno->examen]['USE'])+
                        (($alumno->calificacionReading*100)/$puntos_seccion[$alumno->examen]['REA'])+
                        (($alumno->calificacionListening*100)/$puntos_seccion[$alumno->examen]['LIS'])+
                        (($alumno->calificacionWriting*100)/$puntos_seccion[$alumno->examen]['WRI']))/4;
                    }
                }else if($tipo == 'MOC'){
                    if($alumno->promedio_importado){
                        $promedios += $alumno->promedio_importado;
                    }else{
                        if($nivel->clave == 'A1' || $nivel->clave == 'A2' || $nivel->clave == 'NO'){
                            $calificacionUse = ($alumno->calificacionUse * 100) / 12;
                            $calificacionRea = ($alumno->calificacionReading * 100) / 24;
                            $calificacionLis = ($alumno->calificacionListening * 100) / 24;
                        } else {
                            $calificacionUse = ($alumno->calificacionUse * 100) / 15;
                            $calificacionRea = ($alumno->calificacionReading * 100) / 32;
                            $calificacionLis = ($alumno->calificacionListening * 100) / 32;
                        }
                        $promedios += ($calificacionLis * .35) + ($calificacionRea * .35) + ($calificacionUse * .30);
                    }
                }
            }
            if(count($alumnos_nivel) > 0){
                $totales[$nivel->nombre]['promedio'] = round($promedios/count($alumnos_nivel),2,PHP_ROUND_HALF_EVEN);
            }else{
                $totales[$nivel->nombre]['promedio'] = 0;
            }
        }
        foreach($totales as $nivel => $total_nivel){
            $totales[$nivel]['porcentaje'] =  round(($total_nivel['alumnos']/$total_alumnos) * 100,2,PHP_ROUND_HALF_EVEN);
        }

        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()->setCreator('Oxford TCC')
            ->setLastModifiedBy('Maarten Balliauw')
            ->setTitle('Office 2007 XLSX Test Document')
            ->setSubject('Office 2007 XLSX Test Document')
            ->setDescription('Test document for Office 2007 XLSX, generated using PHP classes.')
            ->setKeywords('office 2007 openxml php')
            ->setCategory('Test result file');
        $spreadsheet->getActiveSheet()->setTitle('Levels');

        $drawing = new Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('Logo');
        $drawing->setPath(realpath('./images/logoColor.png'));
        $drawing->setHeight(100);
        $drawing->setWorksheet($spreadsheet->getActiveSheet());

        $tipo_examen = TipoExamen::find()->where('clave="'.$tipo.'"')->one();

        $spreadsheet->getActiveSheet()
            ->fromArray(
                ['LEVELS '.strtoupper($tipo_examen->nombre)],
                null,
                'B6'
            );
        $spreadsheet->getActiveSheet()
            ->fromArray(
                [strtoupper(date('M Y'))],
                null,
                'B7'
            );

        $data = array();

        $encabezado = ['LEVEL', 'STUDENTS', '%', 'AVERAGE GRADE'];
        array_push($data, $encabezado);

        $total_alumnos = 0;
        $numero_niveles = 0;
        $promedios = 0;
        foreach($totales as $nivel_total => $total){
            $numero_niveles++;
            array_push(
                $data,
                [
                    $nivel_total,
                    $total['alumnos'] ? $total['alumnos'] : '-',
                    $total['porcentaje'] ? $total['porcentaje'] : '-',
                    $total['promedio'] ? $total['promedio'] : '-'
                ]
            );
            $total_alumnos += $total['alumnos'];
            $promedios += $total['promedio'];
        }
        array_push(
            $data,
            [
                'TOTAL',
                $total_alumnos,
                '100',
                round($promedios/$numero_niveles,2,PHP_ROUND_HALF_EVEN)
            ]
        );

        $spreadsheet->getActiveSheet()
            ->fromArray(
                $data,
                null,
                'A9'
            );

        $renglones = count($totales)+1;

         //creando estilos
        $spreadsheet->getActiveSheet()->setShowGridlines(false);
        $spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(15);
        $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(15);
        $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(15);
        $spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(15);
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
        $spreadsheet->getActiveSheet()->getRowDimension('4')->setRowHeight(22);
        $spreadsheet->getActiveSheet()->getRowDimension('5')->setRowHeight(22);
        $spreadsheet->getActiveSheet()->getStyle('A9:D9')->applyFromArray($styleArrayHeader);
        $spreadsheet->getActiveSheet()->getStyle('A10:D' . strval($renglones + 9))->applyFromArray($styleArrayData);
        $spreadsheet->getActiveSheet()->getStyle('B6')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => [
                    'argb' => '00000000'
                ]
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
        ]);
        $spreadsheet->getActiveSheet()->getStyle('B7')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => [
                    'argb' => '00000000'
                ]
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
        ]);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Institute.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        setCookie("downloadStarted", 1, time() + 20, '/', "", false, false);
        $writer->save('php://output');
        exit;
    }
}
