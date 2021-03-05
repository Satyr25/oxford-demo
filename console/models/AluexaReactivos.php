<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "aluexa_reactivos".
 *
 * @property int $id
 * @property int $alumno_examen_id
 * @property int $reactivo_id
 * @property int $respuesta_alu
 * @property int $enunciado_alu
 * @property string $respuestaWriting
 * @property int $calificado
 *
 * @property AlumnoExamen $alumnoExamen
 * @property Reactivo $reactivo
 */
class AluexaReactivos extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'aluexa_reactivos';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['alumno_examen_id', 'reactivo_id'], 'required'],
            [['alumno_examen_id', 'reactivo_id', 'respuesta_alu', 'enunciado_alu', 'calificado'], 'integer'],
            [['respuestaWriting','respuesta_completar'], 'string'],
            [['alumno_examen_id'], 'exist', 'skipOnError' => true, 'targetClass' => AlumnoExamen::className(), 'targetAttribute' => ['alumno_examen_id' => 'id']],
            [['reactivo_id'], 'exist', 'skipOnError' => true, 'targetClass' => Reactivo::className(), 'targetAttribute' => ['reactivo_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'alumno_examen_id' => 'Alumno Examen ID',
            'reactivo_id' => 'Reactivo ID',
            'respuesta_alu' => 'Respuesta Alu',
            'enunciado_alu' => 'Enunciado Alu',
            'respuestaWriting' => 'Respuesta Writing',
            'calificado' => 'Calificado',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAlumnoExamen()
    {
        return $this->hasOne(AlumnoExamen::className(), ['id' => 'alumno_examen_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReactivo()
    {
        return $this->hasOne(Reactivo::className(), ['id' => 'reactivo_id']);
    }
}
