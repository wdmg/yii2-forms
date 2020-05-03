<?php

namespace wdmg\forms\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use wdmg\forms\models\Submits;

/**
 * SubmitsSearch represents the model behind the search form of `app\vendor\wdmg\forms\models\Submits`.
 */
class SubmitsSearch extends Submits
{

    public $contents;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['form_id', 'user_id', 'status'], 'safe'],
            [['contents'], 'string'],
            [['access_token', 'created_at', 'updated_at'], 'safe'],
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
        $query = Submits::find()->alias('submits');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> [
                'defaultOrder' => [
                    'id' => SORT_DESC
                ]
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        if ($this->contents) {
            $query->joinWith('formsContents');
            $query->where(['like', 'value', $this->contents]);
        }


        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        if ($this->form_id !== "*")
            $query->andFilterWhere(['form_id' => $this->form_id]);

        if ($this->user_id !== "*")
            $query->andFilterWhere(['form_id' => $this->user_id]);

        if ($this->status !== "*")
            $query->andFilterWhere(['like', 'status', $this->status]);

        if ($this->access_token)
            $query->andFilterWhere(['like', 'access_token', $this->access_token]);

        return $dataProvider;
    }
}
