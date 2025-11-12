<?php

namespace app\models;

use yii\data\ActiveDataProvider;

class AuthorSearch extends Author
{
    public function rules(): array
    {
        return [
            [['full_name'], 'safe'],
        ];
    }

    public function search(array $params): ActiveDataProvider
    {
        $query = Author::find()
            ->orderBy(['full_name' => SORT_ASC]);

        $dp = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 20],
        ]);

        $this->load($params);
        if (!$this->validate()) {
            $query->andFilterWhere(['like', 'full_name', $this->fu]);
        }

        return $dp;
    }
}