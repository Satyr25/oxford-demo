<?php
namespace backend\models\forms;

use app\models\Reactivo;
use app\models\EnunciadoColumn;
use app\models\RespuestaColumn;
use app\models\Articulo;
use app\models\Audio;
use yii\helpers\ArrayHelper;

use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class ReactivoRELForm extends Model
{
    public $id_pregunta;
    public $pregunta;
    public $tipo;
    public $enunciados;
    public $enunciados_id;
    public $respuestas;
    public $respuestas_id;
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
            [['id_pregunta','pregunta','enunciados','enunciados_id','respuestas','respuestas_id'], 'required'],
            [['id_pregunta', 'respuestas_id', 'enunciados_id'], 'integer'],
            [['enunciados'], 'each', 'rule' => ['string']],
            [['respuestas'], 'each', 'rule' => ['string']],
            [['pregunta','instrucciones'],'string'],
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
        $enunciados = EnunciadoColumn::find()->where('reactivo_id='.$id)->all();
        foreach($enunciados as $i => $enunciado){
            $respuesta = RespuestaColumn::findOne($enunciado->respuesta_column_id);
            $this->enunciados_id[$i] = $enunciado->id;
            $this->enunciados[$i] = $enunciado->enunciado;
            $this->respuestas_id[$i] = $respuesta->id;
            $this->respuestas[$i] = $respuesta->respuesta;
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
            $this->actualizarReactivo($reactivo);
        }
        return true;
    }

    private function actualizarReactivo($reactivo){
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
        foreach ($this->enunciados_id as $i => $enunciado_id) {
            $enunciado = EnunciadoColumn::findOne($enunciado_id);
            $enunciado->enunciado = $this->enunciados[$i];
            if(!$enunciado->save()){
                return false;
            }
            $respuesta = RespuestaColumn::findOne($this->respuestas_id[$i]);
            $respuesta->respuesta = $this->respuestas[$i];
            if(!$respuesta->save()){
                return false;
            }
        }
        return true;
    }
}
