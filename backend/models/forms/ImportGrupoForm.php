<?php
namespace backend\models\forms;

use yii\base\Model;
use yii\web\UploadedFile;

use app\models\Alumno;
use app\models\AlumnoExamen;
use app\models\Examen;
use app\models\Instituto;
use app\models\Grupo;
use app\models\TipoUsuario;
use common\models\User;
use app\models\NivelAlumno;

class ImportGrupoForm extends Model
{
    /**
     * @var UploadedFile
     */
    public $grupoFile;
    public $id;
    private $codigo_escuela;

    public function rules()
    {
        return [
            [['grupoFile'], 'file', 'skipOnEmpty' => false, 'extensions' => 'xls, xlsx', 'checkExtensionByMimeType'=>false],
            [['id'],'integer']
        ];
    }

    public function upload()
    {
        if ($this->validate()) {
            $this->grupoFile->saveAs(getcwd(). '/uploads/' . $this->grupoFile->baseName . '.' . $this->grupoFile->extension);
            return true;
        } else {
            return false;
        }
    }

    public function import()
    {
        $connection = \Yii::$app->db;
        $transaction = $connection->beginTransaction();

        $grupo = Grupo::findOne($this->id);
        $instituto = Instituto::findOne($grupo->instituto_id);
        $palabras = explode(' ', $instituto->nombre);
        if(count($palabras) > 1){
            $codigo = strtoupper(substr($palabras[0], 0, 1).substr($palabras[1], 0, 1));
        }else{
            $codigo = strtoupper(substr($palabras[0], 0, 2));
        }
        $codigo = $this->sanitizeString($codigo);
        $this->codigo_escuela = $codigo.date('Y');

        $noLevel = NivelAlumno::find()->where(['clave'=>'NO'])->one();

        require('../XLS-reader/php-excel-reader/excel_reader2.php');
        require('../XLS-reader/SpreadsheetReader.php');
        $libros = new \SpreadsheetReader(\Yii::getalias('@webroot/uploads/'.$this->grupoFile->baseName . '.' . $this->grupoFile->extension));
        $importados = array();
        $tipo = TipoUsuario::find()->where('clave="ALU"')->one();
        foreach($libros as $i => $datos){
            if($i > 1){
                $importado = array();
                $nombre = $datos[0];
                $apellidos = $datos[1];

                if($nombre != ''){
                    $alumno = new Alumno;
                    $alumno->grupo_id = $this->id;
                    if(!mb_detect_encoding($nombre,'UTF-8', true)){
                        $alumno->nombre = utf8_encode($nombre);
                    }else{
                        $alumno->nombre = $nombre;
                    }
                    if(!mb_detect_encoding($apellidos,'UTF-8', true)){
                        $alumno->apellidos = utf8_encode($apellidos);
                    }else {
                        $alumno->apellidos = $apellidos;
                    }
                    //utf8_encode
                    $alumno->status = 1;
                    $alumno->nivel_alumno_id = $noLevel->id;
                    if($datos[2])
                        $alumno->correo = $datos[2];
                    else
                        $alumno->correo = ' ';

                    if (!$alumno->save()){
                        $transaction->rollback();
                        return false;
                    }

                    $password = $this->randomString(6);
                    $codigo = $this->codigoUsuario();
                    while(User::find()->where('codigo="'.$codigo.'"')->count() > 0){
                        $codigo = $this->codigoUsuario();
                    }
                    $user = new User();
                    $user->username = $this->randomString(10);
                    if($datos[2])
                        $user->email = $datos[2];
                    else
                        $user->email = ' ';
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
                    if (!$user->save()){
                        $transaction->rollback();
                        return false;
                    }

                    $importado['nombre'] = utf8_encode($nombre.' '.$apellidos);
                    if($datos[2])
                        $importado['email'] = $datos[2];
                    else
                        $importado['email'] = ' ';
                    $importado['codigo'] = $codigo;
                    $importado['password'] = $password;
                    $importados[] = $importado;
                }
            }
        }
        unlink(getcwd(). '/uploads/' . $this->grupoFile->baseName . '.' . $this->grupoFile->extension);
        $transaction->commit();
        return $importados;
    }

    private function codigoUsuario(){
        return $this->codigo_escuela.'-'.$this->randomString(4);
    }

    private function randomString($length){
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $string = '';
        $max = strlen($chars) - 1;
        for ($i = 0; $i < $length; $i++) {
             $string .= $chars[rand(0, $max)];
        }
        return $string;
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
