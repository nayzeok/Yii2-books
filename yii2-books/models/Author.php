<?php

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * Модель автора.
 *
 * Таблица: {{%author}}
 *
 * @property int    $id
 * @property string $full_name
 *
 * Отношения:
 * @property Book[] $books
 */
class Author extends ActiveRecord
{
    /**
     * @return string
     */
    public static function tableName(): string
    {
        return '{{%author}}';
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['full_name'], 'required'],
            [['full_name'], 'string', 'max' => 255],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'full_name' => 'ФИО',
        ];
    }

    /**
     * Связь: автор → книги.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBooks(): ActiveQuery
    {
        return $this->hasMany(Book::class, ['id' => 'book_id'])
            ->viaTable('{{%book_author}}', ['author_id' => 'id']);
    }
}