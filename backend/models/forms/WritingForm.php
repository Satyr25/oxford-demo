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
use app\models\ImagenReactivo;
use common\models\User;
use yii\web\UploadedFile;

/**
 * ContactForm is the model behind the contact form.
 */
class WritingForm extends Model
{
    public $instrucciones;
    public $puntos;
    public $examen;
    public $pregunta;
    public $general_instructions;
    public $imagenes;
    private $transaction;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            // [['nombre', 'apellidos', 'email', 'status', 'nivel'], 'required'],
            [['examen', 'puntos'], 'integer'],
            [['instrucciones', 'pregunta', 'general_instructions'], 'string'],
            [['examen', 'puntos'], 'required'],
            [['imagenes'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg', 'maxFiles' => 5]
        ];
    }

    public function attributeLabels()
    {
        return [
            'examen' => 'Exam',
            'puntos' => 'Points',
            'pregunta' => 'Question',
            'instrucciones' => 'Instructions',
            'imagenes' => 'Images'
        ];
    }

    public function guardar()
    {
        $connection = \Yii::$app->db;
        $this->transaction = $connection->beginTransaction();

        $user = User::find()->where('user.id=' . Yii::$app->user->getId())->one();

        $tipo_seccion = TipoSeccion::find()->where('tipo_seccion.clave="WRI"')->one();
        $seccion = Seccion::find()->where('examen_id='.$this->examen.' AND tipo_seccion_id ='.$tipo_seccion->id)->one();
        $examen = Examen::findOne($this->examen);

        if(!$seccion){
            $seccion = new Seccion();
            $seccion->examen_id = $this->examen;
            $tipo_seccion = TipoSeccion::find()->where('tipo_seccion.clave="WRI"')->one();
            $seccion->tipo_seccion_id = $tipo_seccion->id;
            $seccion->instrucciones_generales = $this->general_instructions;
            if(!$seccion->save()){
                $this->transaction->rollback();
                return false;
            }
        }

        if($examen->certificate_v2 == 1 && $examen->tipoExamen->clave == "CER"){
            $writings = count(Reactivo::find()->where('seccion_id = '.$seccion->id.' AND status = 1')->all());
            if($writings >= 2){
                $this->transaction->rollback();
                return false;
            }
        }else{
            $reactivo_anterior = Reactivo::find()->where('seccion_id = '.$seccion->id.' AND status = 1')->one();
            if($reactivo_anterior){
                $reactivo_anterior->status = 0;
                if(!$reactivo_anterior->update()){
                    $this->transaction->rollback();
                    return false;
                }
            }
        }

        $reactivo = new Reactivo();
        $tipoPregunta = TipoReactivo::find()->where('tipo_reactivo.clave="WRI"')->one();
        $reactivo->tipo_reactivo_id = $tipoPregunta->id;
        $reactivo->instrucciones = $this->instrucciones;
        $reactivo->pregunta = $this->pregunta;
        $reactivo->puntos = $this->puntos;
        $reactivo->status = 1;
        $reactivo->user_id = $user->id;
        $reactivo->seccion_id = $seccion->id;
        if(!$reactivo->save()) {
            $this->transaction->rollback();
            return false;
        }

        $this->imagenes = UploadedFile::getInstances($this, 'imagenes');
        $ruta = Yii::getAlias('@backend/web/writings');
        if (!file_exists($ruta)) {
            if (!mkdir($ruta)) {
                return false;
            }
        }
        foreach ($this->imagenes as $imagen) {
            if($imagen){
                $timestamp = time();
                $nombre_archivo = $timestamp . preg_replace("/[^a-z0-9\.]/", "", strtolower($imagen->name));
                if (!file_exists($ruta . '/' . $nombre_archivo)) {
                    if (!$imagen->saveAs($ruta . '/' . $nombre_archivo, false)) {
                        $this->transaction->rollback();
                        return false;
                    }
                }
            }
            $imagen_reactivo = new ImagenReactivo();
            $imagen_reactivo->imagen = 'writings/' . $nombre_archivo;
            $imagen_reactivo->reactivo_id = $reactivo->id;
            $imagen_reactivo->save();
        }

        $examen = Examen::findOne($this->examen);
        if($examen->tipoExamen->clave == 'CER' && $examen->certificate_v2 == 1){
            if(!$examen->actualizarTotales()){
                $this->transaction->rollback();
                return false;
            }
        }

        $this->transaction->commit();
        return true;
    }
}
