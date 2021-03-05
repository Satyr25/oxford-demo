<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "ciclo_escolar".
 *
 * @property int $id
 * @property string $nombre
 * @property int $status
 *
 * @property Grupo[] $grupos
 */
class CicloEscolar extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ciclo_escolar';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nombre', 'status'], 'required'],
            [['status'], 'integer'],
            [['nombre'], 'string', 'max' => 45],
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
            'status' => 'Status',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGrupos()
    {
        return $this->hasMany(Grupo::className(), ['ciclo_escolar_id' => 'id']);
    }
}

