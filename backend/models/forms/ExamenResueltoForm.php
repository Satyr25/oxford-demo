<?php
namespace backend\models\forms;

use common\models\User;
use app\models\Examen;
use app\models\AluexaReactivos;
use app\models\Reactivo;

use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class ExamenResueltoForm extends Model
{
    public $id;
    public $preguntas;
    public $respuestasMul;
    public $enunciadosCol;
    public $respuestasCol;
    public $respuestasCom;

    public $respuestasMulGuard;
    public $enunciadosColGuard;
    public $respuestasComGuard;
    public $preguntasGuard;
    public $error;
    private $transaction;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['id', 'integer'],
            ['preguntas', 'each', 'rule' => ['integer']],
            ['respuestasMul', 'each', 'rule' => ['integer']],
            ['respuestasCol', 'each', 'rule' => ['integer']],
            ['enunciadosCol', 'each', 'rule' => ['integer']],
            ['respuestasCom', 'each', 'rule' => ['string']],
        ];
    }

    public function attributeLabels()
    {
        return [
        ];
    }

    public function guardarRespuestas(){
        if(!$this->preguntas){
            $guardadas = AluexaReactivos::find()->where('alumno_examen_id = '.$this->id)->count();
            if($guardadas > 0){
                return true;
            }
            $this->error = "No se enviaron preguntas y no hay respuestas guardadas";
            return false;
        }
        $connection = \Yii::$app->db;
        $this->transaction = $connection->beginTransaction();
        $indiceMul = 0;
        $indiceCol = 0;
        $indiceCom = 0;

        foreach($this->preguntas as $pregunta){
            $reactivo = Reactivo::findOne($pregunta);
            $guardado = false;

            if($reactivo->tipoReactivo->clave == 'MUL'){
                $repetida = AluexaReactivos::find()->where('alumno_examen_id='.$this->id.' AND reactivo_id='.$reactivo->id)->all();
                if(count($repetida)){
                    $guardado = true;
                }else{
                    $respuestas = $reactivo->respuestas;
                    foreach($respuestas as $respuesta){
                        if (!isset($this->respuestasMul[$indiceMul])){
                            break;
                        }
                        if($this->respuestasMul[$indiceMul] == $respuesta->id){
                            $almacena = new AluexaReactivos;
                            $almacena->alumno_examen_id = $this->id;
                            $almacena->reactivo_id = $reactivo->id;
                            $almacena->respuesta_alu = $this->respuestasMul[$indiceMul];
                            if(!$almacena->save()){
                                foreach ($almacena->getErrors() as $errors) {
                                    foreach ($errors as $error) {
                                        $this->error .= $error.', ';
                                    }
                                }
                                return false;
                            }
                            $indiceMul++;
                            $guardado = true;
                        }
                    }
                }

                if(!$guardado){
                    $almacena = new AluexaReactivos;
                    $almacena->alumno_examen_id = $this->id;
                    $almacena->reactivo_id = $reactivo->id;
                    if (!$almacena->save()) {
                        foreach ($almacena->getErrors() as $errors) {
                            foreach ($errors as $error) {
                                $this->error .= $error.', ';
                            }
                        }
                        return false;
                    }
                }
            }else if ($reactivo->tipoReactivo->clave == 'REL'){
                for ($i = 0; $i < 5; $i++) {
                    $almacena = new AluexaReactivos;
                    $almacena->alumno_examen_id = $this->id;
                    $almacena->reactivo_id = $reactivo->id;
                    $almacena->respuesta_alu = $this->respuestasCol[$indiceCol];
                    $almacena->enunciado_alu = $this->enunciadosCol[$indiceCol];
                    if (!$almacena->save()) {
                        foreach ($almacena->getErrors() as $errors) {
                            foreach ($errors as $error) {
                                $this->error .= $error.', ';
                            }
                        }
                        return false;
                    }
                    $indiceCol++;
                }
            }else if ($reactivo->tipoReactivo->clave == 'COM'){
                if(AluexaReactivos::find()->where('alumno_examen_id = '.$this->id.' AND reactivo_id = '.$reactivo->id)->count()){
                    continue;
                }
                $almacena = new AluexaReactivos;
                $almacena->alumno_examen_id = $this->id;
                $almacena->reactivo_id = $reactivo->id;
                $almacena->respuesta_completar = $this->respuestasCom[$indiceCom];
                if (!$almacena->save()) {
                    foreach ($almacena->getErrors() as $errors) {
                        foreach ($errors as $error) {
                            $this->error .= $error.', ';
                        }
                    }
                    return false;
                }
                $indiceCom++;
            } else {
                $this->error = 'Reactivo de categoria desconocida';
            }
        }

        $this->transaction->commit();
        return true;
    }

    public function cargaRespuestas($respuestas){
        $this->preguntasGuard = [];
        $this->respuestasMulGuard = [];
        $this->respuestasComGuard = [];

        foreach($respuestas as $respuesta)
        {
            switch($respuesta->reactivo->tipoReactivo->clave){
                case 'MUL':
                    array_push($this->preguntasGuard, $respuesta->reactivo_id);
                    array_push($this->respuestasMulGuard, $respuesta->respuesta_alu);
                break;
                case 'COM':
                    $this->respuestasComGuard[$respuesta->reactivo_id] = $respuesta->respuesta_completar;
                break;
                case 'REL':
                break;
            }
        }
    }
}
