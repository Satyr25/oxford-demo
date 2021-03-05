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
use common\models\User;
use app\models\AluexaReactivos;
use app\models\StatusExamen;
use app\models\TipoExamen;
use app\models\WritingData;

/**
 * ContactForm is the model behind the contact form.
 */
class WritingResueltoForm extends Model
{
    public $id;
    public $texto;
    public $reactivo;
    private $transaction;
    public $done;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            // [['nombre', 'apellidos', 'email', 'status', 'nivel'], 'required'],
            [['id', 'reactivo'], 'integer'],
            [['texto'], 'string'],
            [['texto'], 'required']
        ];
    }

    public function attributeLabels()
    {
        return [
            'texto'=>''
        ];
    }

    public function guardar()
    {
        $connection = \Yii::$app->db;
        $this->transaction = $connection->beginTransaction();
        $alumnoExamen = AlumnoExamen::findOne($this->id);
        $examen = $alumnoExamen->examen;

        $alumno = $alumnoExamen->alumno;
        $respondidoWriting = false;
        $nivel = $alumno->nivelAlumno->nombre;

        $statusExamen = StatusExamen::find()->where(['codigo' => 'AWA'])->one();
        $alumno->status_examen_id = $statusExamen->id;
        $alumno->save();
        $tipoDiagnostic = TipoExamen::find()->where('clave="DIA"')->one();
        if($tipoDiagnostic->id == $examen->tipo_examen_id){
            foreach ($alumno->alumnoExamens as $examen_anterior) {
                foreach ($examen_anterior->aluexaReactivos as $reactivo) {
                    if ($reactivo->respuestaWriting && $examen_anterior->fecha_realizacion) {
                        $respondidoWriting = true;
                    }
                }
                if($nivel == 'C2'){
                    if(!$examen_anterior->fecha_realizacion){
                        $examen_anterior->fecha_realizacion = time();
                        $examen_anterior->save();
                    }
                }
            }
            if ($respondidoWriting) {
                //$this->transaction->rollback();
                $this->transaction->commit();
                return true;
            }
        }

        if($alumnoExamen->writing_used_time == null){
            $alumnoExamen->writing_used_time = 0;
        }
        if(($alumnoExamen->writing_used_time/60) >= $examen->writing_duration){
            $alumnoExamen->timedout = 1;
        }

        $alumnoExamen->fecha_realizacion = time();
        if(!$alumnoExamen->writing_used_time){
            $alumnoExamen->writing_used_time = 0;
        }
        $alumnoExamen->save();

        $reactivo = AluexaReactivos::find()
                    ->where(['alumno_examen_id' => $alumnoExamen->id])
                    ->andWhere(['is not', 'respuestaWriting', null])
                    ->one();
        if(!$reactivo){
            $reactivo = new AluexaReactivos();
            $reactivo->alumno_examen_id = $alumnoExamen->id;
            $reactivo->reactivo_id = $this->reactivo;
            $reactivo->respuestaWriting = '';
            $reactivo->save();
        }
        $reactivo->respuestaWriting = $this->texto;
        if(!$reactivo->save()){
            $this->transaction->rollback();
            return false;
        }

        $this->transaction->commit();
        return true;
    }

    public function guardarV2()
    {
        $connection = \Yii::$app->db;
        $this->transaction = $connection->beginTransaction();
        $alumnoExamen = AlumnoExamen::findOne($this->id);
        $writing_data = WritingData::find()->where([
            'reactivo_id' => $this->reactivo,
            'alumno_examen_id' => $this->id
        ])->one();
        $writing_data->completed = 1;
        $writing_data->save();
        $writings_completed = count(WritingData::find()->where([
            'alumno_examen_id' => $this->id,
            'completed' => 1
        ])->all());

        $reactivo = AluexaReactivos::find()
                    ->where(['alumno_examen_id' => $alumnoExamen->id])
                    ->andWhere(['reactivo_id' => $this->reactivo])
                    ->one();
        if(!$reactivo){
            $reactivo = new AluexaReactivos();
            $reactivo->alumno_examen_id = $alumnoExamen->id;
            $reactivo->reactivo_id = $this->reactivo;
            $reactivo->respuestaWriting = '';
            $reactivo->save();
        }
        $reactivo->respuestaWriting = $this->texto;
        if(!$reactivo->save()){
            $this->transaction->rollback();
            return false;
        }

        if($writings_completed < 2){
            $this->done = false;
            $this->transaction->commit();
            return true;
        }else{
            $alumnoExamen->fecha_realizacion = time();
            if(!$alumnoExamen->writing_used_time){
                $alumnoExamen->writing_used_time = 0;
            }
            $alumnoExamen->save();

            $alumno = $alumnoExamen->alumno;
            $statusExamen = StatusExamen::find()->where(['codigo' => 'AWA'])->one();
            $alumno->status_examen_id = $statusExamen->id;
            $alumno->save();

            $this->done = true;
            $this->transaction->commit();
            return true;
        }

        $this->transaction->commit();
        return true;
    }
}
