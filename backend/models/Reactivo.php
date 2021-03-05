<?php

namespace app\models;

use Yii;
use common\models\User;
use app\models\ImagenReactivo;

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
 * @property int $reactivo_id
 *
 * @property AluexaReactivos[] $aluexaReactivos
 * @property EnunciadoColumn[] $enunciadoColumns
 * @property Articulo $articulo
 * @property Audio $audio
 * @property Reactivo $reactivo
 * @property Reactivo[] $reactivos
 * @property Seccion $seccion
 * @property TipoReactivo $tipoReactivo
 * @property User $user
 * @property Respuesta[] $respuestas
 * @property RespuestaCompletar[] $respuestas_completar
 * @property RespuestaColumn[] $respuestaColumns
 * @property WritingData[] $writingDatas
 */
class Reactivo extends \yii\db\ActiveRecord
{
    public $nivel;
    public $variante;
    public $clave_reactivo;
    public $tipo_reactivo;
    public $seccion_nombre;
    public $respuesta_id;

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
            [['reactivo_id'], 'exist', 'skipOnError' => true, 'targetClass' => Reactivo::className(), 'targetAttribute' => ['reactivo_id' => 'id']],
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

    public function porTipoExamen($tipo){
        if($tipo == 'CERV2'){
            return $this->find()
                ->select([
                    'reactivo.id', 'reactivo.pregunta', 'reactivo.puntos AS puntos',
                    'nivel_alumno.clave AS nivel', 'variante.nombre AS variante',
                    'tipo_reactivo.clave AS clave_reactivo', 'tipo_reactivo.nombre AS tipo_reactivo',
                    'tipo_seccion.nombre AS seccion_nombre', 'respuesta.id AS respuesta_id'
                ])
                ->join('INNER JOIN', 'tipo_reactivo', 'tipo_reactivo.id = reactivo.tipo_reactivo_id')
                ->join('INNER JOIN', 'seccion', 'seccion.id = reactivo.seccion_id')
                ->join('INNER JOIN', 'tipo_seccion', 'tipo_seccion.id = seccion.tipo_seccion_id')
                ->join('INNER JOIN', 'examen', 'examen.id = seccion.examen_id')
                ->join('INNER JOIN', 'tipo_examen', 'tipo_examen.id = examen.tipo_examen_id')
                ->join('INNER JOIN', 'nivel_alumno', 'nivel_alumno.id = examen.nivel_alumno_id')
                ->join('INNER JOIN', 'variante', 'variante.id = examen.variante_id')
                ->join('LEFT JOIN', 'respuesta', 'respuesta.reactivo_id = reactivo.id AND respuesta.correcto=1')
                ->where('reactivo.status = 1 AND examen.certificate_v2 = 1 AND examen.status = 1 AND tipo_examen.clave ="CER" AND tipo_seccion.clave!="WRI"')
                ->all();
        }else if($tipo == 'CER'){
            return $this->find()
                ->select([
                    'reactivo.id', 'reactivo.pregunta', 'reactivo.puntos AS puntos',
                    'nivel_alumno.clave AS nivel', 'variante.nombre AS variante',
                    'tipo_reactivo.clave AS clave_reactivo', 'tipo_reactivo.nombre AS tipo_reactivo',
                    'tipo_seccion.nombre AS seccion_nombre', 'respuesta.id AS respuesta_id'
                ])
                ->join('INNER JOIN', 'tipo_reactivo', 'tipo_reactivo.id = reactivo.tipo_reactivo_id')
                ->join('INNER JOIN', 'seccion', 'seccion.id = reactivo.seccion_id')
                ->join('INNER JOIN', 'tipo_seccion', 'tipo_seccion.id = seccion.tipo_seccion_id')
                ->join('INNER JOIN', 'examen', 'examen.id = seccion.examen_id')
                ->join('INNER JOIN', 'tipo_examen', 'tipo_examen.id = examen.tipo_examen_id')
                ->join('INNER JOIN', 'nivel_alumno', 'nivel_alumno.id = examen.nivel_alumno_id')
                ->join('INNER JOIN', 'variante', 'variante.id = examen.variante_id')
                ->join('LEFT JOIN', 'respuesta', 'respuesta.reactivo_id = reactivo.id AND respuesta.correcto=1')
                ->where('reactivo.status = 1 AND examen.certificate_v2 IS NULL AND examen.status = 1 AND tipo_examen.clave ="CER" AND tipo_seccion.clave!="WRI"')
                ->all();
        }else if($tipo == 'DIAV2'){
            return $this->find()
                ->select([
                    'reactivo.id', 'reactivo.pregunta', 'reactivo.puntos AS puntos',
                    'nivel_alumno.clave AS nivel', 'variante.nombre AS variante',
                    'tipo_reactivo.clave AS clave_reactivo', 'tipo_reactivo.nombre AS tipo_reactivo',
                    'tipo_seccion.nombre AS seccion_nombre', 'respuesta.id AS respuesta_id'
                ])
                ->join('INNER JOIN', 'tipo_reactivo', 'tipo_reactivo.id = reactivo.tipo_reactivo_id')
                ->join('INNER JOIN', 'seccion', 'seccion.id = reactivo.seccion_id')
                ->join('INNER JOIN', 'tipo_seccion', 'tipo_seccion.id = seccion.tipo_seccion_id')
                ->join('INNER JOIN', 'examen', 'examen.id = seccion.examen_id')
                ->join('INNER JOIN', 'tipo_examen', 'tipo_examen.id = examen.tipo_examen_id')
                ->join('INNER JOIN', 'nivel_alumno', 'nivel_alumno.id = examen.nivel_alumno_id')
                ->join('INNER JOIN', 'variante', 'variante.id = examen.variante_id')
                ->join('LEFT JOIN', 'respuesta', 'respuesta.reactivo_id = reactivo.id AND respuesta.correcto=1')
                ->where('reactivo.status = 1 AND examen.diagnostic_v2 = 1 AND examen.status = 1 AND tipo_examen.clave ="DIA" AND tipo_seccion.clave!="WRI"')
                ->all();
        }else if($tipo == 'DIA'){
            return $this->find()
                ->select([
                    'reactivo.id', 'reactivo.pregunta', 'reactivo.puntos AS puntos',
                    'nivel_alumno.clave AS nivel', 'variante.nombre AS variante',
                    'tipo_reactivo.clave AS clave_reactivo', 'tipo_reactivo.nombre AS tipo_reactivo',
                    'tipo_seccion.nombre AS seccion_nombre', 'respuesta.id AS respuesta_id'
                ])
                ->join('INNER JOIN', 'tipo_reactivo', 'tipo_reactivo.id = reactivo.tipo_reactivo_id')
                ->join('INNER JOIN', 'seccion', 'seccion.id = reactivo.seccion_id')
                ->join('INNER JOIN', 'tipo_seccion', 'tipo_seccion.id = seccion.tipo_seccion_id')
                ->join('INNER JOIN', 'examen', 'examen.id = seccion.examen_id')
                ->join('INNER JOIN', 'tipo_examen', 'tipo_examen.id = examen.tipo_examen_id')
                ->join('INNER JOIN', 'nivel_alumno', 'nivel_alumno.id = examen.nivel_alumno_id')
                ->join('INNER JOIN', 'variante', 'variante.id = examen.variante_id')
                ->join('LEFT JOIN', 'respuesta', 'respuesta.reactivo_id = reactivo.id AND respuesta.correcto=1')
                ->where('reactivo.status = 1 AND examen.diagnostic_v2 = 0 AND examen.status = 1 AND tipo_examen.clave ="DIA" AND tipo_seccion.clave!="WRI"')
                ->all();
        }else{
            return $this->find()
                ->select([
                    'reactivo.id', 'reactivo.pregunta', 'reactivo.puntos AS puntos',
                    'nivel_alumno.clave AS nivel', 'variante.nombre AS variante',
                    'tipo_reactivo.clave AS clave_reactivo', 'tipo_reactivo.nombre AS tipo_reactivo',
                    'tipo_seccion.nombre AS seccion_nombre', 'respuesta.id AS respuesta_id'
                ])
                ->join('INNER JOIN', 'tipo_reactivo', 'tipo_reactivo.id = reactivo.tipo_reactivo_id')
                ->join('INNER JOIN', 'seccion', 'seccion.id = reactivo.seccion_id')
                ->join('INNER JOIN', 'tipo_seccion', 'tipo_seccion.id = seccion.tipo_seccion_id')
                ->join('INNER JOIN', 'examen', 'examen.id = seccion.examen_id')
                ->join('INNER JOIN', 'tipo_examen', 'tipo_examen.id = examen.tipo_examen_id')
                ->join('INNER JOIN', 'nivel_alumno', 'nivel_alumno.id = examen.nivel_alumno_id')
                ->join('INNER JOIN', 'variante', 'variante.id = examen.variante_id')
                ->join('LEFT JOIN', 'respuesta', 'respuesta.reactivo_id = reactivo.id AND respuesta.correcto=1')
                ->where('reactivo.status = 1 AND examen.status = 1 AND tipo_examen.clave ="'.$tipo.'" AND tipo_seccion.clave!="WRI"')
                ->all();
        }
    }

    public function imagenes(){
        $imagenes = ImagenReactivo::find()->where(['reactivo_id' => $this->id])->all();
        return $imagenes;
    }
}
