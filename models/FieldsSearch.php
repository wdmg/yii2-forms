<?php

namespace wdmg\forms\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\vendor\wdmg\forms\models\Fields;

/**
 * FieldsSearch represents the model behind the search form of `app\vendor\wdmg\forms\models\Fields`.
 */
class FieldsSearch extends Fields
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'form_id', 'type', 'sort_order', 'is_required'], 'integer'],
            [['label', 'description', 'params'], 'safe'],
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
        $query = Fields::find();

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
            'form_id' => $this->form_id,
            'type' => $this->type,
            'sort_order' => $this->sort_order,
            'is_required' => $this->is_required,
        ]);

        $query->andFilterWhere(['like', 'label', $this->label])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'params', $this->params]);

        return $dataProvider;
    }
}
