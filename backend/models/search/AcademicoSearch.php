<?php

namespace app\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

use app\models\Academico;

/**
 * ClienteSearch represents the model behind the search form about `common\models\Cliente`.
 */
class AcademicoSearch extends Academico
{
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
        $query = Academico::find()
            ->where(['user.status' => 10])
            ->innerJoin('user', 'user.academico_id = academico.id');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => false,
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);

        $this->load($params);
        if(!$this->validate()){
            return $dataProvider;
        }

        $query->orFilterWhere(['like', 'academico.nombre', $this->nombre])
            ->orFilterWhere(['like','academico.apellidos',$this->nombre])
            ;

        return $dataProvider;
    }
}
