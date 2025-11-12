<?php

namespace app\models;

use Yii;
use yii\data\ActiveDataProvider;

class BookSearch extends Book
{
    public function rules(): array
    {
        return [
            [['title'], 'safe'],
            [['published_year'], 'integer']
        ];
    }

    public function search($params): ActiveDataProvider
    {
        $query = Book::find()
            ->with(['authors'])
            ->orderBy(['published_year' => SORT_DESC]);

        $dp = new ActiveDataProvider([
           'query' => $query,
           'pagination' => ['pageSize' => 20],
        ]);

        $this->load($params);
        if ($this->validate()) {
            $query->andFilterWhere(['like', 'title', $this->title]);
            $query->andFilterWhere(['published_year' => $this->published_year]);
        }

        return $dp;
    }
}