<?php
namespace backend\models\forms;

use Yii;
use app\models\CicloEscolar;
use yii\base\Model;
use app\models\Grupo;
use app\models\Nivel;

/**
 * ContactForm is the model behind the contact form.
 */
class GrupoForm extends Model
{
    public $id;
    public $nombre;
    public $nivel;
    public $status;
    public $ciclo_escolar;

    private $transaction;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nombre','nivel'], 'required'],
            [['nommbre','nivel'], 'string'],
            [['status', 'id', 'ciclo_escolar'],'integer'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'nombre'=>'Name',
            'nivel'=>'Level',
            'status'=>'Status',
        ];
    }

    public function guardar(){
        $connection = \Yii::$app->db;
        $this->transaction = $connection->beginTransaction();

        $nivel = Nivel::find()->where('nivel.clave="'.$this->nivel.'"')->one();

        $grupo = new Grupo;
        $grupo->instituto_id = $this->id;
        $grupo->grupo = $this->nombre;
        $grupo->nivel_id = $nivel->id;
        $grupo->status = 1;
        $cicloEscolar = new CicloEscolar();
        $grupo->ciclo_escolar_id = $cicloEscolar->cicloEscolarActivo->id;
        if(!$grupo->save()){
            $this->transaction->rollback();
            return false;
        }

        $this->transaction->commit();
        return true;
    }

    public function updateData(){
        $connection = \Yii::$app->db;
        $this->transaction = $connection->beginTransaction();

        $grupo = Grupo::findOne($this->id);
        $grupo->grupo = $this->nombre;
        $grupo->nivel_id = $this->nivel;
        $grupo->ciclo_escolar_id = $this->ciclo_escolar;
        if (!$grupo->save()) {
            $this->transaction->rollback();
            return false;
        }

        $this->transaction->commit();
        return true;
    }
}
