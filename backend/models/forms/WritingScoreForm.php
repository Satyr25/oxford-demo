<?php
namespace backend\models\forms;

use Yii;
use yii\base\Model;
use app\models\AluexaReactivos;
use app\models\Alumno;
use app\models\AlumnoExamen;
use app\models\NivelAlumno;
use app\models\StatusExamen;
use app\models\TipoExamen;

/**
 * ContactForm is the model behind the contact form.
 */
class WritingScoreForm extends Model
{
    public $id;
    public $puntos;

    private $transaction;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['puntos', 'id'], 'integer'],
            [['puntos'], 'required'],
        ];
    }

    public function attributeLabels()
    {
        return [
           'puntos' => ''
        ];
    }

    public function guardar()
    {
        $connection = \Yii::$app->db;
        $this->transaction = $connection->beginTransaction();

        $reactivo = AluexaReactivos::findOne($this->id);

        if($this->puntos > $reactivo->reactivo->puntos || $this->puntos < 0){
            $this->transaction->rollback();
            Yii::$app->session->setFlash('error', 'Score is not in range');
            return false;
        }

        $calificaciones = $reactivo->alumnoExamen->calificaciones;
        if($calificaciones->calificacionWriting != null){
            $this->transaction->rollback();
            Yii::$app->session->setFlash('error', 'This exam has already been scored');
            return false;
        }
        $calificaciones->calificacionWriting = $this->puntos;
        $calificaciones->academico_id = Yii::$app->user->identity->academico->id;
        $calificaciones->fecha_calificacion = time();
        if(!$calificaciones->update()){
            $this->transaction->rollback();
            Yii::$app->session->setFlash('error', 'There was an error scoring the exam');
            return false;
        }

        $reactivo->calificado = 1;
        if(!$reactivo->update()){
            $this->transaction->rollback();
            Yii::$app->session->setFlash('error', 'There was an error scoring the exam');
            return false;
        }

        $aluexa = $reactivo->alumnoExamen;
        $alumno = Alumno::findOne($aluexa->alumno_id);
        $nivel = NivelAlumno::findOne($alumno->nivel_alumno_id);
        $programa = $alumno->grupo->instituto->programa;
        if($aluexa->tipoExamen->clave != 'CER'){
            $promedio = $calificaciones->calcularPromedio();
            $calificaciones->update();
            $statusExamen = StatusExamen::find()->where(['codigo' => 'FIN'])->one();
            $alumno->status_examen_id = $statusExamen->id;
            if(!$alumno->save()){
                $this->transaction->rollback();
                Yii::$app->session->setFlash('error', 'There was an error updating student status');
                return false;
            }
            if($alumno->diagnostic_v2 == 1 || $alumno->diagnostic_v3 == 1){
                if($nivel->nombre == 'B1'){
                    if($promedio < 60){
                        $nuevo_nivel = NivelAlumno::find()->where('nombre="A2"')->one();
                        $alumno->nivel_alumno_id = $nuevo_nivel->id;
                        $alumno->update();
                    }else{
                        $nuevo_nivel = NivelAlumno::find()->where('nombre="B1"')->one();
                        $alumno->nivel_alumno_id = $nuevo_nivel->id;
                        $alumno->update();
                    }
                }else if($nivel->nombre == 'A2'){
                    if($promedio < 60){
                        $nuevo_nivel = NivelAlumno::find()->where('nombre="A1"')->one();
                        $alumno->nivel_alumno_id = $nuevo_nivel->id;
                        $alumno->update();
                    }
                }else if($nivel->nombre == 'B2'){
                    $c1 = NivelAlumno::find()->where('nombre="C1"')->one();
                    $examen_c1 = $aluexa->porNivelAlumno($c1->id,$alumno->id,1);
                    if($examen_c1){
                        $nuevo_nivel = NivelAlumno::find()->where('nombre="B2"')->one();
                    }else if($promedio < 60){
                        $nuevo_nivel = NivelAlumno::find()->where('nombre="B1"')->one();
                    }else{
                        $nuevo_nivel = NivelAlumno::find()->where('nombre="B2"')->one();
                    }
                    $alumno->nivel_alumno_id = $nuevo_nivel->id;
                    $alumno->update();
                }else if($nivel->nombre == 'C1'){
                    if($promedio < 60){
                        $nuevo_nivel = NivelAlumno::find()->where('nombre="B2"')->one();
                    }else if($promedio >= 60){
                        $nuevo_nivel = NivelAlumno::find()->where('nombre="C1"')->one();
                    }
                    $alumno->nivel_alumno_id = $nuevo_nivel->id;
                    $alumno->update();
                }else if($nivel->nombre == 'C2'){
                    if($promedio < 70){
                        $nuevo_nivel = NivelAlumno::find()->where('nombre="C1"')->one();
                        $alumno->nivel_alumno_id = $nuevo_nivel->id;
                        $alumno->update();
                    }
                }
            }else{
                switch($nivel->nombre){
                    case 'C2':
                        if($aluexa->examen->nivelAlumno->nombre != "C2"){
                            $nuevo_nivel = NivelAlumno::find()->where('nombre="C1"')->one();
                            $alumno->nivel_alumno_id = $nuevo_nivel->id;
                            $alumno->update();
                        }
                        break;
                    default:
                        break;
                }

                $promedio = $calificaciones->calcularPromedio();
                $calificaciones->update();
                $statusExamen = StatusExamen::find()->where(['codigo' => 'FIN'])->one();
                $alumno->status_examen_id = $statusExamen->id;
                if(!$alumno->save()){
                    $this->transaction->rollback();
                    Yii::$app->session->setFlash('error', 'There was an error updating student status');
                    return false;
                }
            }
        }else{
            if($calificaciones->calificacionSpeaking !== null || $programa->clave != 'CLI'){
                $statusExamen = StatusExamen::find()->where(['codigo' => 'FIN'])->one();
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
            }else{
                $statusExamen = StatusExamen::find()->where(['codigo' => 'SPE'])->one();
            }
            $alumno->status_examen_id = $statusExamen->id;
            if(!$alumno->save()){
                $this->transaction->rollback();
                Yii::$app->session->setFlash('error', 'There was an error updating student status');
                return false;
            }
        }

        $this->transaction->commit();
        return true;
    }
}
