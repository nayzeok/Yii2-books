<?php

namespace app\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * Подписка на автора для SMS-уведомлений.
 *
 * Таблица: {{%subscription}}
 *
 * @property int    $id
 * @property int    $author_id
 * @property string $phone
 *
 * Отношения:
 * @property Author $author
 */
class Subscription extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%subscription}}';
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['author_id', 'phone'], 'required'],
            [['author_id', 'created_at'], 'integer'],
            ['phone', 'trim'],
            ['phone', 'string', 'max' => 32],
            ['phone', 'match', 'pattern' => '/^\+?\d{7,15}$/', 'message' => 'Укажите телефон в международном формате'],
            ['subscriber_name', 'string', 'max' => 255],
            ['author_id', 'exist', 'targetClass' => Author::class, 'targetAttribute' => 'id'],
            [['author_id', 'phone'], 'unique', 'targetAttribute' => ['author_id', 'phone'], 'message' => 'Вы уже подписаны на этого автора.'],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'id'              => 'ID',
            'author_id'       => 'Автор',
            'phone'           => 'Телефон',
            'subscriber_name' => 'Имя',
            'created_at'      => 'Создано',
        ];
    }

    /**
     * Связь: подписка → автор.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAuthor(): ActiveQuery
    {
        return $this->hasOne(Author::class, ['id' => 'author_id']);
    }
}