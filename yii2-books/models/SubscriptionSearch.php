<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * SubscriptionSearch представляет форму поиска для `app\models\Subscription`.
 */
class SubscriptionSearch extends Subscription
{
    /**
     * Правила валидации для фильтров.
     */
    public function rules(): array
    {
        return [
            [['id', 'author_id', 'created_at'], 'integer'],
            [['phone', 'subscriber_name'], 'safe'],
        ];
    }

    /**
     * Сценарии — оставляем дефолтные от Model, без валидации по сценариям.
     */
    public function scenarios(): array
    {
        return Model::scenarios();
    }

    /**
     * Провайдер данных с применёнными фильтрами.
     */
    public function search(array $params): ActiveDataProvider
    {
        $query = Subscription::find()->with('author');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 20],
            'sort' => [
                'defaultOrder' => ['id' => SORT_DESC],
                'attributes' => [
                    'id',
                    'author_id',
                    'phone',
                    'subscriber_name',
                    'created_at',
                ],
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'author_id' => $this->author_id,
            'created_at' => $this->created_at,
        ]);

        $query->andFilterWhere(['like', 'phone', $this->phone])
            ->andFilterWhere(['like', 'subscriber_name', $this->subscriber_name]);

        return $dataProvider;
    }
}