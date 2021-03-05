<?php

namespace app\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

use app\models\Admin;
/**
 * ClienteSearch represents the model behind the search form about `common\models\Cliente`.
 */
class AdminSearch extends Admin
{
    public $nombre;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['nombre'], 'string'],
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
    public function search($params)
    {
        $query = Admin::find()
            ->where(['user.status' => 10])
            ->innerJoin('user', 'user.admin_id = admin.id');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => false,
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);

        $this->load($params);
        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->orFilterWhere(['like', 'admin.nombre', $this->nombre])
            ->orFilterWhere(['like', 'admin.apellidos', $this->nombre]);

        return $dataProvider;
    }
}
