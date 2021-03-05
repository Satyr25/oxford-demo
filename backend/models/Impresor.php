<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "impresor".
 *
 * @property int $id
 * @property string $nombre
 * @property string $apellidos
 */
class Impresor extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'impresor';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nombre', 'apellidos'], 'required'],
            [['nombre', 'apellidos'], 'string', 'max' => 128],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nombre' => 'Nombre',
            'apellidos' => 'Apellidos',
        ];
    }
}
