<?php

namespace api\modules\v1\models;

use Yii;

/**
 * This is the model class for table "tipo_reactivo".
 *
 * @property int $id
 * @property string $nombre
 * @property string $clave
 *
 * @property Reactivo[] $reactivos
 */
class TipoReactivo extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tipo_reactivo';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nombre', 'clave'], 'string', 'max' => 45],
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
            'clave' => 'Clave',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReactivos()
    {
        return $this->hasMany(Reactivo::className(), ['tipo_reactivo_id' => 'id']);
    }
}
