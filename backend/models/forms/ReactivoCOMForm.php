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
class ReactivoCOMForm extends Model
{
    public $id_pregunta;
    public $pregunta;
    public $tipo;
    public $enunciados;
    public $enunciados_id;
    public $respuestas;
    public $respuestas_guardadas;
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
            [['id_pregunta', 'pregunta', 'enunciados', 'enunciados_id', 'respuestas', 'respuestas_id'], 'required'],
            [['id_pregunta', 'respuestas_id', 'enunciados_id'], 'integer'],
            [['enunciados'], 'each', 'rule' => ['string']],
            [['respuestas'], 'each', 'rule' => ['string']],
            [['pregunta', 'instrucciones'], 'string'],
            [['reading', 'listening'], 'integer']
        ];
    }

    public function attributeLabels()
    {
        return [
            'pregunta' => 'Question',
            'instrucciones' => 'Instructions'
        ];
    }

    public function cargar($id)
    {
        $reactivo = Reactivo::findOne($id);
        $this->pregunta = $reactivo->pregunta;
        $this->id_pregunta = $reactivo->id;
        $this->instrucciones = $reactivo->instrucciones;
        $respuestas = $reactivo->respuestasCompletar;
        $this->respuestas_guardadas = explode('|',$respuestas[0]->respuesta);
    }

    public function actualizar()
    {
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
        if (!$reactivo->save()) {
            return false;
        }
        $nuevas_respuestas = [];
        foreach($this->respuestas as $respuesta){
            if(trim($respuesta) != ''){
                $nuevas_respuestas[] = trim($respuesta);
            }
        }
        $respuestas = $reactivo->respuestasCompletar;
        $respuestas = $respuestas[0];
        $respuestas->respuesta = implode('|',$nuevas_respuestas);
        if(!$respuestas->save()){
            return false;
        }
        return true;
    }
}
