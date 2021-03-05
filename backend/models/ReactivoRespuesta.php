<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "reactivo_respuesta".
 *
 * @property int $id
 * @property int $respuesta_id
 * @property int $reactivo_id
 *
 * @property Reactivo $reactivo
 * @property Respuesta $respuesta
 */
class ReactivoRespuesta extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'reactivo_respuesta';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'respuesta_id', 'reactivo_id'], 'required'],
            [['id', 'respuesta_id', 'reactivo_id'], 'integer'],
            [['id'], 'unique'],
            [['reactivo_id'], 'exist', 'skipOnError' => true, 'targetClass' => Reactivo::className(), 'targetAttribute' => ['reactivo_id' => 'id']],
            [['respuesta_id'], 'exist', 'skipOnError' => true, 'targetClass' => Respuesta::className(), 'targetAttribute' => ['respuesta_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'respuesta_id' => 'Respuesta ID',
            'reactivo_id' => 'Reactivo ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReactivo()
    {
        return $this->hasOne(Reactivo::className(), ['id' => 'reactivo_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRespuesta()
    {
        return $this->hasOne(Respuesta::className(), ['id' => 'respuesta_id']);
    }
}
