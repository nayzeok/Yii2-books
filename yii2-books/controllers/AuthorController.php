<?php

namespace app\controllers;

use app\models\Author;
use app\models\AuthorSearch;
use app\models\Subscription;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class AuthorController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index', 'view', 'subscribe'],
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
                'actions' => [
                    'delete' => ['POST'],
                    'subscribe' => ['POST'],
                ]
            ]
        ];
    }

    public function actionIndex()
    {
        $searchModel = new AuthorSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView(int $id)
    {
        $model = $this->findModel($id);
        $subscription = new Subscription(['author_id' => $id]);
        return $this->render('view', [
            'model' => $model,
            'subscription' => $subscription
        ]);
    }

    public function actionCreate()
    {
        $model = new Author();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }
        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionUpdate(int $id)
    {
        $model = Author::findOne($id);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }
        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionDelete(int $id)
    {
        $model = Author::findOne($id)
            ->delete();
        return $this->redirect(['index']);
    }

    public function actionSubscribe(int $id)
    {
        $author = $this->findModel($id);
        $model = new Subscription(['author_id' => $author->id]);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Вы подписанны на уведомления о новых книгах автора');
            return $this->redirect(['view', 'id' => $author->id]);
        }

        Yii::$app->session->setFlash('error', 'Не удалось оформить подписку');
        return $this->redirect(['view', 'id' => $author->id]);
    }

    protected function findModel(int $id)
    {
        if (($model = Author::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Автор не найден');
    }
}