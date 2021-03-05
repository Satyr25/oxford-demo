<?php
namespace backend\models\forms;

use Yii;
use yii\base\Model;

use app\models\Seccion;
use app\models\Examen;
use app\models\TipoSeccion;
use app\models\Reactivo;
use app\models\Respuesta;

/**
 * ContactForm is the model behind the contact form.
 */
class ReadingMockForm extends Model
{
    public $seccion;
    public $examen;
    private $transaction;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['seccion', 'examen'], 'required'],
            [['seccion', 'examen'], 'integer'],
        ];
    }

    public function guardar(){
        $connection = \Yii::$app->db;
        $transaction = $connection->beginTransaction();

        $seccion = Seccion::findOne($this->seccion);
        $examen = Examen::findOne($this->examen);
        $tipoSeccion = TipoSeccion::find()->where(['clave'=>'REA'])->one();
        $counter = 0;
        foreach($examen->seccions as $seccionRea){
            if($seccionRea->tipoSeccion->clave == 'REA'){
                $counter++;
            }
        }
        if($counter >= 2){
            return false;
        }

        $nuevaSeccion = new Seccion();
        $nuevaSeccion->examen_id = $examen->id;
        $nuevaSeccion->tipo_seccion_id = $tipoSeccion->id;
        $nuevaSeccion->puntos_seccion = $seccion->puntos_seccion;
        $nuevaSeccion->duracion = $seccion->duracion;
        if(!$nuevaSeccion->save()){
            $transaction->rollback();
            return false;
        }

        foreach($seccion->reactivosActivos() as $reactivo){
            $nuevoReactivo = new Reactivo();
            $nuevoReactivo->pregunta = $reactivo->pregunta;
            $nuevoReactivo->instrucciones = $reactivo->instrucciones;
            $nuevoReactivo->puntos = $reactivo->puntos;
            $nuevoReactivo->status = $reactivo->status;
            $nuevoReactivo->tipo_reactivo_id = $reactivo->tipo_reactivo_id;
            $nuevoReactivo->articulo_id = $reactivo->articulo_id;
            $nuevoReactivo->user_id = $reactivo->user_id;
            $nuevoReactivo->seccion_id = $nuevaSeccion->id;
            if(!$nuevoReactivo->save()){
                $transaction->rollback();
                return false;
            }

            foreach($reactivo->respuestas as $respuesta){
                $nuevaRespuesta = new Respuesta();
                $nuevaRespuesta->respuesta = $respuesta->respuesta;
                $nuevaRespuesta->correcto = $respuesta->correcto;
                $nuevaRespuesta->reactivo_id = $nuevoReactivo->id;
                if(!$nuevaRespuesta->save()){
                    $transaction->rollback();
                    return false;
                }
            }
        }
        $transaction->commit();
        return true;
    }

}
