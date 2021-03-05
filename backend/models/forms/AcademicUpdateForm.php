<?php
namespace backend\models\forms;

use Yii;
use yii\base\Model;

use common\models\User;
use app\models\TipoUsuario;
use app\models\Academico;

/**
 * ContactForm is the model behind the contact form.
 */
class AcademicUpdateForm extends Model
{
    public $nombre;
    public $apellidos;
    public $email;
    public $username;
    public $password;
    public $status;

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
            ['email', 'unique', 'targetClass' => User::className(), 'message' => 'This email address has already been taken.'],

            ['password', 'string', 'min' => 6],

            [['nombre', 'apellidos'], 'required'],
            [['nombre', 'apellidos'], 'string'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'nombre' => 'First Name',
            'apellidos' => 'Last Name',
            'email' => 'Email',
        ];
    }

    public function update($id, $post)
    {
        $connection = \Yii::$app->db;
        $this->transaction = $connection->beginTransaction();

        $admin = Academico::find()->where(['id' => $id])->one();
        $admin->nombre = $post['nombre'];
        $admin->apellidos = $post['apellidos'];
        if (!$admin->save()) {
            var_dump($admin->getErrors());
            exit;
            $this->transaction->rollback();
            return false;
        }

        $user = User::find()->where(['academico_id' => $id])->one();
        $user->username = $post['username'];
        $user->email = $post['email'];
        if ($post['password']){
            $user->setPassword($post['password']);
        }
//        $user->generateAuthKey();

        if (!$user->save()) {
            var_dump($user->getErrors());exit;
            $this->transaction->rollback();
            return false;
        }

        $this->transaction->commit();
        return true;
    }

}
