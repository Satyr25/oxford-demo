<?php

namespace app\models;

use common\models\User;
use app\models\Seccion;

use Yii;

/**
 * This is the model class for table "examen".
 *
 * @property int $id
 * @property int $status
 * @property int $porcentaje
 * @property int $puntos
 * @property int $duracion
 * @property int $nivel_alumno_id
 * @property int $tipo_examen_id
 * @property int $variante_id
 * @property int $user_id
 *
 * @property AlumnoExamen[] $alumnoExamens
 * @property NivelAlumno $nivelAlumno
 * @property TipoExamen $tipoExamen
 * @property User $user
 * @property Variante $variante
 * @property Seccion[] $seccions
 */
class Examen extends \yii\db\ActiveRecord
{
    public $version;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'examen';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status', 'nivel_alumno_id', 'tipo_examen_id', 'variante_id', 'user_id'], 'required'],
            [['status', 'porcentaje', 'puntos', 'duracion', 'nivel_alumno_id', 'tipo_examen_id', 'variante_id', 'user_id','reading_duration','writing_duration','listening_duration','english_duration','diagnostic_v2','diagnostic_v3'], 'integer'],
            [['nivel_alumno_id'], 'exist', 'skipOnError' => true, 'targetClass' => NivelAlumno::className(), 'targetAttribute' => ['nivel_alumno_id' => 'id']],
            [['tipo_examen_id'], 'exist', 'skipOnError' => true, 'targetClass' => TipoExamen::className(), 'targetAttribute' => ['tipo_examen_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['variante_id'], 'exist', 'skipOnError' => true, 'targetClass' => Variante::className(), 'targetAttribute' => ['variante_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'status' => 'Status',
            'porcentaje' => 'Porcentaje',
            'puntos' => 'Puntos',
            'duracion' => 'Duracion',
            'nivel_alumno_id' => 'Nivel Alumno ID',
            'tipo_examen_id' => 'Tipo Examen ID',
            'variante_id' => 'Variante ID',
            'user_id' => 'User ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAlumnoExamens()
    {
        return $this->hasMany(AlumnoExamen::className(), ['examen_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNivelAlumno()
    {
        return $this->hasOne(NivelAlumno::className(), ['id' => 'nivel_alumno_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTipoExamen()
    {
        return $this->hasOne(TipoExamen::className(), ['id' => 'tipo_examen_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getVariante()
    {
        return $this->hasOne(Variante::className(), ['id' => 'variante_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSeccions()
    {
        return $this->hasMany(Seccion::className(), ['examen_id' => 'id']);
    }

    public function getExamenNameLevel()
    {
        return $this->nivelAlumno->nombre . ' ' . $this->tipoExamen->nombre . ' ' . $this->variante->nombre;
    }

    public function getExamenNameNoVersion()
    {
        return $this->nivelAlumno->nombre . ' ' . $this->tipoExamen->nombre;
    }

    public function actualizaStatus($status){
        $this->status = $status;

        $validaGuarda = $this->update();
        if (!$validaGuarda) {
            return false;
        }
        return true;
    }

    public function cantidadSecciones($seccion){
        return $this->find()
            ->join('INNER JOIN', 'seccion', 'seccion.examen_id = examen.id')
            ->join('INNER JOIN', 'tipo_seccion', 'seccion.tipo_seccion_id = tipo_seccion.id')
            ->where('seccion.puntos_seccion != 0 AND examen_id='.$this->id.' AND tipo_seccion.clave="'.$seccion.'"')
            ->count();
    }

    public function variantes($tipo,$nivel){
        if($tipo == 'CERV2'){
            $tipo = 'CER';
            $examenes = $this->find()
                ->select(['variante.nombre AS version', 'examen.id AS id'])
                ->join('INNER JOIN','nivel_alumno', 'nivel_alumno.id = examen.nivel_alumno_id')
                ->join('INNER JOIN','variante', 'variante.id = examen.variante_id')
                ->join('INNER JOIN','tipo_examen', 'tipo_examen.id = examen.tipo_examen_id')
                ->where('examen.certificate_v2 = 1 AND examen.status = 1 AND tipo_examen.clave = "'.$tipo.'" AND nivel_alumno.clave = "'.$nivel.'"')
                ->all();
        }else if($tipo == 'CER'){
            $examenes = $this->find()
                ->select(['variante.nombre AS version', 'examen.id AS id'])
                ->join('INNER JOIN','nivel_alumno', 'nivel_alumno.id = examen.nivel_alumno_id')
                ->join('INNER JOIN','variante', 'variante.id = examen.variante_id')
                ->join('INNER JOIN','tipo_examen', 'tipo_examen.id = examen.tipo_examen_id')
                ->where('examen.certificate_v2 IS NULL AND examen.status = 1 AND tipo_examen.clave = "'.$tipo.'" AND nivel_alumno.clave = "'.$nivel.'"')
                ->all();
        }else if($tipo == 'DIA'){
            $examenes = $this->find()
                ->select(['variante.nombre AS version', 'examen.id AS id'])
                ->join('INNER JOIN','nivel_alumno', 'nivel_alumno.id = examen.nivel_alumno_id')
                ->join('INNER JOIN','variante', 'variante.id = examen.variante_id')
                ->join('INNER JOIN','tipo_examen', 'tipo_examen.id = examen.tipo_examen_id')
                ->where('examen.diagnostic_v2 = 0 AND examen.status = 1 AND tipo_examen.clave = "'.$tipo.'" AND nivel_alumno.clave = "'.$nivel.'"')
                ->all();
        }else if($tipo == 'DIAV2'){
            $tipo = 'DIA';
            $examenes = $this->find()
                ->select(['variante.nombre AS version', 'examen.id AS id'])
                ->join('INNER JOIN','nivel_alumno', 'nivel_alumno.id = examen.nivel_alumno_id')
                ->join('INNER JOIN','variante', 'variante.id = examen.variante_id')
                ->join('INNER JOIN','tipo_examen', 'tipo_examen.id = examen.tipo_examen_id')
                ->where('examen.diagnostic_v2 = 1 AND examen.status = 1 AND tipo_examen.clave = "'.$tipo.'" AND nivel_alumno.clave = "'.$nivel.'"')
                ->all();
        }else{
            $examenes = $this->find()
                ->select(['variante.nombre AS version', 'examen.id AS id'])
                ->join('INNER JOIN','nivel_alumno', 'nivel_alumno.id = examen.nivel_alumno_id')
                ->join('INNER JOIN','variante', 'variante.id = examen.variante_id')
                ->join('INNER JOIN','tipo_examen', 'tipo_examen.id = examen.tipo_examen_id')
                ->where('examen.status = 1 AND tipo_examen.clave = "'.$tipo.'" AND nivel_alumno.clave = "'.$nivel.'"')
                ->all();
        }
        return $examenes;
    }

    public function totalPuntos($examen){
        $secciones = Seccion::find()
            ->select(['SUM(puntos_seccion) AS total_puntos'])
            ->where('examen_id = '.$examen)
            ->one();
        return $secciones->total_puntos;
    }

    public function getTotalPointsBySectionType($sectionType) {
        $totalPoints = 0;
        $sections = Seccion::find()
            ->leftJoin('tipo_seccion', 'tipo_seccion.id = seccion.tipo_seccion_id')
            ->where([
                'seccion.examen_id' => $this->id,
                'tipo_seccion.clave' => $sectionType
            ])
            ->all();
        foreach ($sections as $section) {
            $totalPoints += $section->puntos_seccion;
        }
        return $totalPoints;
    }

    public function getExamSectionsOrderedToShow() {
        return array_merge_recursive(
            $this->getExamSectionsByType('LIS'),
            $this->getExamSectionsByType('REA'),
            $this->getExamSectionsByType('USE'),
            $this->getExamSectionsByType('WRI')
        );
    }

    public function getExamSectionsByType($type) {
        return Seccion::find()
            ->leftJoin('tipo_seccion', 'tipo_seccion.id = seccion.tipo_seccion_id')
            ->where([
                'seccion.examen_id' => $this->id,
                'tipo_seccion.clave' => $type
            ])
            ->all();
    }

    public function actualizarTotales(){
        $puntos = Seccion::find()
            ->join('INNER JOIN','reactivo','reactivo.seccion_id = seccion.id')
            ->where(
                'seccion.examen_id = '.$this->id.' AND reactivo.status = 1'
            )->sum('reactivo.puntos');
        $this->puntos = $puntos;
        return $this->save();
    }
}
