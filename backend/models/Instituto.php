<?php

namespace app\models;
use yii\behaviors\TimestampBehavior;
use Yii;
use common\models\User;
use app\models\Grupo;
use DateTime;

/**
 * This is the model class for table "instituto".
 *
 * @property int $id
 * @property int $direccion_id
 * @property string $nombre
 * @property string $email
 * @property string $telefono
 * @property int $created_at
 * @property int $updated_at
 * @property int $status
 * @property int $borrado
 * @property int $programa_id
 * @property int $finalizacion_diagnostic
 * @property int $finalizacion_mock
 * @property int $fecha_examen_dia
 * @property int $fecha_examen_moc
 * @property int $fecha_examen_cer
 * @property string $ronda
 * @property int $contractAccepted
 * @property int $finalizacion_certificate
 * @property int $region_id
 * @property int $pruebas
 * @property int $fecha_examen_spe
 *
 * @property Grupo[] $grupos
 * @property Direccion $direccion
 * @property Programa $programa
 * @property Region $region
 * @property Profesor[] $profesors
 * @property User[] $users
 */
class Instituto extends \yii\db\ActiveRecord
{
    const STATUS_CANCELLED = 2;
    public $nombre_programa;
    public $nombre_profesor;
    public $ciudad;
    public $estado;
    public $pais;
    public $alumnos;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'instituto';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['direccion_id', 'created_at', 'updated_at', 'status', 'borrado', 'programa_id', 'finalizacion_diagnostic', 'finalizacion_mock', 'fecha_examen_dia', 'fecha_examen_moc', 'fecha_examen_cer', 'contractAccepted', 'finalizacion_certificate', 'region_id', 'pruebas', 'fecha_examen_spe'], 'integer'],
            [['nombre', 'email', 'status', 'borrado', 'pruebas'], 'required'],
            [['nombre'], 'string', 'max' => 256],
            [['email'], 'string', 'max' => 255],
            [['telefono'], 'string', 'max' => 45],
            [['ronda'], 'string', 'max' => 1],
            [['direccion_id'], 'exist', 'skipOnError' => true, 'targetClass' => Direccion::className(), 'targetAttribute' => ['direccion_id' => 'id']],
            [['programa_id'], 'exist', 'skipOnError' => true, 'targetClass' => Programa::className(), 'targetAttribute' => ['programa_id' => 'id']],
            [['region_id'], 'exist', 'skipOnError' => true, 'targetClass' => Region::className(), 'targetAttribute' => ['region_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => '#',
            'direccion_id' => 'Direccion ID',
            'nombre' => 'Institute Name',
            'email' => 'E-mail',
            'telefono' => 'Phone',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'status' => 'Status',
            'borrado' => 'Borrado',
            'programa_id' => 'Programa ID',
            'finalizacion_diagnostic' => 'Finalizacion Diagnostic',
            'finalizacion_mock' => 'Finalizacion Mock',
            'fecha_examen_dia' => 'Fecha Examen Dia',
            'fecha_examen_moc' => 'Fecha Examen Moc',
            'fecha_examen_cer' => 'Fecha Examen Cer',
            'ronda' => 'Ronda',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGrupos()
    {
        return $this->hasMany(Grupo::className(), ['instituto_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGruposActivos()
    {
        return $this->hasMany(Grupo::className(), ['instituto_id' => 'id'])
            ->innerJoin('ciclo_escolar', 'grupo.ciclo_escolar_id = ciclo_escolar.id')
            ->where([
                'grupo.status' => 1,
                'ciclo_escolar.status' => 1,
            ]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDireccion()
    {
        return $this->hasOne(Direccion::className(), ['id' => 'direccion_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProfesors()
    {
        return $this->hasMany(Profesor::className(), ['instituto_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(User::className(), ['instituto_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPrograma()
    {
        return $this->hasOne(Programa::className(), ['id' => 'programa_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRegion()
    {
        return $this->hasOne(Region::className(), ['id' => 'region_id']);
    }

    public function behaviors(){
        return [
            TimestampBehavior::className(),
        ];
    }

    public function actualizaStatus($status)
    {
        $this->status = $status;

        $validaGuarda = $this->update();
        if(!$validaGuarda){
            return false;
        }
        return true;
    }

    public function actualizaPais($pais)
    {
        $direccion = $this->direccion;

        $direccion->pais = $pais;
        $validaGuarda = $direccion->update();
        if(!$validaGuarda){
            return false;
        }
        return true;
    }

    public function totalAlumnos(){
        return $this->find()
        ->join('INNER JOIN', 'grupo', 'grupo.instituto_id = instituto.id')
        ->join('INNER JOIN', 'alumno', 'alumno.grupo_id = grupo.id')
        ->join('INNER JOIN', 'ciclo_escolar', 'grupo.ciclo_escolar_id = ciclo_escolar.id')
        ->where([
            'alumno.status' => 1,
            'grupo.status' => 1,
            'ciclo_escolar.status' => 1,
            'instituto.id' => $this->id,
        ])
        ->count();
    }

    public function examenesRealizados($tipo){
        return $this->find()
        ->join('INNER JOIN', 'grupo', 'grupo.instituto_id = instituto.id')
        ->join('INNER JOIN', 'ciclo_escolar', 'grupo.ciclo_escolar_id = ciclo_escolar.id')
        ->join('INNER JOIN', 'alumno', 'alumno.grupo_id = grupo.id')
        ->join('INNER JOIN', 'alumno_examen', 'alumno_examen.alumno_id = alumno.id')
        ->where([
            'and',
            ['alumno.status' => 1],
            ['grupo.status' => 1],
            ['ciclo_escolar.status' => 1],
            ['instituto.id' => $this->id],
            ['alumno_examen.tipo_examen_id' => $tipo],
            ['alumno_examen.status' => 1],
            ['is not', 'alumno_examen.fecha_realizacion', null]
        ])
        ->count();
    }

    public function datosReporte($ciclo_escolar){
        return $this->find()
            ->select([
                'instituto.*', 'programa.nombre AS nombre_programa',
                'profesor.nombre AS nombre_profesor',
                'pais.nombre AS pais', 'estado.estadonombre AS estado',
                'direccion.ciudad AS ciudad'
            ])
            ->join('LEFT JOIN', 'programa', 'programa.id = instituto.programa_id')
            ->join('INNER JOIN', 'profesor', 'profesor.instituto_id = instituto.id')
            ->join('INNER JOIN', 'direccion', 'direccion.id = instituto.direccion_id')
            ->join('INNER JOIN', 'estado', 'estado.id = direccion.estado_id')
            ->join('INNER JOIN', 'pais', 'pais.id = direccion.pais_id')
            ->leftJoin('grupo', 'instituto.id = grupo.instituto_id')
            ->where([
                'instituto.borrado' => 0,
                'instituto.status' => 1,
                'grupo.status' => 1,
                'grupo.ciclo_escolar_id' => $ciclo_escolar
            ])
            ->groupBy(['instituto.id', 'programa.nombre','profesor.nombre',
                'pais.nombre', 'estado.estadonombre',
                'direccion.ciudad'
            ])
            ->all();
    }

    public function getActiveAndCurrentGroups() {
        return $this->hasMany(Grupo::className(), ['instituto_id' => 'id'])
            ->joinWith('cicloEscolar')
            ->where([
                'grupo.status' => 1,
                'ciclo_escolar.status' => 1
            ]);
    }

    public function getNextExamDate() {
        $currentTime = new DateTime();
        $examDates = [];
        if (isset($this->fecha_examen_cer) && $this->fecha_examen_cer > $currentTime->getTimestamp()) array_push($examDates, $this->fecha_examen_cer);
        if (isset($this->fecha_examen_dia) && $this->fecha_examen_dia > $currentTime->getTimestamp()) array_push($examDates, $this->fecha_examen_dia);
        if (isset($this->fecha_examen_moc) && $this->fecha_examen_moc > $currentTime->getTimestamp()) array_push($examDates, $this->fecha_examen_moc);
        if (isset($this->fecha_examen_spe) && $this->fecha_examen_spe > $currentTime->getTimestamp()) array_push($examDates, $this->fecha_examen_spe);
        if (empty($examDates)) {
            return null;
        } else {
            $examTime = new DateTime();
            $examTime->setTimestamp(min($examDates));
            return $examTime;
        }
    }

    public function getNextExamType() {
        $currentTime = new DateTime();
        $examDates = [];
        if (isset($this->fecha_examen_cer) && $this->fecha_examen_cer > $currentTime->getTimestamp()) array_push($examDates, $this->fecha_examen_cer);
        if (isset($this->fecha_examen_dia) && $this->fecha_examen_dia > $currentTime->getTimestamp()) array_push($examDates, $this->fecha_examen_dia);
        if (isset($this->fecha_examen_moc) && $this->fecha_examen_moc > $currentTime->getTimestamp()) array_push($examDates, $this->fecha_examen_moc);
        if (isset($this->fecha_examen_spe) && $this->fecha_examen_spe > $currentTime->getTimestamp()) array_push($examDates, $this->fecha_examen_spe);
        if (empty($examDates)) {
            return null;
        } else {
            switch (min($examDates)) {
                case $this->fecha_examen_cer:
                    return 'Certificate';
                break;
                case $this->fecha_examen_dia:
                    return 'Diagnostic';
                break;
                case $this->fecha_examen_moc:
                    return 'Mock';
                break;
                case $this->fecha_examen_spe:
                    return 'Speaking';
                break;
            }
        }
    }

    public function setFinalizacion($examType, $date) {
        switch ($examType->clave) {
            case 'DIA':
                $this->finalizacion_diagnostic = $date;
            break;
            case 'MOC':
                $this->finalizacion_mock = $date;
            break;
            case 'CER':
                $this->finalizacion_certificate = $date;
            break;
        }
    }

    public function getStudentsSolvingExam($examType, $schoolYear) {
        return (new \yii\db\Query())
            ->select([
                'alumno.id', 'COUNT(alumno_examen.fecha_realizacion) as examenes_terminados',
                'COUNT(alumno_examen.id) as examenes',
                'COUNT(alumno_examen.use_used_time) as useTime',
                'COUNT(alumno_examen.listening_used_time) as listeningTime',
                'COUNT(alumno_examen.reading_used_time) as readingTime'
            ])
            ->from('alumno')
            ->where([
                'alumno.status' => 1,
                'tipo_examen.id' => $examType,
                'grupo.ciclo_escolar_id' => $schoolYear,
                'instituto.id' => $this->id
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
            ])
            ->count();
    }

    public function getStudentsFinishedExam($examType, $schoolYear) {
        return (new \yii\db\Query())
            ->select([
                'alumno.id', 'COUNT(alumno_examen.fecha_realizacion) as examenes_terminados',
                'COUNT(alumno_examen.id) as examenes',
            ])
            ->from('alumno')
            ->where([
                'alumno.status' => 1,
                'tipo_examen.id' => $examType,
                'grupo.ciclo_escolar_id' => $schoolYear,
                'instituto.id' => $this->id
            ])
            ->leftJoin('alumno_examen', 'alumno_examen.alumno_id = alumno.id')
            ->leftJoin('tipo_examen', 'tipo_examen.id = alumno_examen.tipo_examen_id')
            ->leftJoin('grupo', 'alumno.grupo_id = grupo.id')
            ->leftJoin('instituto', 'grupo.instituto_id = instituto.id')
            ->groupBy(['alumno.id'])
            ->having('examenes_terminados = examenes')
            ->count();
    }

    public function getStudentsNotStartedExam($examType, $schoolYear) {
        return (new \yii\db\Query())
            ->select([
                'alumno.id', 'COUNT(alumno_examen.fecha_realizacion) as examenes_terminados',
            ])
            ->from('alumno')
            ->where([
                'alumno.status' => 1,
                'tipo_examen.id' => $examType,
                'grupo.ciclo_escolar_id' => $schoolYear,
                'instituto.id' => $this->id
            ])
            ->leftJoin('alumno_examen', 'alumno_examen.alumno_id = alumno.id')
            ->leftJoin('tipo_examen', 'tipo_examen.id = alumno_examen.tipo_examen_id')
            ->leftJoin('grupo', 'alumno.grupo_id = grupo.id')
            ->leftJoin('instituto', 'grupo.instituto_id = instituto.id')
            ->groupBy(['alumno.id'])
            ->having('examenes_terminados = 0')
            ->count();
    }

    public function getStudentsBySchoolYear($schoolYear) {
        return (new \yii\db\Query())
             ->select(['alumno.id'])
             ->leftJoin('grupo', 'alumno.grupo_id = grupo.id')
             ->leftJoin('instituto', 'grupo.instituto_id = instituto.id')
             ->from('alumno')
             ->where([
                 'alumno.status' => 1,
                 'grupo.status' => 1,
                 'grupo.ciclo_escolar_id' => $schoolYear,
                 'instituto.id' => $this->id
             ])
            ->count();
    }
}
