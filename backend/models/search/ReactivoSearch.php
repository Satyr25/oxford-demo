<?php

namespace app\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

use app\models\Reactivo;

/**
 * ClienteSearch represents the model behind the search form about `common\models\Cliente`.
 */
class ReactivoSearch extends Reactivo
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'created_at', 'updated_at'], 'integer'],
            [['nombre', 'email', 'telefono'], 'string'],

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
    public function search()
    {
        $query = Reactivo::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => false,
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);

        // $this->load($params);
        // if (!$this->validate()) {
        //     return $dataProvider;
        // }

        // $query->orFilterWhere(['like', 'instituto.nombre', $this->nombre])
        //     ->orFilterWhere(['like', 'instituto.email', $this->nombre])
        //     ->orFilterWhere(['like', 'instituto.telefono', $this->nombre]);

        return $dataProvider;
    }
}
