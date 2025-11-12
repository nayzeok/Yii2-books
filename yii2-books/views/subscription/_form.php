<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\Subscription $model */
/** @var app\models\Author $author */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="subscription-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'author_id')->hiddenInput(['value' => $author->id])->label(false) ?>

    <div class="form-group">
        <label>Автор:</label>
        <p><strong><?= Html::encode($author->full_name) ?></strong></p>
    </div>

    <?= $form->field($model, 'phone')->textInput(['maxlength' => true, 'placeholder' => '+79001234567']) ?>

    <div class="form-group">
        <?= Html::submitButton('Подписаться', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>