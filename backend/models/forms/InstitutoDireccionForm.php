<?php
namespace backend\models\forms;

use Yii;
use yii\base\Model;

use app\models\Direccion;
use app\models\Instituto;

/**
 * ContactForm is the model behind the contact form.
 */
class InstitutoDireccionForm extends Model
{
    public $id;
    public $calle;
    public $numero_int;
    public $numero_ext;
    public $colonia;
    public $municipio;
    public $ciudad;
    public $estado;
    public $pais;
    public $codigo_postal;

    private $transaction;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nombre', 'email', 'telefono','nombre_contacto','email_contacto','status'], 'required'],
            [['nombre', 'calle', 'numero_int', 'numero_ext', 'colonia', 'municipio', 'ciudad', 'estado', 'pais', 'codigo_postal', 'nombre_contacto', 'fecha_creacion'], 'string'],
            [['status', 'id'],'integer'],
            [['email', 'email_contacto'], 'email'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'calle'=>'Street Address',
            'numero_int'=>'Int. No.',
            'numero_ext'=>'Ext. No.',
            'colonia'=>'Colonia',
            'municipio'=>'Municipio/Alcaldia',
            'ciudad'=>'City',
            'estado'=>'State',
            'pais'=>'Country',
            'codigo_postal'=>'Zipcode',
        ];
    }

    public function guardar(){
        $connection = \Yii::$app->db;
        $this->transaction = $connection->beginTransaction();

        $instituto = Instituto::findOne($this->id);

        $direccion = Direccion::findOne($instituto->direccion->id);
        if(is_null($direccion)){
            $direccion = new Direccion;
            $direccion->calle = $this->calle;
            $direccion->numero_int = $this->numero_int;
            $direccion->numero_ext = $this->numero_ext;
            $direccion->codigo_postal = $this->codigo_postal;
            $direccion->colonia = $this->colonia;
            $direccion->municipio = $this->municipio;
            $direccion->ciudad = $this->ciudad;
            $direccion->estado_id = $this->estado;
            $direccion->pais_id = $this->pais;

            if(!$direccion->save()){
                $this->transaction->rollback();

                return false;
            }

            $instituto->direccion_id = $direccion->id;
            if(!$instituto->update()){
                $this->transaction->rollback();
                return false;
            }
        }
        else{
            $direccion->calle = $this->calle;
            $direccion->numero_int = $this->numero_int;
            $direccion->numero_ext = $this->numero_ext;
            $direccion->codigo_postal = $this->codigo_postal;
            $direccion->colonia = $this->colonia;
            $direccion->municipio = $this->municipio;
            $direccion->ciudad = $this->ciudad;
            $direccion->estado_id = $this->estado;
            $direccion->pais_id = $this->pais;

            if(!$direccion->update()){
                $this->transaction->rollback();
                return false;
            }
        }

        $this->transaction->commit();
        return true;
    }

    public function cargaDatos($id){
        $direccion = Direccion::findOne($id);

        $this->calle = $direccion->calle;
        $this->numero_int = $direccion->numero_int;
        $this->numero_ext = $direccion->numero_ext;
        $this->codigo_postal = $direccion->codigo_postal;
        $this->colonia = $direccion->colonia;
        $this->municipio = $direccion->municipio;
        $this->ciudad = $direccion->ciudad;
        $this->estado = $direccion->estado_id;
        $this->pais = $direccion->pais_id;

        return;
    }
}
