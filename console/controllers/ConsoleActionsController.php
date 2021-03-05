<?php

namespace console\controllers;

use app\models\AlumnoExamen;
use app\models\Examen;
use Yii;
use yii\console\Controller;
use yii\helpers\Console;
use yii\console\ExitCode;

class ConsoleActionsController extends Controller
{
    private $transaction;

    public function actionCrearPromedio() {
        $connection = \Yii::$app->db;
        $this->transaction = $connection->beginTransaction();

        $exams = AlumnoExamen::find()
            ->where([
                'and',
                ['is not', 'calificaciones_id', null],
                ['is', 'calificaciones.promedio_importado', null],
            ])
            ->leftJoin('calificaciones', 'calificaciones.id = alumno_examen.calificaciones_id');

        foreach($exams->each() as $exam) {
            if (isset($exam->examen)) {
                echo $exam->id."\n";
                $calificaciones = $exam->calificaciones;
                switch ($exam->tipoExamen->clave) {
                    case "DIA":
                        $calificaciones->setAverageScoreForDiagnostic($exam->examen);
                    break;
                    case "MOC":
                        $calificaciones->setAverageScoreForMock($exam->examen);
                    break;
                    case "CER":
                        $student = $exam->alumno;
                        $calificaciones->setAverageScoreForCertificate($exam->examen, $student->instituteProgram);
                    break;
                }
                if (!$calificaciones->save()) {
                    $this->transaction->rollBack();
                    echo "Error guardando promedios";
                    return ExitCode::SOFTWARE;
                }
            }
        }

        $this->transaction->commit();
        echo "Exito";
        return ExitCode::OK;
    }
}
