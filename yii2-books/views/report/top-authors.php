<?php
/** @var yii\web\View $this */
/** @var array $rows */
/** @var int $year */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = "ТОП-10 авторов за {$year}";
$this->params['breadcrumbs'][] = ['label' => 'Отчёты', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

    <h1><?= Html::encode($this->title) ?></h1>

    <form method="get" action="<?= Url::to(['report/top-authors']) ?>" class="mb-3">
        <label for="year">Год:</label>
        <input type="number" id="year" name="year" value="<?= Html::encode($year) ?>" min="0" max="3000">
        <button class="btn btn-primary">Показать</button>
    </form>

<?php if (empty($rows)): ?>
    <div class="alert alert-info">За выбранный год данных нет.</div>
<?php else: ?>
    <table class="table table-striped table-bordered">
        <thead>
        <tr>
            <th>#</th>
            <th>Автор</th>
            <th>Книг</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($rows as $i => $r): ?>
            <tr>
                <td><?= $i + 1 ?></td>
                <td><?= Html::a(Html::encode($r['full_name']), ['author/view', 'id' => $r['author_id']]) ?></td>
                <td><?= (int)$r['books_count'] ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>