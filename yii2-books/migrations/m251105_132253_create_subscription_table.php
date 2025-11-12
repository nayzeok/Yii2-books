<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%subscription}}`.
 */
class m251105_132253_create_subscription_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%subscription}}', [
            'id' => $this->primaryKey()->comment('ID'),
            'author_id' => $this->integer()->notNull()->comment('ID автора'),
            'phone' => $this->string(32)->notNull()->comment('Телефон'),
            'subscriber_name' => $this->string()->defaultValue(null)->comment('Имя подписчика'),
            'created_at' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP')->comment('Создано'),
        ]);

        $this->createIndex('idx-subscription-author_id-phone', '{{%subscription}}', ['author_id', 'phone'], true);

        $this->addForeignKey(
            'fk-subscription-author_id',
            '{{%subscription}}',
            'author_id',
            '{{%author}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-subscription-author_id', '{{%subscription}}');
        $this->dropTable('{{%subscription}}');
    }
}
