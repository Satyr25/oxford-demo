<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tipo_examen".
 *
 * @property int $id
 * @property string $nombre
 * @property string $clave
 *
 * @property Examen[] $examens
 */
class TipoExamen extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tipo_examen';
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
    public function getExamens()
    {
        return $this->hasMany(Examen::className(), ['tipo_examen_id' => 'id']);
    }
}
