<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "enunciado_column".
 *
 * @property int $id
 * @property int $reactivo_id
 * @property string $enunciado
 * @property int $respuesta_column_id
 *
 * @property Reactivo $reactivo
 * @property RespuestaColumn $respuestaColumn
 */
class EnunciadoColumn extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'enunciado_column';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['reactivo_id', 'respuesta_column_id'], 'required'],
            [['reactivo_id', 'respuesta_column_id'], 'integer'],
            [['enunciado'], 'string', 'max' => 45],
            [['reactivo_id'], 'exist', 'skipOnError' => true, 'targetClass' => Reactivo::className(), 'targetAttribute' => ['reactivo_id' => 'id']],
            [['respuesta_column_id'], 'exist', 'skipOnError' => true, 'targetClass' => RespuestaColumn::className(), 'targetAttribute' => ['respuesta_column_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'reactivo_id' => 'Reactivo ID',
            'enunciado' => 'Enunciado',
            'respuesta_column_id' => 'Respuesta Column ID',
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
    public function getRespuestaColumn()
    {
        return $this->hasOne(RespuestaColumn::className(), ['id' => 'respuesta_column_id']);
    }
}
