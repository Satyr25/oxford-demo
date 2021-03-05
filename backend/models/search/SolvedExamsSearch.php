<?php

namespace app\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

use app\models\AlumnoExamen;
use app\models\NivelAlumno;

/**
 * ClienteSearch represents the model behind the search form about `common\models\Cliente`.
 */
class SolvedExamsSearch extends AlumnoExamen
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [];
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
    public function search($id)
    {
        $query = AlumnoExamen::find()
            ->where([
                'alumno_id' => $id,
            ])
            ->andWhere(['is not', 'fecha_realizacion', null]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => false,
            'pagination' => false,
        ]);

        // $this->load($params);
        // if (!$this->validate()) {
        //     return $dataProvider;
        // }

        return $dataProvider;
    }
}
