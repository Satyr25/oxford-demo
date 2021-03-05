<?php

namespace app\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

use app\models\Alumno;
use app\models\NivelAlumno;

class ImpresionSearch extends Alumno{
    public $entidad;
    public $tipo;
    public $ciclo;

    public function rules()
    {
        return [
            [['grupo', 'nombre','impresiones'], 'string'],
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

    public function search($id)
    {
        $query = Alumno::find()
            ->select([
                'alumno.id AS id', 'CONCAT(alumno.nombre," ",alumno.apellidos) AS nombre',
                'alumno.impresiones AS impresiones',
                'grupo.grupo AS grupo_nombre', 'grupo.id AS grupo_id',
                'instituto.nombre AS instituto', 'instituto.id AS instituto_id',
                'calificaciones.id AS calificaciones_id'
            ])
            ->join('INNER JOIN', 'status_examen', 'status_examen.id = alumno.status_examen_id')
            ->join('INNER JOIN', 'grupo', 'grupo.id = alumno.grupo_id')
            ->join('INNER JOIN', 'instituto', 'instituto.id = grupo.instituto_id')
            ->join('INNER JOIN', 'alumno_examen', 'alumno_examen.alumno_id = alumno.id')
            ->join('INNER JOIN', 'calificaciones', 'calificaciones.id = alumno_examen.calificaciones_id')
            ->where([
                'and',
                ['status_examen.codigo' => "FIN"],
                ['alumno.status' => 1],
                ['grupo.status' => 1],
                ['grupo.ciclo_escolar_id' => $this->ciclo],
                ['instituto.status' => 1],
                ['alumno_examen.tipo_examen_id' => 3],
                ['is not', 'calificacionUse', null],
                ['is not', 'calificacionReading', null],
                ['is not', 'calificacionListening', null],
                ['is not', 'calificacionWriting', null],
            ])
            ->orderBy('grupo,nombre ASC');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => false,
            'pagination' => [
                'pageSize' => 50,
            ],
        ]);

        if($this->entidad == 'INS'){
            $query->andFilterWhere(['=', 'instituto.id', $id]);
        }else if($this->entidad == 'GPO'){
            $query->andFilterWhere(['=', 'grupo.id', $id]);
        }else if($this->entidad == 'ALU'){
            $query->andFilterWhere(['=', 'alumno.id', $id]);
        }


        $diploma = NivelAlumno::find()->where('clave="DP"')->one();
        if($this->tipo == 'DIP'){
            $query->andFilterWhere(['=', 'alumno.nivel_certificate_id', $diploma->id]);
        }else{
            $query->andFilterWhere(['!=', 'alumno.nivel_certificate_id', $diploma->id]);
        }
        return $dataProvider;
    }

}
