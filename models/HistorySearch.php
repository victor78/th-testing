<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\History;

/**
 * HistorySearch represents the model behind the search form of `app\models\History`.
 */
class HistorySearch extends History
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'sender_id', 'receiver_id'], 'integer'],
            [['amount'], 'number'],
            [['realized'], 'safe'],
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
        $query = History::find();

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
            'sender_id' => $this->sender_id,
            'receiver_id' => $this->receiver_id,
            'amount' => $this->amount,
            'realized' => $this->realized,
        ]);

        return $dataProvider;
    }
    
    static public function getUserAccountProvider($user)
    {
        $dataProvider = new ActiveDataProvider([
            'query' => History::find()
            ->where([
                'sender_id' => $user->id,
            ])
            ->orWhere([
                'receiver_id' => $user->id,
            ]),
//                'sort'=> ['defaultOrder' => ['id' => SORT_DESC]],
            'pagination' => [
                'pageSize' => Yii::$app->params['user.balanceHistory.pageSize'],
            ],                  
        ]);
        
        return $dataProvider;
                       
    }
}
