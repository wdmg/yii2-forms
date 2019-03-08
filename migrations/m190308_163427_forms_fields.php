<?php

use yii\db\Migration;

/**
 * Class m190308_163427_forms_fields
 */
class m190308_163427_forms_fields extends Migration
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

        $this->createTable('{{%forms_fields}}', [
            'id' => $this->primaryKey(11),
            'form_id' => $this->integer(11)->notNull(),
            'label' => $this->string(64),
            'description' => $this->string(255),
            'type' => $this->smallInteger(2)->notNull(),
            'sort_order' => $this->smallInteger(3)->defaultValue(10),
            'params' => $this->text(),
            'is_required' => $this->boolean(),
        ], $tableOptions);

        $this->createIndex(
            'idx_forms_fields',
            '{{%forms_fields}}',
            [
                'id',
                'form_id',
            ]
        );

        if (!(Yii::$app->db->getTableSchema('{{%forms_fields}}', true) === null)) {
            $this->addForeignKey(
                'fk_forms_fields_to_forms',
                '{{%forms_fields}}',
                'form_id',
                '{{%forms_list}}',
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
        $this->truncateTable('{{%forms_fields}}');
        $this->dropTable('{{%forms_fields}}');
    }

}
