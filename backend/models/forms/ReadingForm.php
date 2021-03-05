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
use app\models\Articulo;
use app\models\Seccion;
use app\models\TipoSeccion;
use app\models\RespuestaCompletar;
use common\models\User;

use yii\web\UploadedFile;
/**
 * ContactForm is the model behind the contact form.
 */
class ReadingForm extends Model
{
    public $instrucciones;
    public $nivel;
    public $examen;
    public $puntos;
    public $pregunta;
    public $correctosMul;
    public $correctosCam;
    public $respuestasMultiple;
    public $respuestasCampo;
    public $respuestasColumna;
    public $enunciados;
    public $tipos;
    public $nombre;
    public $texto;
    public $reading;
    public $imagen;
    public $general_instructions;

    private $transaction;

    public $respuestasCompletar;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            // [['nombre', 'apellidos', 'email', 'status', 'nivel'], 'required'],
            [['examen', 'pregunta', 'puntos'], 'required'],
            [['examen', 'puntos','reading'], 'integer'],
            ['pregunta', 'each', 'rule' => ['string']],
            ['instrucciones', 'each', 'rule' => ['string']],
            ['respuestasMultiple', 'each', 'rule' => ['string']],
            ['respuestasCampo', 'each', 'rule' => ['string']],
            ['respuestasColumna', 'each', 'rule' => ['string']],
            ['respuestasCompletar', 'each', 'rule' => ['string']],
            ['enunciados', 'each', 'rule' => ['string']],
            ['tipos', 'each', 'rule' => ['string']],
            ['correctosMul', 'each', 'rule' => ['integer']],
            ['correctosCam', 'each', 'rule' => ['integer']],
            [['nombre', 'texto', 'general_instructions'], 'string'],
            [['imagen'], 'file', 'extensions' => 'jpg,jpeg,png'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'nivel' => 'Level',
            'examen' => 'Exam',
            'puntos' => 'Points per question',
            'pregunta' => 'Question',
            'instrucciones' => 'Instructions',
            'correcto' => '',
            'correctosMul' => '',
            'correctosCam' => '',
            'respuestasMultiple' => 'Answer',
            'respuestasCampo' => 'Answer',
            'respuestasColumna' => 'Answer',
            'enunciados' => 'Sentence',
            'tipos' => 'Question Type',
            'nombre' => 'Title',
            'texto' => 'Article',
            'reading' => 'Article',
            'imagen' => 'Image'
        ];
    }

    public function guardar()
    {
        $connection = \Yii::$app->db;
        $this->transaction = $connection->beginTransaction();

        $imagen = UploadedFile::getInstance($this, 'imagen');
        if(!$this->reading && (!$this->nombre && !$imagen)){
            $this->transaction->rollback();
            return false;
        }

        $user = User::find()->where('user.id=' . Yii::$app->user->getId())->one();

        $tipo_seccion = TipoSeccion::find()->where('tipo_seccion.clave="REA"')->one();
        $seccion = Seccion::find()->where('examen_id='.$this->examen.' AND tipo_seccion_id ='.$tipo_seccion->id)->one();
        if(!$seccion){
            $seccion = new Seccion();
            $seccion->examen_id = $this->examen;
            $seccion->tipo_seccion_id = $tipo_seccion->id;
            $seccion->instrucciones_generales = $this->general_instructions;
            if (!$seccion->save()) {
                $this->transaction->rollback();
                return false;
            }
        }

        if(!$this->reading){
            $articulo = new Articulo();
            $articulo->titulo = $this->nombre;
            $articulo->texto = $this->texto;
            $ruta = Yii::getAlias('@backend/web/readings');
            if (!file_exists($ruta)) {
                if (!mkdir($ruta)) {
                    return false;
                }
            }
            if($imagen){
                $timestamp = time();
                $nombre_archivo = $timestamp . preg_replace("/[^a-z0-9\.]/", "", strtolower($imagen->name));
                if (!file_exists($ruta . '/' . $nombre_archivo)) {
                    if (!$imagen->saveAs($ruta . '/' . $nombre_archivo, false)) {
                        $this->transaction->rollback();
                        return false;
                    }
                }

                $articulo->imagen = 'readings/' . $nombre_archivo;
            }
            if (!$articulo->save()) {
                $this->transaction->rollback();
                return false;
            }
            $reading = $articulo->id;
        }else{
            $reading = $this->reading;
        }

        $indicePregunta = 0;
        $indiceRespMul = 0;
        $indiceRespCol = 0;

        foreach ($this->tipos as $tipo) {
            if ($tipo == 'MUL') {
                $reactivoMul = new Reactivo();
                $reactivoMul->status = 1;
                $reactivoMul->puntos = $this->puntos;
                $reactivoMul->pregunta = $this->pregunta[$indicePregunta];
                $reactivoMul->instrucciones = $this->instrucciones[$indicePregunta];
                $tipoPregunta = TipoReactivo::find()->where('tipo_reactivo.clave="' . $tipo . '"')->one();
                $reactivoMul->tipo_reactivo_id = $tipoPregunta->id;
                $reactivoMul->seccion_id = $seccion->id;
                $reactivoMul->user_id = $user->id;
                $reactivoMul->articulo_id = $reading;
                if (!$reactivoMul->save()) {
                    $this->transaction->rollback();
                    return false;
                }

                for ($i = 0; $i < 3; $i++) {
                    $resp = new Respuesta();
                    $resp->respuesta = $this->respuestasMultiple[$indiceRespMul];
                    switch ($i) {
                        case 0:
                            $resp->correcto = ($this->correctosMul[$indicePregunta] == 'a' ? 1 : 0);
                            break;
                        case 1:
                            $resp->correcto = ($this->correctosMul[$indicePregunta] == 'b' ? 1 : 0);
                            break;
                        case 2:
                            $resp->correcto = ($this->correctosMul[$indicePregunta] == 'c' ? 1 : 0);
                            break;
                    }
                    $resp->reactivo_id = $reactivoMul->id;
                    if (!$resp->save()) {

                        $this->transaction->rollback();
                        return false;
                    }
                    $indiceRespMul++;
                }
                $indicePregunta++;
                $indiceRespCol = $indiceRespCol + 5;
            } else if ($tipo == 'REL') {
                $reactivoCol = new Reactivo();
                $reactivoCol->status = 1;
                $reactivoCol->puntos = 10;
                $reactivoCol->pregunta = $this->pregunta[$indicePregunta];
                $reactivoCol->instrucciones = $this->instrucciones[$indicePregunta];
                $tipoPregunta = TipoReactivo::find()->where('tipo_reactivo.clave="' . $tipo . '"')->one();
                $reactivoCol->tipo_reactivo_id = $tipoPregunta->id;
                $reactivoCol->seccion_id = $seccion->id;
                $reactivoCol->user_id = $user->id;
                $reactivoCol->articulo_id = $reading;
                if (!$reactivoCol->save()) {
                    $this->transaction->rollback();
                    return false;
                }
                for ($i = 0; $i < 5; $i++) {
                    $respuestaCol = new RespuestaColumn();
                    $respuestaCol->respuesta = $this->respuestasColumna[$indiceRespCol];
                    $respuestaCol->reactivo_id = $reactivoCol->id;
                    if (!$respuestaCol->save()) {
                        $this->transaction->rollback();
                        return false;
                    }

                    $enunciadoCol = new EnunciadoColumn();
                    $enunciadoCol->reactivo_id = $reactivoCol->id;
                    $enunciadoCol->enunciado = $this->enunciados[$indiceRespCol];
                    $enunciadoCol->respuesta_column_id = $respuestaCol->id;
                    if (!$enunciadoCol->save()) {
                        $this->transaction->rollback();
                        return false;
                    }
                    $indiceRespCol++;
                }
                $indicePregunta++;
                $indiceRespMul = $indiceRespMul + 3;
            }else if($tipo == 'COM'){
                $reactivo = new Reactivo();
                $reactivo->status = 1;
                $reactivo->puntos = $this->puntos;
                $reactivo->pregunta = $this->pregunta[$indicePregunta];
                $reactivo->instrucciones = $this->instrucciones[$indicePregunta];
                $tipoPregunta = TipoReactivo::find()->where('tipo_reactivo.clave="'.$tipo.'"')->one();
                $reactivo->tipo_reactivo_id = $tipoPregunta->id;
                $reactivo->seccion_id = $seccion->id;
                $reactivo->user_id = $user->id;
                if(!$reactivo->save())
                {
                    $this->transaction->rollback();
                    return false;
                }
                $respuesta = new RespuestaCompletar();
                $respuesta->respuesta = implode('|',$this->respuestasCompletar[$indicePregunta]);
                $respuesta->reactivo_id = $reactivo->id;
                if(!$respuesta->save()){
                    $this->transaction->rollback();
                    return false;
                }

                $indicePregunta++;
            }
        }

        $puntos_totales = 0;
        foreach ($seccion->reactivosActivos() as $reactivo) {
            $puntos_totales = $reactivo->puntos + $puntos_totales;
        }

        $seccion->puntos_seccion = $puntos_totales;
        $seccion->update();

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
