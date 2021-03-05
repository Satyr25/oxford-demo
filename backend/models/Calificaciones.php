<?php

namespace app\models;

use Yii;

use app\models\AlumnoExamen;
use app\models\Seccion;

/**
 * This is the model class for table "calificaciones".
 *
 * @property int $id
 * @property int $calificacionUse
 * @property int $calificacionReading
 * @property int $calificacionListening
 * @property int $calificacionWriting
 * @property int $calificacionSpeaking
 * @property int $academico_id
 * @property int $academico_speaking_id
 * @property int $fecha_calificacion
 * @property int $fecha_calificacion_speaking
 * @property string $promedio_importado
 * @property double $promedio
 * @property double $promedio_use
 * @property double $promedio_reading
 * @property double $promedio_listening
 * @property double $promedio_writing
 * @property string $observaciones_spe
 * @property string $calificaciones_spe
 *
 * @property AlumnoExamen[] $alumnoExamens
 * @property Academico $academico
 * @property Academico $academicoSpeaking
 */
class Calificaciones extends \yii\db\ActiveRecord
{
    public $code;
    public $level;
    public $score;
    public $academico;
    public $fecha;
    public $id_writing;
    public $examen;
    public $exam;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'calificaciones';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['calificacionUse', 'calificacionReading', 'calificacionListening', 'calificacionWriting', 'calificacionSpeaking', 'academico_id', 'academico_speaking_id', 'fecha_calificacion', 'fecha_calificacion_speaking'], 'integer'],
            [['promedio', 'promedio_use', 'promedio_reading', 'promedio_listening', 'promedio_writing'], 'number'],
            [['observaciones_spe'], 'string'],
            [['promedio_importado'], 'string', 'max' => 45],
            [['calificaciones_spe'], 'string', 'max' => 128],
            [['academico_id'], 'exist', 'skipOnError' => true, 'targetClass' => Academico::className(), 'targetAttribute' => ['academico_id' => 'id']],
            [['academico_speaking_id'], 'exist', 'skipOnError' => true, 'targetClass' => Academico::className(), 'targetAttribute' => ['academico_speaking_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'calificacionUse' => 'Calificacion Use',
            'calificacionReading' => 'Calificacion Reading',
            'calificacionListening' => 'Calificacion Listening',
            'calificacionWriting' => 'Calificacion Writing',
            'calificacionSpeaking' => 'Calificacion Speaking',
            'academico_id' => 'Academico ID',
            'academico_speaking_id' => 'Academico Speaking ID',
            'fecha_calificacion' => 'Fecha Calificacion',
            'fecha_calificacion_speaking' => 'Fecha Calificacion Speaking',
            'promedio_importado' => 'Promedio Importado',
            'promedio' => 'Promedio',
            'promedio_use' => 'Promedio Use',
            'promedio_reading' => 'Promedio Reading',
            'promedio_listening' => 'Promedio Listening',
            'promedio_writing' => 'Promedio Writing',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAlumnoExamens()
    {
        return $this->hasMany(AlumnoExamen::className(), ['calificaciones_id' => 'id']);
    }

    public function getAcademico()
    {
        return $this->hasOne(Academico::className(), ['id' => 'academico_id']);
    }

    public function getAcademicoSpeakink()
    {
        return $this->hasOne(Academico::className(), ['id' => 'academico_speaking_id']);
    }

    public function nivelDiagnostic($alumno){
        return $this->find()
            ->select([
                'calificaciones.*','alumno_examen.examen_id AS examen',
                'alumno_examen.fecha_realizacion AS fecha'
            ])
            ->join('INNER JOIN', 'alumno_examen', 'alumno_examen.calificaciones_id = calificaciones.id')
            ->join('INNER JOIN', 'alumno', 'alumno_examen.alumno_id = alumno.id')
            ->join('INNER JOIN', 'examen', 'examen.id = alumno_examen.examen_id')
            ->where('alumno_examen.tipo_examen_id = 1 AND alumno_examen.alumno_id = '.$alumno.' AND fecha_realizacion IS NOT NULL AND promedio_writing IS NOT NULL')
            ->one();
    }

    public function porExamen($examen, $tipo, $ciclo=false){
        if($tipo == 'CERV2')
            $tipo = 'CER';
        if(!$ciclo){
            return $this->find()
                    ->select('calificaciones.*')
                    ->from('alumno_examen')
                    ->join('INNER JOIN', 'alumno', 'alumno.id = alumno_examen.alumno_id')
                    ->join('INNER JOIN', 'calificaciones', 'calificaciones.id = alumno_examen.calificaciones_id')
                    ->where(
                        'alumno.status = 1 AND alumno_examen.status = 1 '.
                        'AND alumno_examen.examen_id = '.$examen.' '.
                        'AND alumno_examen.fecha_realizacion IS NOT NULL'
                        )
                    ->all();
        }else{
            $where = 'alumno.status = 1 AND alumno_examen.status = 1 '.
            'AND grupo.ciclo_escolar_id = '.$ciclo.' '.
            'AND alumno_examen.examen_id = '.$examen.' '.
            'AND alumno_examen.fecha_realizacion IS NOT NULL ';
            if($tipo == 'DIA'){
                $where .= ' AND examen.diagnostic_v2 = 0';
            }else if($tipo == 'DIAV2'){
                $where .= ' AND examen.diagnostic_v2 = 1';
            }else if($tipo == 'CER'){
                $where .= ' AND examen.certificate_v2 IS NULL';
            }else if($tipo == 'CERV2'){
                $where .= ' AND examen.certificate_v2 = 1';
            }
            return $this->find()
                    ->select('calificaciones.*')
                    ->from('alumno_examen')
                    ->join('INNER JOIN', 'examen', 'examen.id = alumno_examen.examen_id')
                    ->join('INNER JOIN', 'alumno', 'alumno.id = alumno_examen.alumno_id')
                    ->join('INNER JOIN', 'grupo', 'grupo.id = alumno.grupo_id')
                    ->join('INNER JOIN', 'calificaciones', 'calificaciones.id = alumno_examen.calificaciones_id')
                    ->where($where)
                    ->all();
        }
    }

    public function calcularPromedio(){
        $alumno_examen = AlumnoExamen::find()->where('calificaciones_id = '.$this->id)->one();
        $examen = $alumno_examen->examen;
        $tipo_examen = $examen->tipoExamen->clave;
        $nivel = $examen->nivelAlumno->clave;
        $seccion = new Seccion();
        $puntos = $seccion->puntos($examen->id);
        $programa = $alumno_examen->alumno->grupo->instituto->programa->clave;
        if($tipo_examen == 'CER'){
            $this->promedio_use = round(($this->calificacionUse * 100) / $puntos['USE'], 0, PHP_ROUND_HALF_DOWN);
            $this->promedio_reading = round((($this->calificacionReading * 100) / $puntos['REA']) / $examen->cantidadSecciones('REA'), 0, PHP_ROUND_HALF_DOWN);
            $this->promedio_listening = round((($this->calificacionListening * 100) / $puntos['LIS']) / $examen->cantidadSecciones('LIS'), 0, PHP_ROUND_HALF_DOWN);
            if($puntos['WRI'] !== NULL && $puntos['WRI'] !== 0)
                $this->promedio_writing = round(($this->calificacionWriting  * 100) / $puntos['WRI'], 0, PHP_ROUND_HALF_DOWN);
            else{
                $this->promedio_writing = 0;
            }
            if($alumno_examen->examen->certificate_v2 == 1){
                $this->promedio_writing = round($this->calificacionWriting, 0, PHP_ROUND_HALF_DOWN);
                if($programa=="CLI"){
                    $promedio = (($this->promedio_use*.20) + ($this->promedio_reading*.20)  + ($this->promedio_listening*.20) + ($this->promedio_writing*.20)+($this->calificacionSpeaking*.20));
                }else{
                    $promedio = ($this->promedio_use*.25) + ($this->promedio_reading*.25)  + ($this->promedio_listening*.25) + ($this->promedio_writing*.25);
                }
            }else{
                if($programa=="CLI"){
                    $promedio = (($this->promedio_use*.10) + ($this->promedio_reading*.20)  + ($this->promedio_listening*.20) + ($this->promedio_writing*.20)+($this->calificacionSpeaking*.30));
                }else{
                    $promedio = ($this->promedio_use*.20) + ($this->promedio_reading*.25)  + ($this->promedio_listening*.25) + ($this->promedio_writing*.30);
                }
            }
            $this->promedio = round($promedio, 0, PHP_ROUND_HALF_DOWN);
            return $this->promedio;
        } else if ($tipo_examen == "DIA") {
            $aluexa = AlumnoExamen::find()->where(['calificaciones_id' => $this->id])->one();
            if($aluexa->examen->diagnostic_v2){
                $this->promedio_use = round(($this->calificacionUse * 100) / $puntos['USE'], 0, PHP_ROUND_HALF_DOWN);
                $this->promedio_reading = round((($this->calificacionReading * 100) / $puntos['REA']), 0, PHP_ROUND_HALF_DOWN);
                $this->promedio_listening = round((($this->calificacionListening * 100) / $puntos['LIS']), 0, PHP_ROUND_HALF_DOWN);
                $this->promedio_writing = round(($this->calificacionWriting * 100) / $puntos['WRI'], 0, PHP_ROUND_HALF_DOWN);
                $promedio = (($this->promedio_use*.20) + ($this->promedio_reading*.25) + ($this->promedio_listening*.25) + ($this->promedio_writing*.30));
                $this->promedio = round($promedio, 0, PHP_ROUND_HALF_DOWN);
            }else{
                $this->promedio_use = round(($this->calificacionUse * 100) / $puntos['USE'], 0, PHP_ROUND_HALF_DOWN);
                $this->promedio_reading = round((($this->calificacionReading * 100) / $puntos['REA']), 0, PHP_ROUND_HALF_DOWN);
                $this->promedio_listening = round((($this->calificacionListening * 100) / $puntos['LIS']), 0, PHP_ROUND_HALF_DOWN);
                $this->promedio_writing = round(($this->calificacionWriting * 100) / $puntos['WRI'], 0, PHP_ROUND_HALF_DOWN);
                $promedio = ($this->promedio_use + $this->promedio_reading + $this->promedio_listening + $this->promedio_writing) / 4;
                $this->promedio = round($promedio, 0, PHP_ROUND_HALF_DOWN);
            }
            return round($promedio, 0, PHP_ROUND_HALF_DOWN);
        }
    }

    public function getTotalPointsSpeaking() {
        if (isset($this->calificaciones_spe)) {
            $pointsArray = explode(',', $this->calificaciones_spe);
            return array_sum($pointsArray);
        } else {
            return $this->calificacionSpeaking;
        }
    }
}
