<?php

namespace app\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use app\models\Instituto;

/**
 * InstituteSummarySearch represents the model behind the search form about `app\models\Instituto` on summary.
 */
class InstituteSummarySearch extends Instituto
{
    public $examType;
    public $schoolYear;
    public $program;
    public $examsStarted;
    public $instituteStatus;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['examType', 'schoolYear'], 'integer'],
            [['examsStarted'], 'string'],
            [['examType', 'schoolYear', 'instituteStatus'], 'required'],
            [['program', 'examsStarted'], 'default', 'value' => null],
            [['program', 'instituteStatus'], 'each', 'rule' => ['integer']]
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Return total number of started institutes
     *
     * @return int
     */
    public function getStartedInstitutes()
    {
        $query = (new \yii\db\Query())
            ->select([
                'instituto.id', 'COUNT(alumno_examen.id) as examenes',
                'COUNT(alumno_examen.fecha_realizacion) as examenes_terminados',
                'COUNT(alumno_examen.use_used_time) as useTime',
                'COUNT(alumno_examen.listening_used_time) as listeningTime',
                'COUNT(alumno_examen.reading_used_time) as readingTime'
            ])
            ->from('instituto')
            ->where([
                'and',
                ['instituto.status' => $this->instituteStatus],
                ['instituto.borrado' => 0],
                ['instituto.pruebas' => 0],
                ['grupo.status' => 1],
                ['grupo.ciclo_escolar_id' => $this->schoolYear],
                ['alumno.status' => 1],
                ['tipo_examen.id' => $this->examType],
            ])
            ->leftJoin('grupo', 'instituto.id = grupo.instituto_id')
            ->leftJoin('alumno', 'grupo.id = alumno.grupo_id')
            ->leftJoin('alumno_examen', 'alumno.id = alumno_examen.alumno_id')
            ->leftJoin('tipo_examen', 'tipo_examen.id = alumno_examen.tipo_examen_id')
            ->having('examenes_terminados < examenes')
            ->andHaving('examenes_terminados > 0')
            ->andHaving([
                'or',
                ['>=', 'useTime', 1],
                ['>=', 'readingTime', 1],
                ['>=', 'listeningTime', 1],
            ])
            ->groupBy(['instituto.id']);
        if (isset($this->program)) {
            $query->andWhere(['instituto.programa_id' => $this->program]);
        }
        return $query->count();
    }

    /**
     * Return total number of finished institutes
     *
     * @return int
     */
    public function getFinishedInstitutes() {
        $query = (new \yii\db\Query())
            ->select(['instituto.id', 'COUNT(alumno_examen.id) as examenes', 'COUNT(alumno_examen.fecha_realizacion) as examenes_terminados'])
            ->from('instituto')
            ->where([
                'and',
                ['instituto.status' => $this->instituteStatus],
                ['instituto.borrado' => 0],
                ['instituto.pruebas' => 0],
                ['grupo.status' => 1],
                ['grupo.ciclo_escolar_id' => $this->schoolYear],
                ['alumno.status' => 1],
                ['tipo_examen.id' => $this->examType],
            ])
            ->leftJoin('grupo', 'instituto.id = grupo.instituto_id')
            ->leftJoin('alumno', 'grupo.id = alumno.grupo_id')
            ->leftJoin('alumno_examen', 'alumno.id = alumno_examen.alumno_id')
            ->leftJoin('tipo_examen', 'tipo_examen.id = alumno_examen.tipo_examen_id')
            ->groupBy(['instituto.id'])
            ->having('examenes = examenes_terminados');
        if (isset($this->program)) {
            $query->andWhere(['instituto.programa_id' => $this->program]);
        }
        return $query->count();
    }

    /**
     * Return total number of not started institutes
     *
     * @return int
     */
    public function getNotStartedInstitutes() {
        $query = (new \yii\db\Query())
            ->select([
                'instituto.id', 'COUNT(alumno_examen.id) as examenes',
                'COUNT(alumno_examen.fecha_realizacion) as examenes_terminados',
                'COUNT(alumno_examen.use_used_time) as useTime',
                'COUNT(alumno_examen.listening_used_time) as listeningTime',
                'COUNT(alumno_examen.reading_used_time) as readingTime'
            ])
            ->from('instituto')
            ->where([
                'and',
                ['instituto.status' => $this->instituteStatus],
                ['instituto.borrado' => 0],
                ['instituto.pruebas' => 0],
                ['grupo.status' => 1],
                ['grupo.ciclo_escolar_id' => $this->schoolYear],
                ['alumno.status' => 1],
                ['tipo_examen.id' => $this->examType],
            ])
            ->leftJoin('grupo', 'instituto.id = grupo.instituto_id')
            ->leftJoin('alumno', 'grupo.id = alumno.grupo_id')
            ->leftJoin('alumno_examen', 'alumno.id = alumno_examen.alumno_id')
            ->leftJoin('tipo_examen', 'tipo_examen.id = alumno_examen.tipo_examen_id')
            ->groupBy(['instituto.id'])
            ->having('examenes_terminados = 0');
        if (isset($this->program)) {
            $query->andWhere(['instituto.programa_id' => $this->program]);
        }
        return $query->count();
    }

    /**
     * Return total number of started students
     *
     * @return int
     */
    public function getStartedStudents() {
        $query = (new \yii\db\Query())
            ->select([
                'alumno.id', 'COUNT(alumno_examen.fecha_realizacion) as examenes_terminados',
                'COUNT(alumno_examen.id) as examenes',
                'COUNT(alumno_examen.use_used_time) as useTime',
                'COUNT(alumno_examen.listening_used_time) as listeningTime',
                'COUNT(alumno_examen.reading_used_time) as readingTime'
            ])
            ->from('alumno')
            ->where([
                'instituto.status' => $this->instituteStatus,
                'instituto.borrado' => 0,
                'instituto.pruebas' => 0,
                'grupo.status' => 1,
                'grupo.ciclo_escolar_id' => $this->schoolYear,
                'alumno.status' => 1,
                'tipo_examen.id' => $this->examType,
            ])
            ->leftJoin('alumno_examen', 'alumno_examen.alumno_id = alumno.id')
            ->leftJoin('tipo_examen', 'tipo_examen.id = alumno_examen.tipo_examen_id')
            ->leftJoin('grupo' , 'alumno.grupo_id = grupo.id')
            ->leftJoin('instituto', 'grupo.instituto_id = instituto.id')
            ->groupBy(['alumno.id'])
            ->having('examenes_terminados < examenes')
            ->andHaving('examenes_terminados > 0')
            ->andHaving([
                'or',
                ['>=', 'useTime', 1],
                ['>=', 'readingTime', 1],
                ['>=', 'listeningTime', 1],
            ]);
        if (isset($this->program)) {
            $query->andWhere(['instituto.programa_id' => $this->program]);
        }
        return $query->count();
    }

    /**
     * Return total number of finished students
     *
     * @return int
     */
    public function getFinishedStudents() {
        $query = (new \yii\db\Query())
            ->select([
                'alumno.id', 'COUNT(alumno_examen.fecha_realizacion) as examenes_terminados',
                'COUNT(alumno_examen.id) as examenes',
            ])
            ->from('alumno')
            ->where([
                'instituto.status' => $this->instituteStatus,
                'instituto.borrado' => 0,
                'instituto.pruebas' => 0,
                'grupo.status' => 1,
                'grupo.ciclo_escolar_id' => $this->schoolYear,
                'alumno.status' => 1,
                'tipo_examen.id' => $this->examType,
            ])
            ->leftJoin('alumno_examen', 'alumno_examen.alumno_id = alumno.id')
            ->leftJoin('tipo_examen', 'tipo_examen.id = alumno_examen.tipo_examen_id')
            ->leftJoin('grupo', 'alumno.grupo_id = grupo.id')
            ->leftJoin('instituto', 'grupo.instituto_id = instituto.id')
            ->groupBy(['alumno.id'])
            ->having('examenes_terminados = examenes');
        if (isset($this->program)) {
            $query->andWhere(['instituto.programa_id' => $this->program]);
        }
        return $query->count();
    }

     /**
     * Return total number of not started students
     *
     * @return int
     */
    public function getNotStartedStudents()
    {
        $query = (new \yii\db\Query())
            ->select([
                'alumno.id', 'COUNT(alumno_examen.fecha_realizacion) as examenes_terminados',
                'COUNT(alumno_examen.id) as examenes',
            ])
            ->from('alumno')
            ->where([
                'instituto.status' => $this->instituteStatus,
                'instituto.borrado' => 0,
                'instituto.pruebas' => 0,
                'grupo.status' => 1,
                'alumno.status' => 1,
                'tipo_examen.id' => $this->examType,
                'grupo.ciclo_escolar_id' => $this->schoolYear
            ])
            ->leftJoin('alumno_examen', 'alumno_examen.alumno_id = alumno.id')
            ->leftJoin('tipo_examen', 'tipo_examen.id = alumno_examen.tipo_examen_id')
            ->leftJoin('grupo', 'alumno.grupo_id = grupo.id')
            ->leftJoin('instituto', 'grupo.instituto_id = instituto.id')
            ->groupBy(['alumno.id'])
            ->having('examenes_terminados = 0');
        if (isset($this->program)) {
            $query->andWhere(['instituto.programa_id' => $this->program]);
        }
        return $query->count();
    }

    /**
     * Return total number of institutes
     *
     * @return int
     */
    public function getTotalInstitutes()
    {
         $query = (new \yii\db\Query())
            ->select('instituto.id')
            ->leftJoin('grupo', 'instituto.id = grupo.instituto_id')
            ->from('instituto')
            ->where([
                'instituto.status' => $this->instituteStatus,
                'instituto.borrado' => 0,
                'instituto.pruebas' => 0,
                'grupo.status' => 1,
                'grupo.ciclo_escolar_id' => $this->schoolYear,
            ])
            ->groupBy('instituto.id');
        if (isset($this->program)) {
            $query->andWhere(['instituto.programa_id' => $this->program]);
        }
        return $query->count();
    }

    /**
     * Return total number of students
     *
     * @return int
     */
    public function getTotalStudents()
    {
        $query = (new \yii\db\Query())
             ->select(['alumno.id'])
             ->leftJoin('grupo', 'alumno.grupo_id = grupo.id')
             ->leftJoin('instituto', 'grupo.instituto_id = instituto.id')
             ->from('alumno')
             ->where([
                 'alumno.status' => 1,
                 'instituto.status' => $this->instituteStatus,
                 'instituto.borrado' => 0,
                 'instituto.pruebas' => 0,
                 'grupo.status' => 1,
                 'grupo.ciclo_escolar_id' => $this->schoolYear
             ]);
        if (isset($this->program)) {
            $query->andWhere(['instituto.programa_id' => $this->program]);
        }
        return $query->count();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param bool $modelsRequired
     *
     * @return ActiveDataProvider
     */
    public function searchByExams($modelsRequired = false)
    {
        $query = Instituto::find()->where([
                'instituto.borrado' => 0,
                'instituto.status' => $this->instituteStatus,
                'instituto.pruebas' => 0,
                'grupo.status' => 1,
                'grupo.ciclo_escolar_id' => $this->schoolYear
            ])
            ->leftJoin('grupo', 'instituto.id = grupo.instituto_id')
            ->groupBy(['instituto.id']);
        if (isset($this->program)) {
            $query->andWhere([
                'instituto.programa_id' => $this->program
            ]);
        }
        if ($this->examsStarted != null) {
            $query->leftJoin('alumno', 'grupo.id = alumno.grupo_id')
                ->leftJoin('alumno_examen', 'alumno.id = alumno_examen.alumno_id')
                ->leftJoin('tipo_examen', 'tipo_examen.id = alumno_examen.tipo_examen_id');
            if($this->examsStarted == 'started'){
                $query->select([
                        'instituto.*', 'COUNT(alumno_examen.id) as examenes',
                        'COUNT(alumno_examen.fecha_realizacion) as examenes_terminados',
                        'COUNT(alumno_examen.use_used_time) as useTime',
                        'COUNT(alumno_examen.listening_used_time) as listeningTime',
                        'COUNT(alumno_examen.reading_used_time) as readingTime'
                    ])
                    ->andWhere([
                        'and',
                        ['alumno.status' => 1],
                        ['tipo_examen.id' => $this->examType],
                    ])
                    ->having('examenes_terminados < examenes')
                    ->andHaving('examenes_terminados > 0')
                    ->andHaving([
                        'or',
                        ['>=', 'useTime', 1],
                        ['>=', 'readingTime', 1],
                        ['>=', 'listeningTime', 1],
                    ]);
            } else if ($this->examsStarted == 'not-started') {
                $query->select(['instituto.*', 'COUNT(alumno_examen.id) as examenes', 'COUNT(alumno_examen.fecha_realizacion) as examenes_terminados'])
                    ->andWhere([
                        'and',
                        ['alumno.status' => 1],
                        ['tipo_examen.id' => $this->examType],
                    ])
                    ->having('examenes_terminados = 0');
            } else if($this->examsStarted == 'finished') {
                $query->select(['instituto.*', 'COUNT(alumno_examen.id) as examenes', 'COUNT(alumno_examen.fecha_realizacion) as examenes_terminados'])
                    ->andWhere([
                        'and',
                        ['alumno.status' => 1],
                        ['tipo_examen.id' => $this->examType],
                    ])
                    ->having('examenes = examenes_terminados');
            } else if ($this->examsStarted == 'remaining') {
                $this->examsStarted = 'started';
                $institutesStarted = ArrayHelper::getColumn($this->searchByExams(true), 'id');
                $this->examsStarted = 'not-started';
                $institutesNotStarted = ArrayHelper::getColumn($this->searchByExams(true), 'id');
                $this->examsStarted = 'finished';
                $institutesFinished = ArrayHelper::getColumn($this->searchByExams(true), 'id');
                $institutes = ArrayHelper::merge($institutesStarted, $institutesNotStarted);
                $institutes = ArrayHelper::merge($institutesFinished, $institutes);
                    $query->andWhere([
                        'not in', 'instituto.id', $institutes
                    ]);
                $this->examsStarted = 'remaining';
            }
        }
        if ($modelsRequired) {
            return $query->all();
        } else {
            $dataProvider = new ActiveDataProvider([
                'query' => $query,
                'sort' => false,
                'pagination' => [
                    'pageSize' => 50,
                ],
            ]);
            return $dataProvider;
        }
    }

    /**
     * Return formatted string to property examsStarted
     *
     * @return string
     */
    public function getExamsStartedStr()
    {
        switch ($this->examsStarted) {
            case "started":
                return "Started";
                break;
            case "not-started":
                return "Not started";
                break;
            case "finished":
                return "Finished";
                break;
            case "remaining":
                return "Remaining";
                break;
            default:
                return "";
                break;
        }
    }
}
