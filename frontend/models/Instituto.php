<?php

namespace app\models;
use yii\behaviors\TimestampBehavior;
use Yii;

/**
 * This is the model class for table "instituto".
 *
 * @property int $id
 * @property int $direccion_id
 * @property string $nombre
 * @property string $email
 * @property string $telefono
 * @property int $created_at
 * @property int $updated_at
 * @property int $status
 * @property int $borrado
 *
 * @property Grupo[] $grupos
 * @property Direccion $direccion
 * @property Profesor[] $profesors
 * @property User[] $users
 */
class Instituto extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'instituto';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['direccion_id', 'created_at', 'updated_at', 'status', 'borrado'], 'integer'],
            [['nombre', 'email', 'status', 'borrado'], 'required'],
            [['nombre'], 'string', 'max' => 256],
            [['telefono'], 'string', 'max' => 45],
            [['email'], 'string', 'max' => 255],
            [['direccion_id'], 'exist', 'skipOnError' => true, 'targetClass' => Direccion::className(), 'targetAttribute' => ['direccion_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'direccion_id' => 'Direccion ID',
            'nombre' => 'Institute Name',
            'email' => 'Email',
            'telefono' => 'Phone No.',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'status' => 'Status',
            'borrado' => 'Borrado',
        ];
    }

    public function behaviors(){
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGrupos()
    {
        return $this->hasMany(Grupo::className(), ['instituto_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDireccion()
    {
        return $this->hasOne(Direccion::className(), ['id' => 'direccion_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProfesors()
    {
        return $this->hasMany(Profesor::className(), ['instituto_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(User::className(), ['instituto_id' => 'id']);
    }
}
