<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "articulo".
 *
 * @property int $id
 * @property string $titulo
 * @property string $texto
 * @property string $imagen
 *
 * @property Reactivo[] $reactivos
 */
class Articulo extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'articulo';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['texto', 'imagen'], 'string'],
            [['titulo'], 'string', 'max' => 256],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'titulo' => 'Titulo',
            'texto' => 'Texto',
            'imagen' => 'Imagen',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReactivos()
    {
        return $this->hasMany(Reactivo::className(), ['articulo_id' => 'id']);
    }
}
