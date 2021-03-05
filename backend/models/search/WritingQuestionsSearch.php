<?php
namespace app\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

use app\models\AluexaReactivos;

/**
 * ClienteSearch represents the model behind the search form about `common\models\Cliente`.
 */
class WritingQuestionsSearch extends AluexaReactivos
{
    public $nombre;
    public $fecha;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['nombre','fecha'],'string'],
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
    public function search($params,$version)
    {
        if($version == 'v2'){
            $query = AluexaReactivos::find()
                ->join('INNER JOIN', 'alumno_examen', 'alumno_examen.id = aluexa_reactivos.alumno_examen_id')
                ->join('INNER JOIN', 'writing_data', 'writing_data.alumno_examen_id = alumno_examen.id')
                ->join('INNER JOIN', 'examen', 'examen.id = alumno_examen.examen_id')
                ->join('INNER JOIN', 'alumno', 'alumno.id = alumno_examen.alumno_id')
                ->join('INNER JOIN', 'user', 'user.alumno_id = alumno.id')
                ->join('INNER JOIN', 'nivel_alumno', 'nivel_alumno.id = examen.nivel_alumno_id')
                ->where(
                    'alumno_examen.fecha_realizacion IS NOT NULL AND '.
                    'examen.certificate_v2 = 1 AND '.
                    'writing_data.grade is NULL AND '.
                    'aluexa_reactivos.respuestaWriting IS NOT NULL AND aluexa_reactivos.calificado IS NULL OR aluexa_reactivos.calificado = 0')
                ->distinct();
        }else{
            $query = AluexaReactivos::find()
                ->join('INNER JOIN', 'alumno_examen', 'alumno_examen.id = aluexa_reactivos.alumno_examen_id')
                ->join('INNER JOIN', 'examen', 'examen.id = alumno_examen.examen_id')
                ->join('INNER JOIN', 'alumno', 'alumno.id = alumno_examen.alumno_id')
                ->join('INNER JOIN', 'user', 'user.alumno_id = alumno.id')
                ->join('INNER JOIN', 'nivel_alumno', 'nivel_alumno.id = examen.nivel_alumno_id')
                ->where(
                    'alumno_examen.fecha_realizacion IS NOT NULL AND '.
                    'examen.certificate_v2 IS NULL AND '.
                    'aluexa_reactivos.respuestaWriting IS NOT NULL AND aluexa_reactivos.calificado IS NULL OR aluexa_reactivos.calificado = 0');

        }

        // add conditions that should always apply here

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

        $timestampInicio = (($this->fecha) ? strtotime($this->fecha) : null);
        $timestampFin = (($timestampInicio) ? $timestampInicio + 86400 : null);

        $query->andFilterWhere([
            'or',
            ['like', 'codigo', $this->nombre],
            ['like', 'nivel_alumno.nombre', $this->nombre],
            ])
            ->andFilterWhere(['and',
                ['>=', 'alumno_examen.fecha_realizacion', $timestampInicio],
                ['<=', 'alumno_examen.fecha_realizacion', $timestampFin],
            ]);

        return $dataProvider;
    }
}
