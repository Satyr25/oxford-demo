<?php
namespace backend\models\forms;

use Yii;
use yii\base\Model;

use common\models\User;
use app\models\TipoUsuario;
use app\models\Impresor;

/**
 * ContactForm is the model behind the contact form.
 */
class PrinterForm extends Model
{
    public $nombre;
    public $apellidos;
    public $email;
    public $username;
    public $password;

    private $transaction;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['username', 'trim'],
            ['username', 'required'],
            ['username', 'unique', 'targetClass' => User::className(), 'message' => 'This username has already been taken.'],
            ['username', 'string', 'min' => 2, 'max' => 255],

            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            ['email', 'unique', 'targetClass' => User::className(), 'message' => 'This email has already been taken.'],

            ['password', 'required'],
            ['password', 'string', 'min' => 6],

            [['nombre', 'apellidos'], 'required'],
            [['nombre', 'apellidos'], 'string'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'nombre'=>'First Name',
            'apellidos'=>'Last Name',
            'email'=>'Email',
        ];
    }

    public function guardar(){

        $connection = \Yii::$app->db;
        $this->transaction = $connection->beginTransaction();

        $impresor = new Impresor();
        $impresor->nombre = $this->nombre;
        $impresor->apellidos = $this->apellidos;
        if(!$impresor->save()){
            $this->transaction->rollback();
            return false;
        }

        $user = new User();
        $user->username = $this->username;
        $user->email = $this->email;
        $user->setPassword($this->password);
        $user->generateAuthKey();
        $user->impresor_id = $impresor->id;

        $tipo = TipoUsuario::find()->where('tipo_usuario.clave="IMP"')->one();
        $user->tipo_usuario_id = $tipo->id;
        if(!$user->save()){
            $this->transaction->rollback();
            return false;
        }

        $this->transaction->commit();
        return true;
    }
}
