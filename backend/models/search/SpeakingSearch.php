<?php
namespace app\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

use app\models\AlumnoExamen;
use app\models\StatusExamen;
use app\models\TipoExamen;
use app\models\Programa;

/**
 * ClienteSearch represents the model behind the search form about `common\models\Cliente`.
 */
class SpeakingSearch extends AlumnoExamen
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
    public function search($params)
    {
        $certificate = TipoExamen::find()->where('clave="CER"')->one();
        $programa = Programa::find()->where('clave="CLI"')->one();
        $query = AlumnoExamen::find()
            ->select([
                'alumno_examen.id AS id', 'alumno_examen.fecha_realizacion',
                'CONCAT_WS(" ",alumno.nombre,alumno.apellidos) AS nombre_alumno',
                'user.codigo AS codigo', 'nivel_alumno.clave as nivel',
                'instituto.nombre AS instituto'
            ])
            ->join('LEFT JOIN', 'calificaciones', 'calificaciones.id = alumno_examen.calificaciones_id')
            ->join('INNER JOIN', 'alumno', 'alumno.id = alumno_examen.alumno_id')
            ->join('INNER JOIN', 'nivel_alumno', 'alumno.nivel_certificate_id = nivel_alumno.id')
            ->join('INNER JOIN', 'user', 'user.alumno_id = alumno.id')
            ->join('INNER JOIN', 'grupo', 'alumno.grupo_id = grupo.id')
            ->join('INNER JOIN', 'instituto', 'instituto.id = grupo.instituto_id')
            ->where(
                'calificaciones.calificacionSpeaking IS NULL AND '.
                'alumno.status = 1 AND alumno_examen.tipo_examen_id = '.$certificate->id.
                ' AND instituto.programa_id = '.$programa->id
            );

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
            ['like', 'instituto.nombre', $this->nombre],
            ['like', 'alumno.nombre', $this->nombre],
            ['like', 'alumno.apellidos', $this->nombre],
            ['like', 'CONCAT(alumno.nombre," ",alumno.apellidos)', $this->nombre],
            ['like', 'CONCAT(alumno.apellidos," ",alumno.nombre)', $this->nombre],
            ['like', 'nivel_alumno.nombre', $this->nombre],
            ])
            ->andFilterWhere(['and',
                ['>=', 'alumno_examen.fecha_realizacion', $timestampInicio],
                ['<=', 'alumno_examen.fecha_realizacion', $timestampFin],
            ]);

        return $dataProvider;
    }
}
