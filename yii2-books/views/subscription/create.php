<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Subscription $model */
/** @var app\models\Author $author */

$this->title = 'Create Subscription';
$this->params['breadcrumbs'][] = ['label' => 'Subscriptions', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="subscription-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'author' => $author,
    ]) ?>

</div>
