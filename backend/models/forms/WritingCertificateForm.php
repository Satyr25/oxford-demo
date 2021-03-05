<?php
namespace backend\models\forms;

use Yii;
use yii\base\Model;

use app\models\Seccion;
use app\models\Examen;
use app\models\TipoSeccion;
use app\models\Reactivo;
use app\models\Respuesta;
use app\models\ImagenReactivo;

/**
 * ContactForm is the model behind the contact form.
 */
class WritingCertificateForm extends Model
{
    public $reactivo;
    public $examen;
    private $transaction;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['reactivo', 'examen'], 'required'],
            [['reactivo', 'examen'], 'integer'],
        ];
    }

    public function guardar(){
        $connection = \Yii::$app->db;
        $transaction = $connection->beginTransaction();

        $reactivo = Reactivo::findOne($this->reactivo);
        $examen = Examen::findOne($this->examen);
        $seccion = Seccion::find()->where('examen_id = '.$examen->id.' AND tipo_seccion_id = 4')->one();
        $counter = 0;

        $nuevoReactivo = new Reactivo();
        $nuevoReactivo->pregunta = $reactivo->pregunta;
        $nuevoReactivo->instrucciones = $reactivo->instrucciones;
        $nuevoReactivo->puntos = $reactivo->puntos;
        $nuevoReactivo->status = $reactivo->status;
        $nuevoReactivo->tipo_reactivo_id = $reactivo->tipo_reactivo_id;
        $nuevoReactivo->audio_id = $reactivo->audio_id;
        $nuevoReactivo->user_id = $reactivo->user_id;
        $nuevoReactivo->seccion_id = $seccion->id;
        $nuevoReactivo->reactivo_id = $reactivo->id;
        if(!$nuevoReactivo->save()){
            $transaction->rollback();
            return false;
        };

        $reactivo->reactivo_id = $nuevoReactivo->id;
        $reactivo->save();

        foreach($reactivo->imagenes() as $imagen){
            $imagen_reactivo = new ImagenReactivo();
            $imagen_reactivo->reactivo_id = $nuevoReactivo->id;
            $imagen_reactivo->imagen = $imagen->imagen;
            $imagen_reactivo->save();
        }
        if($examen->tipoExamen->clave == 'CER' && $examen->certificate_v2 == 1){
            if(!$examen->actualizarTotales()){
                $this->transaction->rollback();
                return false;
            }
        }
        $transaction->commit();
        return true;
    }

}
