<?php

use yii\db\Migration;

/**
 * Class m190308_163646_forms_content
 */
class m190308_163646_forms_content extends Migration
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

        $this->createTable('{{%forms_content}}', [
            'id' => $this->primaryKey(11),
            'submit_id' => $this->integer(11)->notNull(),
            'input_id' => $this->integer(11)->notNull(),
            'value' => $this->text(),
        ], $tableOptions);

        $this->createIndex(
            'idx_forms_content',
            '{{%forms_content}}',
            [
                'id',
                'submit_id',
                'input_id',
            ]
        );

        if (!(Yii::$app->db->getTableSchema('{{%forms_content}}', true) === null)) {
            $this->addForeignKey(
                'fk_forms_content_to_submits',
                '{{%forms_content}}',
                'submit_id',
                '{{%forms_submits}}',
                'id',
                'CASCADE',
                'CASCADE'
            );

            $this->addForeignKey(
                'fk_forms_content_to_fields',
                '{{%forms_content}}',
                'input_id',
                '{{%forms_fields}}',
                'id',
                'CASCADE',
                'CASCADE'
            );
        }

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->truncateTable('{{%forms_content}}');
        $this->dropTable('{{%forms_content}}');
    }

}
