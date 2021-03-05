<?php

namespace app\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

use app\models\Alumno;
use app\models\NivelAlumno;
use app\models\TipoExamen;

/**
 * ClienteSearch represents the model behind the search form about `common\models\Cliente`.
 */
class AlumnoSearch extends Alumno
{
    public $status;
    public $nombre;
    public $examenes;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id','created_at','updated_at','status'], 'integer'],
            [['nombre', 'email', 'telefono', 'examenes'], 'string'],

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
    public function search($id)
    {
        $query = Alumno::find()
        ->where('alumno.grupo_id='.$id);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => false,
            'pagination' => [
                'pageSize' => 50,
            ],
        ]);
        is_null($this->status) ? $this->status = 1 : $this->status;
        $query->andFilterWhere(['=', 'status', $this->status]);

        // $this->load($params);
        // if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
        //     return $dataProvider;
        // }

        $query->andFilterWhere([
            'or',
            ['like','nombre',$this->nombre],
            ['like','apellidos',$this->nombre],
            ['like','CONCAT(nombre," ",apellidos)',$this->nombre],
            ['like','CONCAT(apellidos," ",nombre)',$this->nombre]
        ]);
        if($this->examenes){
            $null = new \yii\db\Expression('null');
            $examen = explode('-',$this->examenes);
            if($examen[0] == '1'){
                $query->andFilterWhere(['=', 'nivel_alumno_id', $examen[1]]);
                $query->andFilterWhere([
                    'or',
                    ['is', 'nivel_mock_id', $null],
                    ['=', 'nivel_mock_id', 8],
                ]);
                $query->andFilterWhere([
                    'or',
                    ['is', 'nivel_inicio_mock_id', $null],
                    ['=', 'nivel_inicio_mock_id', 8],
                ]);
                $query->andFilterWhere([
                    'or',
                    ['is', 'nivel_certificate_id', $null],
                    ['=', 'nivel_certificate_id', 8],
                ]);
            }else if($examen[0] == '2'){
                $query->andFilterWhere(['=', 'nivel_mock_id', $examen[1]]);
                $query->andFilterWhere([
                    'or',
                    ['is', 'nivel_certificate_id', $null],
                    ['=', 'nivel_certificate_id', 8],
                ]);

            }else if($examen[0] == '3'){
                $query->andFilterWhere(['=', 'nivel_certificate_id', $examen[1]]);
            }
        }

        // $query->andFilterWhere(['like', 'nombre', $this->nombre])
        //     ->orFilterWhere(['lie']);
        $query->orderBy('alumno.apellidos, alumno.nombre ASC');
        return $dataProvider;
    }
}
