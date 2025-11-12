<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%book}}`.
 */
class m251105_131517_create_book_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%book}}', [
            'id' => $this->primaryKey(),
            'title' => $this->string()->notNull()->notNull()->comment('Артикул поставщика'),
            'published_year' => $this->integer()->notNull()->notNull()->comment('Год публикации'),
            'description' => $this->text()->notNull()->notNull()->comment('Описание'),
            'isbn' => $this->string()->notNull()->comment('ISBN'),
            'cover_path' => $this->string()->defaultValue(null)->comment('Путь к обложке'),
            'created_at' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP')->comment('Создано'),
            'updated_at' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP')->append('ON UPDATE CURRENT_TIMESTAMP')->comment('Обновлено'),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%book}}');
    }
}
