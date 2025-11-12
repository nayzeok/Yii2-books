<?php

use yii\base\Security;
use yii\db\Migration;

/**
 * Handles the creation of table `{{%user}}`.
 */
class m251105_131034_create_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%user}}', [
            'id' => $this->primaryKey()->comment('ID'),
            'username' => $this->string()->notNull()->unique()->comment('Имя пользователя'),
            'password_hash' => $this->string()->notNull()->comment('Хэш пароля'),
            'auth_key' => $this->string(32)->notNull()->comment('Ключ авторизации (cookie)'),
            'access_token' => $this->string()->defaultValue(null)->comment('Токен доступа API'),
            'created_at' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP')->comment('Создано'),
            'updated_at' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP')->append('ON UPDATE CURRENT_TIMESTAMP')->comment('Обновлено'),
        ]);

        $security = Yii::$app->security;
        $this->insert('{{%user}}', [
            'username' => 'demo',
            'password_hash' => $security->generatePasswordHash('demo'),
            'auth_key' => $security->generateRandomString(),
            'access_token' => $security->generateRandomString(64),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%user}}');
    }
}
