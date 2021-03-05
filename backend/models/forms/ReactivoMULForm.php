<?php
namespace backend\models\forms;

use app\models\Reactivo;
use app\models\Respuesta;
use app\models\Articulo;
use app\models\Audio;
use yii\helpers\ArrayHelper;

use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class ReactivoMULForm extends Model
{
    public $id_pregunta;
    public $pregunta;
    public $tipo;
    public $respuestas;
    public $respuestas_id;
    public $respuestas_id_ligadas;
    public $correcta;
    public $reading;
    public $articulos;
    public $listening;
    public $audios;
    public $instrucciones;

    private $transaction;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_pregunta','pregunta'], 'required'],
            [['id_pregunta'], 'integer'],
            [['correcta'], 'integer'],
            [['pregunta','instrucciones'],'string'],
            [['respuestas'], 'each', 'rule' => ['string']],
            [['respuestas_id'], 'each', 'rule' => ['integer']],
            [['respuestas_id_ligadas'], 'each', 'rule' => ['integer']],
            [['reading','listening'], 'integer']
        ];
    }

    public function attributeLabels()
    {
        return [
            'pregunta'=>'Question',
            'instrucciones' => 'Instructions'
        ];
    }

    public function cargar($id){
        $reactivo = Reactivo::findOne($id);
        $this->pregunta = $reactivo->pregunta;
        $this->id_pregunta = $reactivo->id;
        $this->instrucciones = $reactivo->instrucciones;
        if($reactivo->articulo_id){
            $this->cargaReading($reactivo->articulo_id);
        }
        if($reactivo->audio_id){
            $this->cargaListening($reactivo->audio_id);
        }
        $respuestas = Respuesta::find()->where('reactivo_id='.$reactivo->id)->all();
        foreach ($respuestas as $respuesta) {
            $this->respuestas[] = $respuesta->respuesta;
            $this->respuestas_id[] = $respuesta->id;
            if($respuesta->correcto){
                $this->correcta = $respuesta->id;
            }
        }
        if($reactivo->reactivo_id){
            $respuestas = Respuesta::find()->where('reactivo_id='.$reactivo->reactivo_id)->all();
            foreach ($respuestas as $respuesta) {
                $this->respuestas_id_ligadas[] = $respuesta->id;
            }
        }
    }

    private function cargaReading($reading){
        $this->reading = $reading;
        $this->articulos = ArrayHelper::map(Articulo::find()->all(), 'id', 'titulo');
    }

    private function cargaListening($listening){
        $this->listening = $listening;
        $this->audios = ArrayHelper::map(Audio::find()->all(), 'id', 'nombre');
    }

    public function actualizar(){
        $reactivo = Reactivo::findOne($this->id_pregunta);
        $this->actualizarReactivo($reactivo);
        if($reactivo->reactivo_id){
            $reactivo = Reactivo::findOne($reactivo->reactivo_id);
            $this->actualizarReactivo($reactivo,true);
        }
        return true;
    }

    private function actualizarReactivo($reactivo,$ligada=false){
        $reactivo->pregunta = $this->pregunta;
        $reactivo->instrucciones = $this->instrucciones;
        if($reactivo->articulo_id && $this->reading){
            $reactivo->articulo_id = $this->reading;
        }
        if($reactivo->audio_id && $this->listening){
            $reactivo->audio_id = $this->listening;
        }
        if(!$reactivo->save()){
            return false;
        }
        foreach($this->respuestas_id as $i => $respuesta_id){
            $respuesta = Respuesta::findOne($respuesta_id);
            $respuesta->respuesta = $this->respuestas[$i];
            $respuesta->correcto = ($respuesta_id == $this->correcta ? 1 : 0);
            if(!$respuesta->save()){
                return false;
            }
            if($ligada){
                $respuesta = Respuesta::findOne($this->respuestas_id_ligadas[$i]);
                $respuesta->respuesta = $this->respuestas[$i];
                $respuesta->correcto = ($respuesta_id == $this->correcta ? 1 : 0);
                if(!$respuesta->save()){
                    return false;
                }
            }
        }
        return true;
    }
}
