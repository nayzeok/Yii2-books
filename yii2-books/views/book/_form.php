<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\Book $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="book-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'published_year')->textInput() ?>

    <?= $form->field($model, 'isbn')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'description')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'cover_path')->textInput(['maxlength' => true]) ?>

    <?php if (!empty($authors)): ?>
        <?= $form->field($model, 'author_ids')->checkboxList($authors) ?>
    <?php else: ?>
        <div class="alert alert-warning">
            Нет доступных авторов. Сначала <a href="<?= \yii\helpers\Url::to(['/author/create']) ?>">создайте автора</a>.
        </div>
    <?php endif; ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
