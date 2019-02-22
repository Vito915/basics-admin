<?php

namespace common\models\searchs;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Crontab;

/**
 * CrontabSearch represents the model behind the search form of `common\models\Crontab`.
 */
class CrontabSearch extends Crontab
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'status', 'is_del', 'created_at', 'updated_at'], 'integer'],
            [['name', 'route', 'crontab_str', 'last_rundate', 'next_rundate'], 'safe'],
            [['exec_memory', 'exec_time'], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
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
        $query = Crontab::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'last_rundate' => $this->last_rundate,
            'next_rundate' => $this->next_rundate,
            'exec_memory' => $this->exec_memory,
            'exec_time' => $this->exec_time,
            'status' => $this->status,
            'is_del' => $this->is_del,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'route', $this->route])
            ->andFilterWhere(['like', 'crontab_str', $this->crontab_str]);

        return $dataProvider;
    }
}
