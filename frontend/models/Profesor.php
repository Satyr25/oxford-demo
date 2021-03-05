<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "profesor".
 *
 * @property int $id
 * @property string $nombre
 * @property string $telefono
 * @property string $email
 * @property int $instituto_id
 *
 * @property Instituto $instituto
 */
class Profesor extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'profesor';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nombre', 'email', 'instituto_id'], 'required'],
            [['instituto_id'], 'integer'],
            [['nombre', 'telefono'], 'string', 'max' => 45],
            [['email'], 'string', 'max' => 256],
            [['instituto_id'], 'exist', 'skipOnError' => true, 'targetClass' => Instituto::className(), 'targetAttribute' => ['instituto_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nombre' => 'Contact Name',
            'telefono' => 'Telefono',
            'email' => 'Contact Email',
            'instituto_id' => 'Instituto ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInstituto()
    {
        return $this->hasOne(Instituto::className(), ['id' => 'instituto_id']);
    }
}
