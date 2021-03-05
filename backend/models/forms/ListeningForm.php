<?php
namespace backend\models\forms;

use Yii;
use yii\base\Model;
use app\models\Alumno;
use app\models\NivelAlumno;
use app\models\AlumnoExamen;
use app\models\Examen;
use app\models\Reactivo;
use app\models\TipoReactivo;
use app\models\Respuesta;
use app\models\RespuestaColumn;
use app\models\EnunciadoColumn;
use app\models\Audio;
use app\models\Seccion;
use app\models\TipoSeccion;
use app\models\RespuestaCompletar;
use common\models\User;


use yii\web\UploadedFile;
/**
 * ContactForm is the model behind the contact form.
 */
class ListeningForm extends Model
{
    public $instrucciones;
    public $nivel;
    public $examen;
    public $puntos;
    public $pregunta;
    public $correctosMul;
    public $correctosCam;
    public $respuestasMultiple;
    public $respuestasCampo;
    public $respuestasColumna;
    public $enunciados;
    public $tipos;
    public $nombre;
    public $audio;
    public $audio_guardado;
    public $general_instructions;
    public $respuestasCompletar;

    private $transaction;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            // [['nombre', 'apellidos', 'email', 'status', 'nivel'], 'required'],
            [['examen','pregunta', 'puntos'], 'required'],
            [['examen', 'puntos', 'audio_guardado'], 'integer'],
            ['pregunta', 'each', 'rule' => ['string']],
            ['instrucciones', 'each', 'rule' => ['string']],
            ['respuestasMultiple', 'each', 'rule' => ['string']],
            ['respuestasCampo', 'each', 'rule' => ['string']],
            ['respuestasCompletar', 'each', 'rule' => ['string']],
            ['respuestasColumna', 'each', 'rule' => ['string']],
            ['enunciados', 'each', 'rule' => ['string']],
            ['tipos', 'each', 'rule' => ['string']],
            ['correctosMul', 'each', 'rule' => ['integer']],
            ['correctosCam', 'each', 'rule' => ['integer']],
            [['nombre', 'general_instructions'], 'string'],
            [['audio'], 'file', 'extensions' => 'mp3'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'nivel' => 'Level',
            'examen' => 'Exam',
            'puntos' => 'Points per Question',
            'pregunta' => 'Question',
            'instrucciones' => 'Instructions',
            'correcto' => '',
            'correctosMul' => '',
            'correctosCam' => '',
            'nombre'=>'Title',
            'respuestasMultiple' => 'Answer',
            'respuestasCampo' => 'Answer',
            'respuestasColumna' => 'Answer',
            'enunciados' => 'Sentence',
            'tipos' => 'Question Type',
            'audio_guardado' => 'Audio'
        ];
    }

    public function guardar()
    {
        $connection = \Yii::$app->db;
        $this->transaction = $connection->beginTransaction();

        $user = User::find()->where('user.id=' . Yii::$app->user->getId())->one();

        $tipo_seccion = TipoSeccion::find()->where('tipo_seccion.clave="LIS"')->one();
        $seccion = Seccion::find()->where('examen_id='.$this->examen.' AND tipo_seccion_id ='.$tipo_seccion->id)->one();

        if(!$seccion){
            $seccion = new Seccion();
            $seccion->examen_id = $this->examen;
            $tipo_seccion = TipoSeccion::find()->where('tipo_seccion.clave="LIS"')->one();
            $seccion->tipo_seccion_id = $tipo_seccion->id;
            $seccion->instrucciones_generales = $this->general_instructions;
            if (!$seccion->save()) {
                $this->transaction->rollback();
                return false;
            }
        }

        if(!$this->audio_guardado){
            $audio = new Audio;
            $audio->nombre = $this->nombre;
            if (!$this->guardaAudio($audio)) {
                $this->transaction->rollback();
                return false;
            }
            if (!$audio->save()) {
                $this->transaction->rollback();
                return false;
            }
            $audio_id = $audio->id;
        }else {
            $audio_id = $this->audio_guardado;
        }

        $indicePregunta = 0;
        $indiceRespMul = 0;
        $indiceRespCol = 0;

        foreach ($this->tipos as $tipo) {
            if ($tipo == 'MUL') {
                $reactivoMul = new Reactivo();
                $reactivoMul->pregunta = $this->pregunta[$indicePregunta];
                $reactivoMul->status = 1;
                $reactivoMul->puntos = $this->puntos;
                $reactivoMul->instrucciones = $this->instrucciones[$indicePregunta];
                $tipoPregunta = TipoReactivo::find()->where('tipo_reactivo.clave="' . $tipo . '"')->one();
                $reactivoMul->tipo_reactivo_id = $tipoPregunta->id;
                $reactivoMul->seccion_id = $seccion->id;
                $reactivoMul->user_id = $user->id;
                $reactivoMul->audio_id = $audio_id;
                if (!$reactivoMul->save()) {
                    $this->transaction->rollback();
                    return false;
                }

                for ($i = 0; $i < 3; $i++) {
                    $resp = new Respuesta();
                    $resp->respuesta = $this->respuestasMultiple[$indiceRespMul];
                    switch ($i) {
                        case 0:
                            $resp->correcto = ($this->correctosMul[$indicePregunta] == 'a' ? 1 : 0);
                            break;
                        case 1:
                            $resp->correcto = ($this->correctosMul[$indicePregunta] == 'b' ? 1 : 0);
                            break;
                        case 2:
                            $resp->correcto = ($this->correctosMul[$indicePregunta] == 'c' ? 1 : 0);
                            break;
                    }
                    $resp->reactivo_id = $reactivoMul->id;
                    if (!$resp->save()) {
                        var_dump("hola");
                        exit;

                        $this->transaction->rollback();
                        return false;
                    }
                    $indiceRespMul++;
                }
                $indicePregunta++;
                $indiceRespCol = $indiceRespCol + 5;
            } else if ($tipo == 'REL') {
                $reactivoCol = new Reactivo();
                $reactivoCol->status = 1;
                $reactivoCol->puntos = 10;
                $reactivoCol->pregunta = $this->pregunta[$indicePregunta];
                $reactivoCol->instrucciones = $this->instrucciones[$indicePregunta];
                $tipoPregunta = TipoReactivo::find()->where('tipo_reactivo.clave="' . $tipo . '"')->one();
                $reactivoCol->tipo_reactivo_id = $tipoPregunta->id;
                $reactivoCol->seccion_id = $seccion->id;
                $reactivoCol->user_id = $user->id;
                $reactivoCol->audio_id = $audio_id;
                if (!$reactivoCol->save()) {
                    $this->transaction->rollback();
                    return false;
                }
                for ($i = 0; $i < 5; $i++) {
                    $respuestaCol = new RespuestaColumn();
                    $respuestaCol->respuesta = $this->respuestasColumna[$indiceRespCol];
                    $respuestaCol->reactivo_id = $reactivoCol->id;
                    if (!$respuestaCol->save()) {
                        $this->transaction->rollback();
                        return false;
                    }

                    $enunciadoCol = new EnunciadoColumn();
                    $enunciadoCol->reactivo_id = $reactivoCol->id;
                    $enunciadoCol->enunciado = $this->enunciados[$indiceRespCol];
                    $enunciadoCol->respuesta_column_id = $respuestaCol->id;
                    if (!$enunciadoCol->save()) {
                        $this->transaction->rollback();
                        return false;
                    }
                    $indiceRespCol++;
                }
                $indicePregunta++;
                $indiceRespMul = $indiceRespMul + 3;
            }else if($tipo == 'COM'){
                $reactivo = new Reactivo();
                $reactivo->status = 1;
                $reactivo->puntos = $this->puntos;
                $reactivo->pregunta = $this->pregunta[$indicePregunta];
                $reactivo->instrucciones = $this->instrucciones[$indicePregunta];
                $tipoPregunta = TipoReactivo::find()->where('tipo_reactivo.clave="'.$tipo.'"')->one();
                $reactivo->tipo_reactivo_id = $tipoPregunta->id;
                $reactivo->seccion_id = $seccion->id;
                $reactivo->user_id = $user->id;
                if(!$reactivo->save())
                {
                    $this->transaction->rollback();
                    return false;
                }
                $respuesta = new RespuestaCompletar();
                $respuesta->respuesta = implode('|',$this->respuestasCompletar[$indicePregunta]);
                $respuesta->reactivo_id = $reactivo->id;
                if(!$respuesta->save()){
                    $this->transaction->rollback();
                    return false;
                }

                $indicePregunta++;
            }

        }

        $puntos_totales = 0;
        foreach ($seccion->reactivosActivos() as $reactivo) {
            $puntos_totales = $reactivo->puntos + $puntos_totales;
        }

        $seccion->puntos_seccion = $puntos_totales;
        $seccion->update();

        $examen = Examen::findOne($this->examen);
        if($examen->tipoExamen->clave == 'CER' && $examen->certificate_v2 == 1){
            if(!$examen->actualizarTotales()){
                $this->transaction->rollback();
                return false;
            }
        }

        $this->transaction->commit();
        return true;
    }

    public function guardaAudio($audioObj)
    {
        $audio = UploadedFile::getInstance($this, 'audio');
        // var_dump($audio);exit;
        $ruta = Yii::getAlias('@backend/web/audios');
        $ruta_frontend = Yii::getAlias('@frontend/web/audios');
        if (!file_exists($ruta)) {
            if (!mkdir($ruta)) {
                return false;
            }
        }

        if (!file_exists($ruta_frontend)) {
            if (!mkdir($ruta_frontend)) {
                return false;
            }
        }

        $timestamp = time();
        $nombre_archivo = $timestamp . preg_replace("/[^a-z0-9\.]/", "", strtolower($audio->name));
            // var_dump($audio->name);exit;

        if (!file_exists($ruta . '/' . $nombre_archivo)) {
            if (!$audio->saveAs($ruta . '/' . $nombre_archivo, false)) {
                // var_dump($ruta . '/' . $nombre_archivo);
                exit;
                return false;
            }
            if (!$audio->saveAs($ruta_frontend . '/' . $nombre_archivo, true)) {
                // var_dump("hola");
                exit;
                return false;
            }
        }

        $audioObj->audio = 'audios/' . $nombre_archivo;
            // var_dump("hola");exit;
        return true;
    }
}
