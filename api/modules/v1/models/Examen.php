<?php

namespace api\modules\v1\models;

use common\models\User;

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
            [['status', 'porcentaje', 'puntos', 'duracion', 'nivel_alumno_id', 'tipo_examen_id', 'variante_id', 'user_id','reading_duration','writing_duration','listening_duration','english_duration'], 'integer'],
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

    public function getPointsFromSection($section)
    {
        $sections = Seccion::find()
            ->where([
                'examen_id' => $this->id,
                'tipo_seccion.clave' => $section
            ])
            ->leftJoin('tipo_seccion', 'seccion.tipo_seccion_id = tipo_seccion.id')
            ->all();
        $points = 0;
        foreach ($sections as $section) {
            $points += $section->puntos_seccion;
        }
        return $points;
    }
}
