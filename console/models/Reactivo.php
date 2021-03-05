<?php

namespace app\models;

use Yii;
use common\models\User;

/**
 * This is the model class for table "reactivo".
 *
 * @property int $id
 * @property string $pregunta
 * @property string $instrucciones
 * @property int $puntos
 * @property int $tipo_reactivo_id
 * @property int $articulo_id
 * @property int $audio_id
 * @property int $user_id
 * @property int $seccion_id
 *
 * @property AluexaReactivos[] $aluexaReactivos
 * @property EnunciadoColumn[] $enunciadoColumns
 * @property Articulo $articulo
 * @property Audio $audio
 * @property Seccion $seccion
 * @property TipoReactivo $tipoReactivo
 * @property User $user
 * @property Respuesta[] $respuestas
 * @property RespuestaCompletar[] $respuestas_completar
 * @property RespuestaColumn[] $respuestaColumns
 */
class Reactivo extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'reactivo';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['puntos', 'tipo_reactivo_id', 'articulo_id', 'audio_id', 'user_id', 'seccion_id','status'], 'integer'],
            [['pregunta', 'instrucciones'], 'string', 'max' => 512],
            [['articulo_id'], 'exist', 'skipOnError' => true, 'targetClass' => Articulo::className(), 'targetAttribute' => ['articulo_id' => 'id']],
            [['audio_id'], 'exist', 'skipOnError' => true, 'targetClass' => Audio::className(), 'targetAttribute' => ['audio_id' => 'id']],
            [['seccion_id'], 'exist', 'skipOnError' => true, 'targetClass' => Seccion::className(), 'targetAttribute' => ['seccion_id' => 'id']],
            [['tipo_reactivo_id'], 'exist', 'skipOnError' => true, 'targetClass' => TipoReactivo::className(), 'targetAttribute' => ['tipo_reactivo_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'pregunta' => 'Pregunta',
            'instrucciones' => 'Instrucciones',
            'puntos' => 'Puntos',
            'tipo_reactivo_id' => 'Tipo Reactivo ID',
            'articulo_id' => 'Articulo ID',
            'audio_id' => 'Audio ID',
            'user_id' => 'User ID',
            'seccion_id' => 'Seccion ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAluexaReactivos()
    {
        return $this->hasMany(AluexaReactivos::className(), ['reactivo_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEnunciadoColumns()
    {
        return $this->hasMany(EnunciadoColumn::className(), ['reactivo_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getArticulo()
    {
        return $this->hasOne(Articulo::className(), ['id' => 'articulo_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAudio()
    {
        return $this->hasOne(Audio::className(), ['id' => 'audio_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSeccion()
    {
        return $this->hasOne(Seccion::className(), ['id' => 'seccion_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTipoReactivo()
    {
        return $this->hasOne(TipoReactivo::className(), ['id' => 'tipo_reactivo_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRespuestas()
    {
        return $this->hasMany(Respuesta::className(), ['reactivo_id' => 'id']);
    }

    public function getRespuestasCompletar()
    {
        return $this->hasMany(RespuestaCompletar::className(), ['reactivo_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRespuestaColumns()
    {
        return $this->hasMany(RespuestaColumn::className(), ['reactivo_id' => 'id']);
    }
}
