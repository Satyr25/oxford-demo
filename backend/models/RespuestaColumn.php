<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "respuesta_column".
 *
 * @property int $id
 * @property string $respuesta
 * @property int $reactivo_id
 *
 * @property Reactivo $reactivo
 */
class RespuestaColumn extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'respuesta_column';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['reactivo_id'], 'required'],
            [['reactivo_id'], 'integer'],
            [['respuesta'], 'string', 'max' => 45],
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
            'respuesta' => 'Respuesta',
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
}
