<?php

namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use app\models\search\InstitutoSearch;
use app\models\search\InstituteSummarySearch;
use app\models\TipoExamen;
use app\models\CicloEscolar;
use app\models\Programa;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class SummaryController extends Controller
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
                        'actions' => ['index', 'details', 'export'],
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

        if ($rol == 'ACA' || $rol == 'INS' || $rol == 'ALU') {
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
        $searchModel = new InstituteSummarySearch();
        if (!$searchModel->load(Yii::$app->request->get()) || !$searchModel->validate()) {
            $diagnosticType = TipoExamen::find()->where(['clave' => 'DIA'])->one();
            $currentSchoolYear = CicloEscolar::find()->where(['status' => 1])->one();
            $searchModel->examType = $diagnosticType->id;
            $searchModel->schoolYear = $currentSchoolYear->id;
            $searchModel->instituteStatus = 1;
        }
        return $this->render('index', [
            'searchModel' => $searchModel,
            'examTypes' => ArrayHelper::map(TipoExamen::find()->all(), 'id', 'nombre'),
            'schoolYears' => ArrayHelper::map(CicloEscolar::find()->all(), 'id', 'nombre'),
            'programs' => ArrayHelper::map(Programa::find()->all(), 'id', 'nombre'),
            'totalInstitutes' => $searchModel->getTotalInstitutes(),
            'totalStudents' => $searchModel->getTotalStudents(),
            'startedInstitutes' => $searchModel->getStartedInstitutes(),
            'startedStudents' => $searchModel->getStartedStudents(),
            'notStartedInstitutes' => $searchModel->getNotStartedInstitutes(),
            'notStartedStudents' => $searchModel->getNotStartedStudents(),
            'finishedInstitutes' => $searchModel->getFinishedInstitutes(),
            'finishedStudents' => $searchModel->getFinishedStudents(),
        ]);
    }

    public function actionDetails()
    {
        $searchModel = new InstituteSummarySearch();
        if ($searchModel->load(Yii::$app->request->get()) && $searchModel->validate()) {
            $dataProvider = $searchModel->searchByExams();
            $examType = TipoExamen::findOne($searchModel->examType);
            $schoolYear = CicloEscolar::findOne($searchModel->schoolYear);
            $programs = Programa::findAll($searchModel->program);
            $searchModelView = new InstitutoSearch();
            $searchModelView->ciclo_escolar = $schoolYear->id;
            if (isset($programs) && !empty($programs)) {
                $programsStr = "";
                if (count($programs) > 1) {
                    foreach ($programs as $program) {
                        $programsStr .= $program->nombre . ', ';
                    }
                } else {
                    $programsStr = $programs[0]->nombre;
                }
            } else {
                $programsStr = "Todos los programas";
            }
            return $this->render('//institutes/index', [
                'dataProvider' => $dataProvider,
                'searchModel' => $searchModelView,
                'ciclos' => ArrayHelper::map(CicloEscolar::find()->all(), 'id', 'nombre'),
                'isSummary' => true,
                'examType' => $examType->nombre,
                'schoolYear' => $schoolYear->nombre,
                'programs' => $programsStr,
                'typeInstitutes' => $searchModel->getExamsStartedStr(),
            ]);
        } else {
            return $this->redirect(['summary/index']);
        }
    }

    public function actionExport()
    {
        $searchModel = new InstituteSummarySearch();
        if (!$searchModel->load(Yii::$app->request->get()) && !$searchModel->validate()) {
            return $this->redirect(['summary/index']);
        }
        // nueva hoja de calculo
        $spreadsheet = new Spreadsheet;
        $spreadsheet->getProperties()->setCreator('Oxford TCC')
            ->setLastModifiedBy('Oxford TCC')
            ->setTitle('Group Report');
        $spreadsheet->getActiveSheet()->setTitle('Alumnos');
        //mostrando imagen de logo
        $drawing = new Drawing();
        $drawing->setPath(realpath('./images/oxford_education_logo.png'))
            ->setName('Logo')
            ->setDescription('Logo')
            ->setHeight(100)
            ->setCoordinates('A1')
            ->setWorksheet($spreadsheet->getActiveSheet());
        $drawing2 = new Drawing();
        $drawing2->setPath(realpath('./images/logoColor.png'))
            ->setName('Logo')
            ->setDescription('Logo')
            ->setHeight(100)
            ->setCoordinates('B1')
            ->setWorksheet($spreadsheet->getActiveSheet());
        // informacion de titulo
        $examType = TipoExamen::findOne($searchModel->examType);
        $schoolYear = CicloEscolar::findOne($searchModel->schoolYear);
        $programs = Programa::findAll($searchModel->program);
        if (isset($programs) && !empty($programs)) {
            $programsStr = "";
            if (count($programs) > 1) {
                foreach ($programs as $program) {
                    $programsStr .= $program->nombre . ', ';
                }
            } else {
                $programsStr = $programs[0]->nombre;
            }
        } else {
            $programsStr = "Todos";
        }
        $title = [
            ['Summary Report'],
            [],
            ['Tipo de Examen', $examType->nombre],
            ['Ciclo Escolar', $schoolYear->nombre],
            ['Programas', $programsStr],
        ];
        $spreadsheet->getActiveSheet()
            ->fromArray($title, null, 'C1');
        // informacion general
        $totalInstitutes = $searchModel->getTotalInstitutes();
        $totalStudents = $searchModel->getTotalStudents();
        $startedInstitutes = $searchModel->getStartedInstitutes();
        $startedStudents = $searchModel->getStartedStudents();
        $finishedInstitutes = $searchModel->getFinishedInstitutes();
        $finishedStudents = $searchModel->getFinishedStudents();
        $notStartedInstitutes = $searchModel->getNotStartedInstitutes();
        $notStartedStudents = $searchModel->getNotStartedStudents();
        $generalInfo = [
            ['Total de colegios', $totalInstitutes],
            ['Total de colegios realizando', $startedInstitutes],
            ['Total de colegios terminados', $finishedInstitutes],
            ['Total de colegios no realizando', $notStartedInstitutes],
            ['Total de colegios restantes', $totalInstitutes - ($startedInstitutes + $finishedInstitutes + $notStartedInstitutes)],
            ['Total de estudiantes', $totalStudents],
            ['Total de estudiantes realizando', $startedStudents],
            ['Total de estudiantes terminados', $finishedStudents],
            ['Total de estudiantes no realizando', $notStartedStudents],
            ['Total de estudiantes restantes', $totalStudents - ($startedStudents + $finishedStudents + $notStartedStudents)],
        ];
        $spreadsheet->getActiveSheet()
            ->fromArray($generalInfo, null, 'A8', true);
        // titulos para columnas de listados
        $listTitles = ['Institutos', 'Total de alumnos no realizando', 'Total de alumnos realizando', 'Total de alumnos terminados', 'Total de alumnos restantes'];
        $spreadsheet->getActiveSheet()
            ->fromArray($listTitles, null, 'A18');
        // listados
        $institutes = $searchModel->searchByExams(true);
            $list = [];
            foreach ($institutes as $i => $institute) {
                $instituteStudents = $institute->getStudentsBySchoolYear($searchModel->schoolYear);
                $instituteNotStartedExam = $institute->getStudentsNotStartedExam($searchModel->examType, $searchModel->schoolYear);
                $instituteSolvingExam = $institute->getStudentsSolvingExam($searchModel->examType, $searchModel->schoolYear);
                $instituteFinishedExam = $institute->getStudentsFinishedExam($searchModel->examType, $searchModel->schoolYear);
                array_push($list, [
                    $institute->nombre,
                    $instituteNotStartedExam,
                    $instituteSolvingExam,
                    $instituteFinishedExam,
                    $instituteStudents - ($instituteNotStartedExam + $instituteSolvingExam + $instituteFinishedExam)
                ]
            );
            }
            $spreadsheet->getActiveSheet()
            ->fromArray($list, null, 'A19', true);
        //creando estilos
        $spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(40);
        $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(40);
        $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(30);
        $spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(30);
        $spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(30);
        $spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(30);

        $styleSheetTitle = [
            'font' => [
                'bold' => true,
                'size' => 15
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
        $noTableStyle = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_NONE,
                ],
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
        $spreadsheet->getActiveSheet()->getStyle('C1')->applyFromArray($styleSheetTitle);
        $spreadsheet->getActiveSheet()->getStyle('A1:L8')->applyFromArray($noTableStyle);
        $spreadsheet->getActiveSheet()->getStyle('C2:C8')->applyFromArray($styleColumnData);
        $spreadsheet->getActiveSheet()->getStyle('A18:E18')->applyFromArray($styleArrayHeader);
        // configuracion de export
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Institute.xlsx"');
        header('Cache-Control: max-age=0');
        $spreadsheet->getActiveSheet()->setShowGridlines(false);
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        setCookie("downloadStarted", 1, time() + 20, '/', "", false, false);
        $writer->save('php://output');
        exit;
    }
}
