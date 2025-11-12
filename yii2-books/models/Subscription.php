<?php

namespace app\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * @property int         $id
 * @property int         $author_id
 * @property string      $phone
 * @property string|null $subscriber_name
 * @property int         $created_at
 *
 * @property Author      $author
 */
class Subscription extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%subscription}}';
    }

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

    public function getAuthor(): ActiveQuery
    {
        return $this->hasOne(Author::class, ['id' => 'author_id']);
    }
}