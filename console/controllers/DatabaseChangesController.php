<?php

namespace console\controllers;

use app\models\Grupo;
use Yii;
use yii\console\Controller;
use yii\helpers\Console;


class DatabaseChangesController extends Controller
{
    private $transaction;

    public function actionChangeCicloEscolarFromAllGroups()
    {
        $connection = \Yii::$app->db;
        $this->transaction = $connection->beginTransaction();

        $grupos = Grupo::find();
        foreach($grupos->each() as $grupo) {
            $grupo->ciclo_escolar_id = 1;
            if (!$grupo->save()) {
                $this->transaction->commit();
                echo "Error guardando";
                return 1;
            }
        }

        $this->transaction->commit();
        echo "Exito";
        return 0;
    }
}
