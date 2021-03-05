<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "alumno_examen".
 *
 * @property int $id
 * @property int $alumno_id
 * @property int $examen_id
 * @property int $tipo_examen_id
 * @property int $calificaciones_id
 * @property int $status
 * @property int $fecha_realizacion
 * @property int $writing_used_time
 * @property int $reading_used_time
 * @property int $listening_used_time
 * @property int $use_used_time
 * @property int $use_used_time
 * @property int $inactivity
 *
 * @property AluexaReactivos[] $aluexaReactivos
 * @property Alumno $alumno
 * @property Calificaciones $calificaciones
 * @property Examen $examen
 * @property TipoExamen $tipoExamen
 */
class AlumnoExamen extends \yii\db\ActiveRecord
{
    public $examenHecho;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'alumno_examen';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['alumno_id', 'tipo_examen_id', 'status'], 'required'],
            [['alumno_id', 'examen_id', 'tipo_examen_id', 'calificaciones_id', 'status', 'fecha_realizacion', 'writing_used_time', 'reading_used_time', 'listening_used_time', 'use_used_time','timedout','ultima_actualizacion','inactivity'], 'integer'],
            [['alumno_id'], 'exist', 'skipOnError' => true, 'targetClass' => Alumno::className(), 'targetAttribute' => ['alumno_id' => 'id']],
            [['calificaciones_id'], 'exist', 'skipOnError' => true, 'targetClass' => Calificaciones::className(), 'targetAttribute' => ['calificaciones_id' => 'id']],
            [['examen_id'], 'exist', 'skipOnError' => true, 'targetClass' => Examen::className(), 'targetAttribute' => ['examen_id' => 'id']],
            [['tipo_examen_id'], 'exist', 'skipOnError' => true, 'targetClass' => TipoExamen::className(), 'targetAttribute' => ['tipo_examen_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'alumno_id' => 'Alumno ID',
            'examen_id' => 'Examen ID',
            'tipo_examen_id' => 'Tipo Examen ID',
            'calificaciones_id' => 'Calificaciones ID',
            'status' => 'Status',
            'fecha_realizacion' => 'Fecha Realizacion',
            'writing_used_time' => 'Writing Used Time',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAluexaReactivos()
    {
        return $this->hasMany(AluexaReactivos::className(), ['alumno_examen_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAlumno()
    {
        return $this->hasOne(Alumno::className(), ['id' => 'alumno_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCalificaciones()
    {
        return $this->hasOne(Calificaciones::className(), ['id' => 'calificaciones_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getExamen()
    {
        return $this->hasOne(Examen::className(), ['id' => 'examen_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTipoExamen()
    {
        return $this->hasOne(TipoExamen::className(), ['id' => 'tipo_examen_id']);
    }
}
