<?php
namespace app\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

use app\models\Calificaciones;

/**
 * ClienteSearch represents the model behind the search form about `common\models\Cliente`.
 */
class ScoredSpeakingSearch extends Model
{
    public $level;
    public $exam;
    public $score;
    public $code;
    public $academico;
    public $fecha;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['level','exam','score','code','academico'],'string'],
            [['fecha'], 'string']
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
        $query = Calificaciones::find()
            ->select([
                'nivel_alumno.nombre AS level', 'calificaciones.calificacionSpeaking AS score',
                'user.codigo AS code', 'CONCAT(academico.nombre," ",academico.apellidos) AS academico',
                'calificaciones.fecha_calificacion_speaking AS fecha'
            ])
            ->join('INNER JOIN', 'academico', 'academico.id = calificaciones.academico_speaking_id')
            ->join('INNER JOIN', 'alumno_examen', 'alumno_examen.calificaciones_id = calificaciones.id')
            ->join('INNER JOIN', 'alumno', 'alumno.id = alumno_examen.alumno_id')
            ->join('INNER JOIN', 'user', 'user.alumno_id = alumno.id')
            ->join('LEFT JOIN', 'examen', 'examen.id = alumno_examen.examen_id')
            ->join('LEFT JOIN', 'nivel_alumno', 'nivel_alumno.id = examen.nivel_alumno_id')
            ->where('calificaciones.calificacionSpeaking IS NOT NULL');
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

        $query->andFilterWhere([
            'or',
            ['like', 'codigo', $this->code],
            ['=', 'DATE(calificaciones.fecha_calificacion)', $this->fecha],
        ]);



        return $dataProvider;
    }
}
