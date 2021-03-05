<?php

namespace api\modules\v1\models;

use Yii;

/**
 * This is the model class for table "programa".
 *
 * @property int $id
 * @property string $clave
 * @property string $nombre
 *
 * @property Instituto[] $institutos
 */
class Programa extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'programa';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['clave', 'nombre'], 'required'],
            [['clave', 'nombre'], 'string', 'max' => 45],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'clave' => 'Clave',
            'nombre' => 'Nombre',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInstitutos()
    {
        return $this->hasMany(Instituto::className(), ['programa_id' => 'id']);
    }
}
