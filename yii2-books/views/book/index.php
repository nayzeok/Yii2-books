<?php

use app\models\Book;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var app\models\BookSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Books';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="book-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Book', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            [
                'attribute' => 'cover_path',
                'label' => 'Обложка',
                'format' => 'raw',
                'value' => function (\app\models\Book $model) {
                    return $model->coverUrl
                        ? Html::img($model->coverUrl, ['style' => 'max-width:60px; height:auto;'])
                        : '';
                },
                'contentOptions' => ['style' => 'width:80px;'],
            ],
            'title',
            'published_year',
            'description:ntext',
            'isbn',
            [
                'label' => 'Авторы',
                'format' => 'raw',
                'value' => function ($model) {
                    $authors = $model->authors;
                    if (empty($authors)) {
                        return Html::tag('span', '—', ['class' => 'text-muted']);
                    }
                    return implode(', ', array_map(static function ($author) {
                        return Html::encode($author->full_name);
                    }, $authors));
                },
            ],
            //'created_at',
            //'updated_at',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, Book $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],
        ],
    ]); ?>


</div>
