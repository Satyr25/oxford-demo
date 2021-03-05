<?php

namespace app\models\search;

use app\models\CicloEscolar;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

use app\models\Grupo;

/**
 * ClienteSearch represents the model behind the search form about `common\models\Cliente`.
 */
class GrupoSearch extends Grupo
{
    public $ciclo_escolar;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ciclo_escolar'], 'integer'],
            [['ciclo_escolar'], 'required']
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($id, $ciclo_escolar)
    {
        $query = Grupo::find()
            ->leftJoin('ciclo_escolar', 'ciclo_escolar.id = grupo.ciclo_escolar_id')
            ->where([
                'instituto_id' => $id,
                'grupo.status' => 1,
                'ciclo_escolar_id' => $ciclo_escolar
            ]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => false,
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);

        return $dataProvider;
    }
}
