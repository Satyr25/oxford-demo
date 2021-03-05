<?php

namespace app\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

use app\models\Examen;
use app\models\TipoExamen;

use common\models\User;

/**
 * ClienteSearch represents the model behind the search form about `common\models\Cliente`.
 */
class ExamenAcademicoSearch extends Examen
{

    public $nombre;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // [['id'], 'integer'],
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
        // $user = User::find()->where('user.id='.Yii::$app->user->getId())->one();
        // $query = Examen::find()->where('examen.academico_id='.$user->academico->id);
        $query = Examen::find()->where('status=1');

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

        // if($this->nombre == "active" || $this->nombre == "Active")
        // {
        //     $this->nombre = 1;
        // }
        // else if($this->nombre == "inactive" || $this->nombre == "Inactive")
        // {
        //     $this->nombre = 0;
        // }

        // $query->andfilterWhere(['or',['like', 'examen.nombre', $this->nombre],['like','examen.status',$this->nombre]]);

        // var_dump($query->createCommand()->sql);exit;
        return $dataProvider;

    }

    public function searchType($type, $params){
        // $user = User::find()->where('user.id=' . Yii::$app->user->getId())->one();
        if($type == 'CER2'){
            $tipo_examen = TipoExamen::find()->where(['tipo_examen.clave'=>'CER'])->one();
            $query = Examen::find()->where(
                [
                    'examen.certificate_v2' => 1,
                    'examen.tipo_examen_id' => $tipo_examen->id,
                    'status' => 1
                ]
            );
        }else if($type == 'DIA2'){
            $tipo_examen = TipoExamen::find()->where(['tipo_examen.clave'=>'DIA'])->one();
            $query = Examen::find()->where(
                [
                    'examen.diagnostic_v2' => 1,
                    'examen.tipo_examen_id' => $tipo_examen->id,
                    'status' => 1
                ]
            );
        }else if($type == 'DIA3'){
            $tipo_examen = TipoExamen::find()->where(['tipo_examen.clave'=>'DIA'])->one();
            $query = Examen::find()->where(
                [
                    'examen.diagnostic_v3' => 1,
                    'examen.tipo_examen_id' => $tipo_examen->id,
                    'status' => 1
                ]
            );
        }else{
            $tipo_examen = TipoExamen::find()->where(['tipo_examen.clave'=>$type])->one();
            $query = Examen::find()->where(
                [
                    // 'examen.academico_id' => $user->academico->id,
                    'examen.tipo_examen_id' => $tipo_examen->id,
                    'status' => 1
                ]
            );
        }

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

        // if($this->nombre == "active" || $this->nombre == "Active")
        // {
        //     $this->nombre = 1;
        // }
        // else if($this->nombre == "inactive" || $this->nombre == "Inactive")
        // {
        //     $this->nombre = 0;
        // }

        // $query->andfilterWhere(['or',['like', 'examen.nombre', $this->nombre],['like','examen.status',$this->nombre]]);

        // var_dump($query->createCommand()->sql);exit;
        return $dataProvider;

    }
}
