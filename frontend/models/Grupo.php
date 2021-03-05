<?php

namespace app\models;

use yii\behaviors\TimestampBehavior;
use Yii;

/**
 * This is the model class for table "grupo".
 *
 * @property int $id
 * @property int $nivel_id
 * @property int $instituto_id
 * @property string $grupo
 * @property int $status
 * @property int $created_at
 * @property int $updated_at
 * @property int $ciclo_escolar_id
 *
 * @property Alumno[] $alumnos
 * @property CicloEscolar $cicloEscolar
 * @property Instituto $instituto
 * @property Nivel $nivel
 */
class Grupo extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'grupo';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nivel_id', 'instituto_id', 'grupo', 'status', 'ciclo_escolar_id'], 'required'],
            [['nivel_id', 'instituto_id', 'status', 'created_at', 'updated_at', 'ciclo_escolar_id'], 'integer'],
            [['grupo'], 'string', 'max' => 45],
            [['ciclo_escolar_id'], 'exist', 'skipOnError' => true, 'targetClass' => CicloEscolar::className(), 'targetAttribute' => ['ciclo_escolar_id' => 'id']],
            [['instituto_id'], 'exist', 'skipOnError' => true, 'targetClass' => Instituto::className(), 'targetAttribute' => ['instituto_id' => 'id']],
            [['nivel_id'], 'exist', 'skipOnError' => true, 'targetClass' => Nivel::className(), 'targetAttribute' => ['nivel_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nivel_id' => 'Nivel ID',
            'instituto_id' => 'Instituto ID',
            'grupo' => 'Grupo',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'ciclo_escolar_id' => 'Ciclo Escolar ID',
        ];
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAlumnos()
    {
        return $this->hasMany(Alumno::className(), ['grupo_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCicloEscolar()
    {
        return $this->hasOne(CicloEscolar::className(), ['id' => 'ciclo_escolar_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInstituto()
    {
        return $this->hasOne(Instituto::className(), ['id' => 'instituto_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNivel()
    {
        return $this->hasOne(Nivel::className(), ['id' => 'nivel_id']);
    }
}
