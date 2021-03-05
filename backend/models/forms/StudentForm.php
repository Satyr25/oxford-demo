<?php
namespace backend\models\forms;

use Yii;
use yii\base\Model;
use app\models\Alumno;
use app\models\NivelAlumno;
use app\models\AlumnoExamen;
use app\models\Examen;
use app\models\Grupo;
use app\models\Instituto;
use app\models\TipoUsuario;
use common\models\User;

/**
 * ContactForm is the model behind the contact form.
 */
class StudentForm extends Model
{
    public $id;
    public $nombre;
    public $apellidos;
    public $email;
    public $status;
    public $nivel;
    public $codigo_escuela;

    private $transaction;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nombre','apellidos','email','status','nivel'], 'required'],
            [['nommbre','apellidos'], 'string'],
            [['email'],'email'],
            [['status','nivel', 'id'],'integer'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id'=>'ID',
            'nombre'=>'First Name',
            'apellidos'=>'Last Name',
            'email'=>'Email',
            'status'=>'Status',
            'nivel'=>'Level',
        ];
    }

    public function guardar(){
        $connection = \Yii::$app->db;
        $this->transaction = $connection->beginTransaction();

        $grupo = Grupo::findOne($this->id);
        $instituto = Instituto::findOne($grupo->instituto_id);
        $palabras = explode(' ', $instituto->nombre);
        if (count($palabras) > 1) {
            $codigo = strtoupper(substr($palabras[0], 0, 1) . substr($palabras[1], 0, 1));
        } else {
            $codigo = strtoupper(substr($palabras[0], 0, 2));
        }
        $codigo = $this->sanitizeString($codigo);
        $this->codigo_escuela = $codigo . date('Y');

        $noLevel = NivelAlumno::find()->where(['clave' => 'NO'])->one();

        $alumno = new Alumno;
        $alumno->grupo_id = $this->id;
        $alumno->nombre = $this->nombre;
        $alumno->apellidos = $this->apellidos;
        $alumno->status = 1;
        $alumno->nivel_alumno_id = $noLevel->id;
        $alumno->correo = $this->email;
        if(!$alumno->save()){
            $this->transaction->rollback();
            return false;
        }

        $tipo = TipoUsuario::find()->where(['clave'=>'ALU'])->one();
        $password = $this->randomString(6);
        $codigo = $this->codigoUsuario();
        $user = new User();
        $user->username = $this->randomString(10);
        $user->email = $this->email;
        $user->setPassword($password);
        $user->generateAuthKey();
        $user->alumno_id = $alumno->id;
        $user->codigo = $codigo;
        $user->tipo_usuario_id = $tipo->id;

        $encrypt_method = "AES-256-CBC";
        $secret_key = \Yii::$app->params['hash'];
        $key = hash('sha256', $secret_key);
        $iv = openssl_random_pseudo_bytes(16);
        $encrypted = openssl_encrypt($password, $encrypt_method, $key, 0, $iv);
        $user->acceso = $encrypted;
        $user->iv = bin2hex($iv);
        if (!$user->save()) {
            $transaction->rollback();
            return false;
        }

        $this->transaction->commit();
        return true;
    }

    private function codigoUsuario()
    {
        return $this->codigo_escuela . '-' . $this->randomString(4);
    }

    private function randomString($length)
    {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $string = '';
        $max = strlen($chars) - 1;
        for ($i = 0; $i < $length; $i++) {
            $string .= $chars[rand(0, $max)];
        }
        return $string;
    }

    public function loadData($student){
        $this->nombre = $student->nombre;
        $this->apellidos = $student->apellidos;
        $this->email = $student->correo;
    }

    public function updateData(){
        $student = Alumno::findOne($this->id);

        $student->nombre = $this->nombre;
        $student->apellidos = $this->apellidos;
        if(!$student->update()){
            return false;
        }

        return true;
    }

    private function sanitizeString($string)
    {
        if (!mb_detect_encoding($string, 'UTF-8', true)) {
            $cleanString = utf8_encode($string);
        } else {
            $cleanString = $string;
        }
        return $cleanString;
    }
}
