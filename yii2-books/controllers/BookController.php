<?php

namespace app\controllers;

use app\models\Author;
use Yii;
use app\models\Book;
use app\models\BookSearch;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

class BookController extends Controller
{
    /**
     * @return array
     */
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index', 'view'],
                        'roles' => ['?', '@'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['create', 'update', 'delete'],
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => ['delete' => ['POST']],
            ],
        ];
    }

    /**
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new BookSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param int $id
     * @return int
     */
    public function actionView(int $id): string
    {
        $model = Book::findOne($id);
        return $this->render('view', [
            'model' => $model,
        ]);
    }

    public function actionCreate(): Response|string
    {
        $model = new Book();

        if ($model->load(Yii::$app->request->post())) {
            $post = Yii::$app->request->post('Book', []);
            $model->author_ids = (array)($post['author_ids'] ?? []);

            if ($model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        $authors = ArrayHelper::map(
            Author::find()
                ->select(['id', 'full_name'])
                ->orderBy(['full_name' => SORT_ASC])
                ->asArray()
                ->all(),
            'id',
            'full_name'
        );

        return $this->render('create', [
            'model' => $model,
            'authors' => $authors,
        ]);
    }

    public function actionUpdate(int $id): Response|string
    {
        $model = $this->findModel($id);

        $model->author_ids = $model->getAuthors()->select('id')->column();

        $authors = ArrayHelper::map(
            Author::find()
                ->select(['id', 'full_name'])
                ->orderBy(['full_name' => SORT_ASC])
                ->asArray()
                ->all(),
            'id',
            'full_name'
        );

        if ($model->load(Yii::$app->request->post())) {
            $post = Yii::$app->request->post('Book', []);
            $model->author_ids = (array)($post['author_ids'] ?? []);
            if ($model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('update', [
            'model' => $model,
            'authors' => $authors,
        ]);
    }

    public function actionDelete(int $id): Response
    {
        $this->findModel($id)->delete();
        return $this->redirect(['index']);
    }

    protected function findModel(int $id): Book
    {
        if (($model = Book::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('Книга не найдена');
    }
}