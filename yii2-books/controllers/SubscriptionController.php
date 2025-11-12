<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use app\models\Subscription;
use app\models\Author;

class SubscriptionController extends Controller
{
    public $layout = 'main';

    public function actionCreate(int $id, string $name)
    {
        $author = Author::findOne($id);
        if (!$author) {
            throw new NotFoundHttpException("Автор не найден");
        }

        $model = new Subscription();
        $model->author_id = $author->id;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $msg = sprintf(
                'Вы подписались на новые книги автора %s.',
                $author->full_name
            );

            try {
                $res = Yii::$app->smsPilot->send($model->phone, $msg);
                if (!$res['ok']) {
                    Yii::$app->session->setFlash('warning', 'Подписка создана, но SMS не отправлена: ' . $res['error']);
                } else {
                    Yii::$app->session->setFlash('success', 'Вы успешно подписались! Мы отправили SMS-подтверждение.');
                }
            } catch (\Throwable $e) {
                Yii::error(['sms_exception' => $e->getMessage()], __METHOD__);
                Yii::$app->session->setFlash('warning', 'Подписка создана, но SMS не отправлена.');
            }

            return $this->redirect(['author/view', 'id' => $author->id]);
        }

        return $this->render('create', [
            'model' => $model,
            'author' => $author,
        ]);
    }
}