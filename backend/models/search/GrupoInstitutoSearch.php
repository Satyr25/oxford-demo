<?php

namespace app\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

use app\models\Grupo;
use common\models\User;

/**
 * ClienteSearch represents the model behind the search form about `common\models\Cliente`.
 */
class GrupoInstitutoSearch extends Grupo
{

    public $nombre;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['nombre'], 'string']
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
        $user = User::findOne(Yii::$app->user->getId());
        $query = Grupo::find()
            ->leftJoin('ciclo_escolar', 'ciclo_escolar.id = grupo.ciclo_escolar_id')
            ->where([
                'instituto_id' => $user->instituto->id,
                'grupo.status' => 1,
                'ciclo_escolar.status' => 1
            ]);

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

        if($this->nombre == "active" || $this->nombre == "Active")
        {
            $this->nombre = 1;
        }
        else if($this->nombre == "inactive" || $this->nombre == "Inactive")
        {
            $this->nombre = 0;
        }

        $query->andfilterWhere(['or',['like', 'grupo.grupo', $this->nombre],['=','grupo.status',$this->nombre]]);

        return $dataProvider;
    }
}
