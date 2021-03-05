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
class StudentExamsSearch extends AlumnoExamen
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
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
        $query = AlumnoExamen::find()
            ->where([
                'alumno_id' => Yii::$app->user->identity->alumno->id,
                'fecha_realizacion' => NULL
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
        if (!$this->validate()) {
            return $dataProvider;
        }

        return $dataProvider;

    }

    public function searchDoneExams($params){
        $query = AlumnoExamen::find()
            ->select('alumno_examen.*')
            ->join('INNER JOIN', 'examen', 'examen.id = alumno_examen.examen_id')
            ->join('INNER JOIN', 'tipo_examen', 'tipo_examen.id = examen.tipo_examen_id')
            ->where(
                'alumno_id = '.Yii::$app->user->identity->alumno->id.
                ' AND fecha_realizacion IS NOT NULL'.
                ' AND tipo_examen.clave="DIA"'.
                ' AND alumno_examen.writing_used_time IS NOT NULL'
            )
            ->orderBy([
                'fecha_realizacion' => SORT_DESC,
            ])->limit(1);
        $queryMock = AlumnoExamen::find()
            ->select('alumno_examen.*')
            ->join('INNER JOIN', 'examen', 'examen.id = alumno_examen.examen_id')
            ->join('INNER JOIN', 'tipo_examen', 'tipo_examen.id = examen.tipo_examen_id')
            ->where(
                'alumno_id = '.Yii::$app->user->identity->alumno->id.
                ' AND fecha_realizacion IS NOT NULL'.
                ' AND tipo_examen.clave="MOC"'
            )
            ->orderBy([
                'fecha_realizacion' => SORT_DESC,
            ])->limit(1);
        $queryCertificate = AlumnoExamen::find()
            ->select('alumno_examen.*')
            ->join('INNER JOIN', 'examen', 'examen.id = alumno_examen.examen_id')
            ->join('INNER JOIN', 'tipo_examen', 'tipo_examen.id = examen.tipo_examen_id')
            ->where(
                'alumno_id = '.Yii::$app->user->identity->alumno->id.
                ' AND fecha_realizacion IS NOT NULL'.
                ' AND tipo_examen.clave="CER"'
            )
            ->orderBy([
                'fecha_realizacion' => SORT_DESC,
            ])->limit(1);

        $query->union($queryMock)->union($queryCertificate);
        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => false,
            'pagination' => false,
        ]);

        $this->load($params);
        if (!$this->validate()) {
            return $dataProvider;
        }

        return $dataProvider;
    }
}
