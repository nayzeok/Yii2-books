<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use app\models\Subscription;
use app\models\Author;
use yii\web\Response;

/**
 * Контроллер для оформления подписки на автора.
 *
 * Позволяет гостю подписаться на SMS-уведомления о новых книгах автора.
 */
class SubscriptionController extends Controller
{
    /** @var string Основной layout для страниц подписки */
    public $layout = 'main';

    /**
     * Создание подписки на автора.
     *
     * @param int $id ID автора
     * @param string $name Имя автора (используется в URL)
     *
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException Если автор не найден
     */
    public function actionCreate(int $id, string $name): string|Response
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