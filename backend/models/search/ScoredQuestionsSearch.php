<?php
namespace app\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

use app\models\Calificaciones;

/**
 * ClienteSearch represents the model behind the search form about `common\models\Cliente`.
 */
class ScoredQuestionsSearch extends Model
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
    public function search($params, $v2 = false)
    {
        $query = Calificaciones::find()
            ->select([
                'nivel_alumno.nombre AS level', 'calificaciones.calificacionWriting AS score',
                'user.codigo AS code', 'CONCAT(academico.nombre," ",academico.apellidos) AS academico',
                'calificaciones.fecha_calificacion AS fecha', 'aluexa_reactivos.id AS id_writing',
                'tipo_examen.nombre AS exam'
            ])
            ->join('LEFT JOIN', 'academico', 'academico.id = calificaciones.academico_id')
            ->join('INNER JOIN', 'alumno_examen', 'alumno_examen.calificaciones_id = calificaciones.id')
            ->join('INNER JOIN', 'aluexa_reactivos', 'aluexa_reactivos.alumno_examen_id = alumno_examen.id')
            ->join('INNER JOIN', 'alumno', 'alumno.id = alumno_examen.alumno_id')
            ->join('INNER JOIN', 'user', 'user.alumno_id = alumno.id')
            ->join('INNER JOIN', 'examen', 'examen.id = alumno_examen.examen_id')
            ->join('INNER JOIN', 'tipo_examen', 'tipo_examen.id = examen.tipo_examen_id')
            ->join('INNER JOIN', 'nivel_alumno', 'nivel_alumno.id = examen.nivel_alumno_id')
            ->where('aluexa_reactivos.respuestaWriting IS NOT NULL AND calificaciones.calificacionWriting IS NOT NULL');
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
        if($v2){
            $query->andWhere(['examen.certificate_v2' => 1]);
        }



        return $dataProvider;
    }
}
