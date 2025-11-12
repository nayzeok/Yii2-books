<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use app\models\Subscription;
use yii\db\Expression;

/**
 * Модель книги.
 *
 * Таблица: {{%book}}
 *
 * @property int         $id
 * @property string      $title
 * @property int         $published_year
 * @property string|null $description
 * @property string|null $isbn
 * @property string|null $cover_path
 *
 * Отношения:
 * @property Author[]    $authors
 *
 * Вспомогательные:
 * @property int[]       $author_ids ID авторов, передаваемые из формы
 */
class Book extends ActiveRecord
{
    /** @var int[] список ID авторов из формы */
    public array $author_ids = [];

    public static function tableName(): string
    {
        return '{{%book}}';
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['title', 'published_year', 'description'], 'required'],
            [['published_year'], 'integer', 'min' => 0, 'max' => 3000],
            [['title'], 'string', 'max' => 255],
            [['isbn'], 'string', 'max' => 13],
            [['cover_path', 'description'], 'string', 'max' => 512],
            [['author_ids'], 'each', 'rule' => ['integer']],
        ];
    }

    /**
     * @return string[]
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Название',
            'isbn' => 'ISBN',
            'published_year' => 'Год выпуска',
            'description' => 'Описание',
            'author_ids' => 'Авторы'
        ];
    }

    /**
     * Связь: многие-ко-многим с авторами через таблицу {{%book_author}}.
     *
     * @return \yii\db\ActiveQuery
     * @throws \yii\base\InvalidConfigException
     */
    public function getAuthors()
    {
        return $this->hasMany(Author::class, ['id' => 'author_id'])
            ->viaTable('{{%book_author}}', ['book_id' => 'id']);
    }

    /**
     * Публичный URL/путь к обложке (если не задано — пустая строка).
     *
     * @return string
     */
    public function getCoverUrl(): string
    {
        return (string)($this->cover_path ?? '');
    }

    /**
     * После сохранения:
     *  - синхронизирует связи «книга—авторы» по $author_ids;
     *  - при создании книги отправляет SMS подписчикам соответствующих авторов.
     *
     * @param bool  $insert             true, если новая запись
     * @param array $changedAttributes  изменённые атрибуты
     *
     * @throws \yii\db\Exception
     * @return void
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        $newIds = array_values(array_unique(array_map('intval', (array)$this->author_ids)));

        $tx = Yii::$app->db->beginTransaction();
        try {
            $this->unlinkAll('authors', true);

            if (!empty($newIds)) {
                $authors = Author::find()->where(['id' => $newIds])->all();
                foreach ($authors as $author) {
                    $this->link('authors', $author);
                }
            }

            $tx->commit();
        } catch (\Throwable $e) {
            $tx->rollBack();
            Yii::error(['author_sync_error' => $e->getMessage()], __METHOD__);
        }

        if ($insert) {
            try {
                $authorIds = $this->getAuthors()->select('id')->column();
                if (empty($authorIds)) {
                    return;
                }

                $phones = Subscription::find()
                    ->distinct()
                    ->select('phone')
                    ->where(['author_id' => $authorIds])
                    ->andWhere(['not', ['phone' => null]])
                    ->column();

                $phones = array_unique(array_filter($phones));
                if (empty($phones)) {
                    return;
                }

                $authorsNames = Author::find()
                    ->select('full_name')
                    ->where(['id' => $authorIds])
                    ->orderBy(['full_name' => SORT_ASC])
                    ->column();

                $msg = sprintf(
                    'Новая книга: %s (%s). Автор(ы): %s.',
                    $this->title,
                    $this->published_year,
                    implode(', ', $authorsNames)
                );

            } catch (\Throwable $e) {
                Yii::error(['sms_notify_error' => $e->getMessage()], __METHOD__);
            }
        }
    }
}