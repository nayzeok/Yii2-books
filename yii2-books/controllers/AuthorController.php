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
use yii\web\Response;

class AuthorController extends Controller
{
    /**
     * @return array[]
     */
    public function behaviors(): array
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

    /**
     * @return string
     */
    public function actionIndex(): string
    {
        $searchModel = new AuthorSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param int $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView(int $id): string
    {
        $model = $this->findModel($id);
        $subscription = new Subscription(['author_id' => $id]);
        return $this->render('view', [
            'model' => $model,
            'subscription' => $subscription
        ]);
    }

    /**
     * @return string|\yii\web\Response
     * @throws \yii\db\Exception
     */
    public function actionCreate(): string|Response
    {
        $model = new Author();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }
        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * @param int $id
     * @return string|\yii\web\Response
     * @throws \yii\db\Exception
     */
    public function actionUpdate(int $id): string|Response
    {
        $model = Author::findOne($id);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }
        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * @param int $id
     * @return \yii\web\Response
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete(int $id): Response
    {
        $model = Author::findOne($id)
            ->delete();
        return $this->redirect(['index']);
    }

    /**
     * @param int $id
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     * @throws \yii\db\Exception
     */
    public function actionSubscribe(int $id): Response
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

    /**
     * @param int $id
     * @return Author|null
     * @throws NotFoundHttpException
     */
    protected function findModel(int $id): Author
    {
        if (($model = Author::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Автор не найден');
    }
}