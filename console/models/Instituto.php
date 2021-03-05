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
            [['nombre', 'email', 'telefono'], 'string', 'max' => 45],
            [['direccion_id'], 'exist', 'skipOnError' => true, 'targetClass' => Direccion::className(), 'targetAttribute' => ['direccion_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => '#',
            'direccion_id' => 'Direccion ID',
            'nombre' => 'Institute Name',
            'email' => 'E-mail',
            'telefono' => 'Phone',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'status' => 'Status',
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
    public function getPrograma()
    {
        return $this->hasOne(Programa::className(), ['id' => 'programa_id']);
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

    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGruposActivos()
    {
        return $this->hasMany(Grupo::className(), ['instituto_id' => 'id'])
            ->where(['status' => 1]);
    }

    public function actualizaStatus($status)
    {
        $this->status = $status;

        $validaGuarda = $this->update();
        if(!$validaGuarda){
            return false;
        }
        return true;
    }

    public function actualizaPais($pais)
    {
        $direccion = $this->direccion;

        $direccion->pais = $pais;
        $validaGuarda = $direccion->update();
        if(!$validaGuarda){
            return false;
        }
        return true;
    }

}
