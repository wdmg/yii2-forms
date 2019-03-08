<?php

use yii\db\Migration;

/**
 * Class m190308_163312_forms_list
 */
class m190308_163312_forms_list extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%forms_list}}', [
            'id' => $this->primaryKey(11),
            'name' => $this->string(64),
            'slug' => $this->string(64)->notNull()->unique(),
            'title' => $this->string(255),
            'description' => $this->text(),
            'available' => $this->boolean(),
            'created_at' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->datetime()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions);

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->truncateTable('{{%forms_list}}');
        $this->dropTable('{{%forms_list}}');
    }

}
