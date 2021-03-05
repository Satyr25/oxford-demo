<?php
namespace backend\models\forms;

use app\models\Alumno;
use app\models\AlumnoExamen;
use app\models\Calificaciones;
use app\models\NivelAlumno;
use app\models\StatusExamen;
use Yii;
use yii\base\Model;

/**
 * ScoreSpeakingForm is the model behind the score speaking form.
 */
class ScoreSpeakingForm extends Model
{
    const MAX_POINTS_BY_LEVEL = [
        "A1" => 70,
        "A2" => 70,
        "B1" => 75,
        "B2" => 90,
        "C1" => 85,
        "C2" => 95,
    ];
    public $student_id;
    public $student_name;
    public $scores;
    public $observations;
    private $transaction;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['student_id', 'scores'], 'required'],
            [['student_id'], 'integer'],
            [['scores'], 'each', 'rule' => ['integer']],
            [['observations'], 'string']
        ];
    }

    public function saveSpeakingScoreDetails()
    {
        $connection = Yii::$app->db;
        $this->transaction = $connection->beginTransaction();
        $alumno = Alumno::findOne($this->student_id);
        $alumno_examen = AlumnoExamen::find()
            ->leftJoin('tipo_examen', 'alumno_examen.tipo_examen_id = tipo_examen.id')
            ->where([
                'alumno_examen.alumno_id' => $alumno->id,
                'tipo_examen.clave' => 'CER'
            ])
            ->one();
        if($alumno_examen->calificaciones){
            $calificaciones = Calificaciones::findOne($alumno_examen->calificaciones->id);
        }else{
            $calificaciones = new Calificaciones();
        }
        $calificaciones->calificacionSpeaking = $this->calculateFinalScore($alumno->nivelCertificate->clave);
        $calificaciones->academico_speaking_id = Yii::$app->user->identity->academico->id;
        $calificaciones->fecha_calificacion_speaking = time();
        $calificaciones->observaciones_spe = $this->observations;
        $calificaciones->calificaciones_spe = implode(',', $this->scores);
        if(!$calificaciones->save()){
            var_dump($calificaciones->getErrors());exit;
            $this->student_name = $alumno->fullName;
            $this->transaction->rollback();
            return false;
        }
        if(!$alumno_examen->calificaciones){
            $alumno_examen->calificaciones_id = $calificaciones->id;
            if(!$alumno_examen->save()){
                var_dump($alumno_examen->getErrors());exit;
                $this->student_name = $alumno->fullName;
                $this->transaction->rollback();
                return false;
            }
        }
        if($alumno_examen->alumno->statusExamen->codigo == 'SPE'){
            $promedio = $calificaciones->calcularPromedio();
            $calificaciones->update();
            $alumno->nivel_inicio_certificate_id = $alumno->nivel_certificate_id;
            if($promedio >= 50 && $promedio <= 59.00){
                $nivel = NivelAlumno::findOne($alumno->nivel_certificate_id);
                if($nivel->nombre == 'A1' || $nivel->nombre == 'N/A'){
                    $nuevo_nivel = NivelAlumno::find()->where('clave="DP"')->one();
                }else if($nivel->nombre == 'A2'){
                    $nuevo_nivel = NivelAlumno::find()->where('nombre="A1"')->one();
                }else if($nivel->nombre == 'B1'){
                    $nuevo_nivel = NivelAlumno::find()->where('nombre="A2"')->one();
                }else if($nivel->nombre == 'B2'){
                    $nuevo_nivel = NivelAlumno::find()->where('nombre="B1"')->one();
                }else if($nivel->nombre == 'C1'){
                    $nuevo_nivel = NivelAlumno::find()->where('nombre="B2"')->one();
                }else if($nivel->nombre == 'C2'){
                    $nuevo_nivel = NivelAlumno::find()->where('nombre="C1"')->one();
                }
                $alumno->nivel_certificate_id = $nuevo_nivel->id;
            }else if($promedio >= 0 && $promedio <= 49.99){
                $nuevo_nivel = NivelAlumno::find()->where('clave="DP"')->one();
                $alumno->nivel_certificate_id = $nuevo_nivel->id;
            }
            $status_examen = StatusExamen::find()->where('codigo="FIN"')->one();
            $alumno->status_examen_id = $status_examen->id;
            if(!$alumno->save()){
                var_dump($alumno->getErrors());exit;
                $this->student_name = $alumno->fullName;
                $this->transaction->rollback();
                return false;
            }
        }

        $this->transaction->commit();
        return true;
    }

    private function calculateFinalScore($level)
    {
        $totalReceived = array_sum($this->scores);
        return round(($totalReceived * 100) / self::MAX_POINTS_BY_LEVEL[$level], null, PHP_ROUND_HALF_DOWN);
    }
    
    public function saveSection($post){
        
        $connection = Yii::$app->db;
        $this->transaction = $connection->beginTransaction();        
        
        $alumno = Alumno::findOne($post['id']);
        $alumno_examen = AlumnoExamen::find()
            ->leftJoin('tipo_examen', 'alumno_examen.tipo_examen_id = tipo_examen.id')
            ->where([
                'alumno_examen.alumno_id' => $post['id'],
                'tipo_examen.clave' => 'CER'
            ])
            ->one();
        if($alumno_examen->calificaciones){
            $calificaciones = Calificaciones::findOne($alumno_examen->calificaciones->id);
        }else{
            $calificaciones = new Calificaciones();
        }
        if($calificaciones->calificaciones_spe){
            $scores = str_replace(',','',$calificaciones->calificaciones_spe);
            
            for( $i = 1; $i <= 3; $i++ ){
                if($i == $post['section']){
                    foreach ($post['answers'] as $key => $answer){
                        $scores = substr_replace($scores, $answer, $key, 1);
                    }
                } 
            }
        } else {
            $scores = '';
            for( $i = 1; $i <= 3; $i++ ){
                if($i == $post['section']){
                    foreach ($post['answers'] as $key => $answer){
                        $scores .= $answer;
                    }
                } else {
                    for ( $j = 0; $j < $post['long'.$i]; $j++){
                        $scores .= '0';
                    }
                }
            }
        }
        $scores = implode(",", preg_split('//', $scores, -1, PREG_SPLIT_NO_EMPTY));
        $calificaciones->calificaciones_spe = $scores;
        
        if(!$calificaciones->save()){
            var_dump($calificaciones->getErrors());exit;
            $this->transaction->rollback();
            return false;
        }
        
        if(!$alumno_examen->calificaciones){
            $alumno_examen->calificaciones_id = $calificaciones->id;
            if(!$alumno_examen->save()){
                var_dump($alumno_examen->getErrors());exit;
                $this->transaction->rollback();
                return false;
            }
        }
        
        $this->transaction->commit();
        return true;
    }
    
    public function deleteSection($post){
        
        $alumno_examen = AlumnoExamen::find()
            ->leftJoin('tipo_examen', 'alumno_examen.tipo_examen_id = tipo_examen.id')
            ->where([
                'alumno_examen.alumno_id' => $post['id'],
                'tipo_examen.clave' => 'CER'
            ])
            ->one();
        if(!$alumno_examen->calificaciones){
            return true;
        }else{
            $calificaciones = Calificaciones::findOne($alumno_examen->calificaciones->id);
        }
        $calificaciones->calificaciones_spe = null;
        
        if(!$calificaciones->save()){
            var_dump($calificaciones->getErrors());exit;
            return false;
        }
        return true;
    }
    
}
