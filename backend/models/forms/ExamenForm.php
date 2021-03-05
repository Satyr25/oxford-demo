<?php
namespace backend\models\forms;
use common\models\User;
use app\models\Examen;

use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class ExamenForm extends Model
{
    public $id;
    public $tipo;
    public $nivel;
    public $version;
    public $porcentaje;
    public $duracion;
    public $puntos;
    public $status;
    public $reading_duration;
    public $writing_duration;
    public $listening_duration;
    public $english_duration;
    public $diagnostic_v2;
    public $certificate_v2;

    private $transaction;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id','tipo', 'nivel', 'version', 'porcentaje', 'duracion', 'status','reading_duration','writing_duration','listening_duration','english_duration'], 'required'],
            [['id','tipo','nivel','version','porcentaje', 'duracion', 'puntos', 'status','reading_duration','writing_duration','listening_duration','english_duration','diagnostic_v2','certificate_v2'], 'integer'],
            ['porcentaje','integer', 'min'=>0, 'max'=>100]
        ];
    }

    public function attributeLabels()
    {
        return [
            'tipo'=>'Exam',
            'nivel'=>'Level',
            'version'=>'Version',
            'porcentaje'=>'Test Passing Criteria Percentage',
            'duracion'=>'Total Duration',
            'puntos'=>'Total Points',
            'status'=>'Status',
            'reading_duration' => 'Reading',
            'writing_duration' => 'Writing',
            'listening_duration' => 'Listening',
            'english_duration' => 'Use Of English',
            'diagnostic_v2' => 'Diagnostic Version',
            'certificate_v2' => 'Certificate Version',
        ];
    }

    public function guardar()
    {
        $connection = \Yii::$app->db;
        $this->transaction = $connection->beginTransaction();

        $user = User::findOne(Yii::$app->user->getId());

        $examen = new Examen();
        $examen->tipo_examen_id = $this->tipo;
        $examen->nivel_alumno_id = $this->nivel;
        $examen->variante_id = $this->version;
        $examen->porcentaje = $this->porcentaje;
        $examen->puntos = $this->puntos;
        $examen->duracion = $this->duracion;
        $examen->status = 1;
        $examen->user_id = $user->id;
        $examen->reading_duration = $this->reading_duration;
        $examen->writing_duration = $this->writing_duration;
        $examen->listening_duration = $this->listening_duration;
        $examen->english_duration = $this->english_duration;
        if($this->tipo == 1){
            if($this->diagnostic_v2 == 1){
                $examen->diagnostic_v2 = 1;
            }else if($this->diagnostic_v2 == 2){
                $examen->diagnostic_v3 = 1;
            }
        }
        if($this->tipo == 3){
            $examen->certificate_v2 = $this->certificate_v2;
        }
        if(!$examen->save())
        {
            $this->transaction->rollback();
            return false;
        }

        $this->transaction->commit();
        return true;
    }

    public function cargar($id){
        $examen = Examen::findOne($id);
        $this->id = $examen->id;
        $this->tipo = $examen->tipo_examen_id;
        $this->nivel = $examen->nivel_alumno_id;
        $this->version = $examen->variante_id;
        $this->porcentaje = $examen->porcentaje;
        $this->duracion = $examen->duracion;
        $this->puntos = $examen->puntos;
        $this->status = $examen->status;
        $this->reading_duration = $examen->reading_duration;
        $this->writing_duration = $examen->writing_duration;
        $this->listening_duration = $examen->listening_duration;
        $this->english_duration = $examen->english_duration;
        if($examen->diagnostic_v2 == 1){
            $this->diagnostic_v2 = 1;
        }else if($examen->diagnostic_v3 == 1){
            $this->diagnostic_v2 = 3;
        }
    }

    public function actualizar(){
        $user = User::findOne(Yii::$app->user->getId());
        $examen = Examen::findOne($this->id);
        $examen->tipo_examen_id = $this->tipo;
        $examen->nivel_alumno_id = $this->nivel;
        $examen->variante_id = $this->version;
        $examen->porcentaje = $this->porcentaje;
        $examen->duracion = $this->duracion;
        $examen->puntos = $this->puntos;
        $examen->status = $this->status;
        $examen->user_id = $user->id;
        $examen->reading_duration = $this->reading_duration;
        $examen->writing_duration = $this->writing_duration;
        $examen->listening_duration = $this->listening_duration;
        $examen->english_duration = $this->english_duration;
        if($this->tipo == 1){
            if($this->diagnostic_v2 == 1){
                $examen->diagnostic_v2 = 1;
                $examen->diagnostic_v3 = NULL;
            }else if($this->diagnostic_v2 == 2){
                $examen->diagnostic_v2 = NULL;
                $examen->diagnostic_v3 = 1;
            }
        }
        if(!$examen->save()){
            return false;
        }
        return true;
    }
}
