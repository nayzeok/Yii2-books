<?php

namespace app\controllers;

use app\models\Author;
use app\models\Book;
use Yii;
use yii\web\Controller;

class ReportController extends Controller
{
    /**
     * Топ-10 авторов по количеству книг за указанный год.
     *
     * @param int|null $year Год (по умолчанию текущий)
     * @return string
     */
    public function actionTopAuthors(?int $year = null): string
    {
        $year = $year ?? (int)date('Y');

        $authorTable = Author::tableName();
        $bookTable   = Book::tableName();
        $baTable     = '{{%book_author}}';

        $rows = (new \yii\db\Query())
            ->select([
                'author_id'   => 'a.id',
                'full_name'   => 'a.full_name',
                'books_count' => 'COUNT(b.id)',
            ])
            ->from(['a' => $authorTable])
            ->innerJoin(['ba' => $baTable], 'ba.author_id = a.id')
            ->innerJoin(['b'  => $bookTable], 'b.id = ba.book_id')
            ->where(['b.published_year' => $year])
            ->groupBy(['a.id', 'a.full_name'])
            ->orderBy(['books_count' => SORT_DESC, 'a.full_name' => SORT_ASC])
            ->limit(10)
            ->all();

        return $this->render('top-authors', [
            'rows' => $rows,
            'year' => $year,
        ]);
    }
}