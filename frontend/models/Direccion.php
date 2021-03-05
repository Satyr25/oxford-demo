<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "direccion".
 *
 * @property int $id
 * @property string $calle
 * @property string $numero_int
 * @property string $numero_ext
 * @property string $codigo_postal
 * @property string $colonia
 * @property string $municipio
 * @property string $ciudad
 * @property int $pais_id
 * @property int $estado_id
 *
 * @property Estado $estado
 * @property Pais $pais
 * @property Instituto[] $institutos
 */
class Direccion extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'direccion';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pais_id', 'estado_id'], 'required'],
            [['pais_id', 'estado_id'], 'integer'],
            [['calle'], 'string', 'max' => 256],
            [['numero_int', 'numero_ext', 'codigo_postal', 'colonia', 'municipio', 'ciudad'], 'string', 'max' => 45],
            [['estado_id'], 'exist', 'skipOnError' => true, 'targetClass' => Estado::className(), 'targetAttribute' => ['estado_id' => 'id']],
            [['pais_id'], 'exist', 'skipOnError' => true, 'targetClass' => Pais::className(), 'targetAttribute' => ['pais_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'calle' => 'Street',
            'numero_int' => 'Int. No.',
            'numero_ext' => 'Ext. No.',
            'codigo_postal' => 'Zip Code',
            'colonia' => 'District',
            'municipio' => 'Municipality',
            'ciudad' => 'City',
            'pais_id' => 'Country',
            'estado_id' => 'State',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEstado()
    {
        return $this->hasOne(Estado::className(), ['id' => 'estado_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPais()
    {
        return $this->hasOne(Pais::className(), ['id' => 'pais_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInstitutos()
    {
        return $this->hasMany(Instituto::className(), ['direccion_id' => 'id']);
    }
}
