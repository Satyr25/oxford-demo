<?php

namespace app\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

use app\models\Reactivo;

/**
 * ClienteSearch represents the model behind the search form about `common\models\Cliente`.
 */
class QuestionSearch extends Reactivo
{
    public $nombre;
    public $section;
    public $level;
    public $version;
    public $exam_type;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['nombre'], 'string'],
            [['section','level','exam_type','version'], 'integer'],
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
        $query = Reactivo::find()
            ->join('INNER JOIN', 'seccion', 'seccion.id = reactivo.seccion_id')
            ->join('INNER JOIN', 'examen', 'examen.id = seccion.examen_id')
            ->where('reactivo.status = 1');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => false,
            'pagination' => [
                'pageSize' => 50,
            ],
        ]);

        $this->load($params);
        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere(['like', 'reactivo.pregunta', $this->nombre])
            ->orFilterWhere(['like', 'reactivo.instrucciones', $this->nombre]);
        if($this->section)
            $query->andFilterWhere(['=', 'seccion.tipo_seccion_id', $this->section]);
        if($this->level)
            $query->andFilterWhere(['=', 'examen.nivel_alumno_id', $this->level]);
        if($this->version)
            $query->andFilterWhere(['=', 'examen.variante_id', $this->version]);
        if($this->exam_type){
            if($this->exam_type == 4){
                $query->andFilterWhere(['=', 'examen.tipo_examen_id', '1']);
                $query->andFilterWhere(['=', 'examen.diagnostic_v2', '1']);
            }else if($this->exam_type == 5){
                $query->andFilterWhere(['=', 'examen.tipo_examen_id', '3']);
                $query->andFilterWhere(['=', 'examen.certificate_v2', '1']);
            }else if($this->exam_type == 6){
                $query->andFilterWhere(['=', 'examen.tipo_examen_id', '1']);
                $query->andFilterWhere(['=', 'examen.diagnostic_v3', '1']);
            }else{
                $query->andFilterWhere(['=', 'examen.tipo_examen_id', $this->exam_type]);
            }
        }

        return $dataProvider;
    }
}
