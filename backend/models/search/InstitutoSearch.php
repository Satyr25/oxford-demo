<?php

namespace app\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

use app\models\CicloEscolar;
use app\models\Instituto;

/**
 * ClienteSearch represents the model behind the search form about `common\models\Cliente`.
 */
class InstitutoSearch extends Instituto
{
    public $ciclo_escolar;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ciclo_escolar'], 'integer']
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
        $query = Instituto::find()
            ->leftJoin('grupo', 'instituto.id = grupo.instituto_id')
            ->leftJoin('ciclo_escolar', 'ciclo_escolar.id = grupo.ciclo_escolar_id ')
            ->where([
                'instituto.status' => 1,
                'borrado' => 0,
            ])
            ->groupBy('instituto.id');
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => false,
            'pagination' => [
                'pageSize' => 50,
            ],
        ]);

        if (!($this->load($params) && $this->validate())) {
            $query->andWhere(['ciclo_escolar.status' => 1]);
            $cicloactivo = CicloEscolar::find()->where(['status' => 1])->one();
            $this->ciclo_escolar = $cicloactivo->id;
            return $dataProvider;
        }

        $query->andWhere(['grupo.ciclo_escolar_id' => $this->ciclo_escolar]);
        return $dataProvider;
    }

    public function searchInactiveInstitutes() {
        $query = Instituto::find()
            ->where([
                'status' => 0,
                'borrado' => 0
            ]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => false,
            'pagination' => [
                'pageSize' => 50,
            ],
        ]);
        return $dataProvider;
    }

    public function searchCancelledInstitutes() {
        $query = Instituto::find()
            ->where([
                'status' => 2,
                'borrado' => 0
            ]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => false,
            'pagination' => [
                'pageSize' => 50,
            ],
        ]);
        return $dataProvider;
    }
}
