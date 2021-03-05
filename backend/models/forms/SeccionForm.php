<?php

namespace backend\models\forms;

use app\models\Seccion;
use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class SeccionForm extends Model
{
    public $id;
    public $instrucciones_generales;
    private $transaction;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['instrucciones_generales'], 'string'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'instrucciones_generales' => 'General Instructions'
        ];
    }

    public function updateSection()
    {
        $connection = \Yii::$app->db;
        $this->transaction = $connection->beginTransaction();

        if (!isset($this->id)) {
            $this->transaction->rollBack();
            return false;
        }

        $seccion = Seccion::findOne($this->id);
        $seccion->instrucciones_generales = $this->instrucciones_generales;
        if (!$seccion->save()) {
            $this->transaction->rollBack();
            return false;
        }

        $this->transaction->commit();
        return true;
    }

    public function loadDataFromSection($id)
    {
        $seccion = Seccion::findOne($id);
        $this->id = $seccion->id;
        $this->instrucciones_generales = $seccion->instrucciones_generales;
    }
}
