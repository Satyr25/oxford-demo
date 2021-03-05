<?php
namespace backend\models\forms;

use Yii;
use yii\base\Model;
use app\models\Instituto;
use app\models\Direccion;
use app\models\CicloEscolar;
use app\models\Grupo;
use app\models\Profesor;
use app\models\TipoUsuario;
use common\models\User;
use DateTime;

/**
 * ContactForm is the model behind the contact form.
 */
class InstitutoForm extends Model
{
    public $id;
    public $nombre;
    public $programa;
    public $email;
    public $password;
    public $telefono;
    public $calle;
    public $numero_int;
    public $numero_ext;
    public $colonia;
    public $municipio;
    public $ciudad;
    public $estado;
    public $pais;
    public $codigo_postal;
    public $nombre_contacto;
    public $email_contacto;
    public $status;
    public $fecha_creacion;
    public $diagnosticDate;
    public $mockDate;
    public $certificateDate;
    public $speakingDate;
    public $ronda;
    public $region;
    public $pruebas;
    public $referencia;
    public $errorMessage;

    private $transaction;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nombre', 'email', 'telefono','nombre_contacto','email_contacto','status','pais', 'estado', 'region', 'pruebas'], 'required'],
            [['nombre', 'calle', 'numero_int', 'numero_ext', 'colonia', 'municipio', 'ciudad', 'estado', 'pais', 'codigo_postal', 'nombre_contacto', 'fecha_creacion','password', 'diagnosticDate', 'mockDate', 'certificateDate', 'speakingDate', 'referencia'], 'string'],
            [['status','id','programa', 'region', 'pruebas'],'integer'],
            ['ronda', 'string', 'max' => 1],
            [['email', 'email_contacto'], 'email'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'nombre'=>'Name',
            'programa'=>'Programa',
            'email'=>'Email',
            'telefono'=>'Phone No.',
            'calle'=>'Street Address',
            'numero_int'=>'Int. No.',
            'numero_ext'=>'Ext. No.',
            'colonia'=>'Colonia',
            'municipio'=>'Municipio/Alcaldia',
            'ciudad'=>'City',
            'estado'=>'State',
            'pais'=>'Country',
            'codigo_postal'=>'Zipcode',
            'nombre_contacto'=>'Contact Name',
            'email_contacto'=>'Contact Email',
            'status'=>'Status',
            'fecha_creacion'=>'Creation Date',
            'programa'=>'Program',
        ];
    }

    public function guardar(){
        $connection = \Yii::$app->db;
        $this->transaction = $connection->beginTransaction();
        $tipo_usuario = TipoUsuario::find()->where('clave="INS"')->one();
        $cicloActivo = CicloEscolar::find()->where(['status' => 1])->one();

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
        if (!$direccion->save()) {
            $this->errorMessage = "Error at adding address";
            $this->transaction->rollback();
            return false;
        }
        $instituto = new Instituto;
        $instituto->direccion_id = $direccion->id;
        $instituto->nombre = $this->nombre;
        $instituto->programa_id = $this->programa;
        $instituto->email = $this->email;
        $instituto->telefono = $this->telefono;
        $instituto->status = 1;
        $instituto->borrado = 0;
        $instituto->pruebas = 0;
        if (!$instituto->save()) {
            $this->errorMessage = "Error at adding institute";
            $this->transaction->rollback();
            return false;
        }
        $profesor = new Profesor;
        $profesor->nombre = $this->nombre_contacto;
        $profesor->email = $this->email_contacto;
        $profesor->instituto_id = $instituto->id;
        if (!$profesor->save()) {
            $this->errorMessage = "Error at adding contact";
            $this->transaction->rollback();
            return false;
        }
        $user = new User();
        $user->username = $this->nombre.'-'.time();
        $user->email = $this->email;
        $user->setPassword($this->password);
        $user->generateAuthKey();
        $user->instituto_id = $instituto->id;
        $user->tipo_usuario_id = $tipo_usuario->id;
        if (!$user->save()) {
            $this->errorMessage = "Error at adding user";
            $this->transaction->rollback();
            return false;
        }
        $grupo = new Grupo();
        $grupo->nivel_id = 1;
        $grupo->instituto_id = $instituto->id;
        $grupo->grupo = "Empty Group";
        $grupo->status = 0;
        $grupo->ciclo_escolar_id = $cicloActivo->id;
        if (!$grupo->save()) {
            $this->errorMessage = "Error at adding initial group";
            $this->transaction->rollback();
            return false;
        }

        $this->transaction->commit();
        return true;
    }

    public function updateData(){
        $connection = \Yii::$app->db;
        $this->transaction = $connection->beginTransaction();

        $instituto = Instituto::findOne($this->id);
        $instituto->nombre = $this->nombre;
        $instituto->programa_id = $this->programa;
        $instituto->email = $this->email;
        $instituto->telefono = $this->telefono;
        if (isset($this->diagnosticDate) && $this->diagnosticDate) {
        $dateTime = DateTime::createFromFormat('d/m/Y', $this->diagnosticDate);
        $instituto->fecha_examen_dia = $dateTime->getTimestamp();
        }
        if (isset($this->mockDate) && $this->mockDate) {
            $dateTime = DateTime::createFromFormat('d/m/Y', $this->mockDate);
            $instituto->fecha_examen_moc = $dateTime->getTimestamp();
        }
        if (isset($this->certificateDate) && $this->certificateDate) {
            $dateTime = DateTime::createFromFormat('d/m/Y', $this->certificateDate);
            $instituto->fecha_examen_cer = $dateTime->getTimestamp();
        }
        if (isset($this->speakingDate) && $this->speakingDate) {
            $dateTime = DateTime::createFromFormat('d/m/Y', $this->speakingDate);
            $instituto->fecha_examen_spe = $dateTime->getTimestamp();
        }
        $instituto->ronda = $this->ronda;
        $instituto->region_id = $this->region;
        $instituto->pruebas = $this->pruebas;
        if (!$instituto->save()) {
            $this->transaction->rollback();
            return false;
        }

        $direccion = $instituto->direccion;
        $direccion->calle = $this->calle;
        $direccion->numero_int = $this->numero_int;
        $direccion->numero_ext = $this->numero_ext;
        $direccion->codigo_postal = $this->codigo_postal;
        $direccion->colonia = $this->colonia;
        $direccion->municipio = $this->municipio;
        $direccion->ciudad = $this->ciudad;
        $direccion->estado_id = $this->estado;
        $direccion->pais_id = $this->pais;
        $direccion->codigo_postal = $this->codigo_postal;
        $direccion->referencia = $this->referencia;
        $direccion->update();

        $profesor = $instituto->profesors[0];
        $profesor->nombre = $this->nombre_contacto;
        $profesor->email = $this->email_contacto;
        $profesor->update();

        $user = User::find()->where('instituto_id = '.$instituto->id)->one();
        $tipo_usuario = TipoUsuario::find()->where('clave="INS"')->one();

        if($user){
            $user = $instituto->users[0];
            $user->email = $this->email;
            $user->tipo_usuario_id = $tipo_usuario->id;
            if($this->password){
                $user->setPassword($this->password);
                $user->generateAuthKey();
            }
            $user->save();
        }else{
            $user = new User();
            $user->username = $this->nombre.'-'.time();
            $user->email = $this->email;
            $user->setPassword($this->password);
            $user->generateAuthKey();
            $user->instituto_id = $instituto->id;
            $user->tipo_usuario_id = $tipo_usuario->id;
            if(!$user->save()){
                $this->transaction->rollback();
                return false;
            }
        }

        $this->transaction->commit();
        return true;
    }

    public function loadData($institutoObj){
        $this->nombre = $institutoObj->nombre;
        $this->programa = $institutoObj->programa_id;
        $this->email = $institutoObj->email;
        $this->telefono = $institutoObj->telefono;
        $this->calle = $institutoObj->direccion->calle;
        $this->numero_int = $institutoObj->direccion->numero_int;
        $this->numero_ext = $institutoObj->direccion->numero_ext;
        $this->colonia = $institutoObj->direccion->colonia;
        $this->municipio = $institutoObj->direccion->municipio;
        $this->ciudad = $institutoObj->direccion->ciudad;
        if(isset($institutoObj->direccion->estado_id)){
            $this->estado = $institutoObj->direccion->estado_id;
        }
        $this->pais = $institutoObj->direccion->pais_id;
        $this->referencia = $institutoObj->direccion->referencia;
        $this->codigo_postal = $institutoObj->direccion->codigo_postal;
        $this->nombre_contacto = $institutoObj->profesors[0]->nombre;
        $this->email_contacto = $institutoObj->profesors[0]->email;
        $this->diagnosticDate = $institutoObj->fecha_examen_dia ? date('d/m/Y', $institutoObj->fecha_examen_dia) : null;
        $this->mockDate = $institutoObj->fecha_examen_moc ? date('d/m/Y', $institutoObj->fecha_examen_moc) : null;
        $this->certificateDate = $institutoObj->fecha_examen_cer ? date('d/m/Y', $institutoObj->fecha_examen_cer) : null;
        $this->speakingDate = $institutoObj->fecha_examen_spe ? date('d/m/Y', $institutoObj->fecha_examen_spe) : null;
        $this->ronda = $institutoObj->ronda;
        $this->region = $institutoObj->region->id;
        $this->pruebas = $institutoObj->pruebas;
    }
}
