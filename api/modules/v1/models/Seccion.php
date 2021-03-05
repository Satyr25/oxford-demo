<?php

namespace api\modules\v1\models;

use Yii;

/**
 * This is the model class for table "seccion".
 *
 * @property int $id
 * @property int $puntos_seccion
 * @property string $duracion
 * @property int $tipo_seccion_id
 * @property int $examen_id
 *
 * @property Reactivo[] $reactivos
 * @property TipoSeccion $tipoSeccion
 * @property Examen $examen
 */
class Seccion extends \yii\db\ActiveRecord
{
    public $clave;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'seccion';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['puntos_seccion', 'tipo_seccion_id', 'examen_id'], 'integer'],
            [['tipo_seccion_id'], 'required'],
            [['duracion'], 'string', 'max' => 45],
            [['tipo_seccion_id'], 'exist', 'skipOnError' => true, 'targetClass' => TipoSeccion::className(), 'targetAttribute' => ['tipo_seccion_id' => 'id']],
            [['examen_id'], 'exist', 'skipOnError' => true, 'targetClass' => Examen::className(), 'targetAttribute' => ['examen_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'puntos_seccion' => 'Puntos Seccion',
            'duracion' => 'Duracion',
            'tipo_seccion_id' => 'Tipo Seccion ID',
            'examen_id' => 'Examen ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReactivos()
    {
        return $this->hasMany(Reactivo::className(), ['seccion_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTipoSeccion()
    {
        return $this->hasOne(TipoSeccion::className(), ['id' => 'tipo_seccion_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getExamen()
    {
        return $this->hasOne(Examen::className(), ['id' => 'examen_id']);
    }

    public function getReactivosActivos(){
        return Reactivo::find()->where('status = 1 AND seccion_id = '.$this->id)->all();
    }

    public function puntos($examen){
        $secciones = $this->find()
            ->select(['seccion.*','tipo_seccion.clave'])
            ->join('INNER JOIN','tipo_seccion','seccion.tipo_seccion_id = tipo_seccion.id')
            ->where('examen_id ='.$examen)
            ->all();
        $puntos_seccion = array();
        foreach($secciones as $puntos){
            $puntos_seccion[$puntos->clave] = $puntos->puntos_seccion;
        }
        return $puntos_seccion;
    }
}
