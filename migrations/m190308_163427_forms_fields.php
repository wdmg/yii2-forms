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
            'name' => $this->string(64),
            'placeholder' => $this->string(124),
            'description' => $this->string(255),
            'type' => $this->smallInteger(2)->notNull(),
            'sort_order' => $this->smallInteger(3)->defaultValue(10),
            'params' => $this->text(),
            'is_required' => $this->boolean(),
            'status' => $this->tinyInteger(1)->null()->defaultValue(0),
            'created_at' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
            'created_by' => $this->integer(11)->null(),
            'updated_at' => $this->datetime()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_by' => $this->integer(11)->null(),
        ], $tableOptions);

        $this->createIndex(
            '{{%idx_forms_fields}}',
            '{{%forms_fields}}',
            [
                'id',
                'form_id',
                'label',
                'name',
                'type',
                'sort_order',
                'status'
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

        // If exist module `Users` set foreign key `created_by`, `updated_by` to `users.id`
        if (class_exists('\wdmg\users\models\Users')) {
            $this->createIndex('{{%idx_forms_fields_created}}','{{%forms_fields}}', ['created_by'],false);
            $this->createIndex('{{%idx_forms_fields_updated}}','{{%forms_fields}}', ['updated_by'],false);
            $userTable = \wdmg\users\models\Users::tableName();
            $this->addForeignKey(
                'fk_forms_fields_to_users1',
                '{{%forms_fields}}',
                'created_by',
                $userTable,
                'id',
                'NO ACTION',
                'CASCADE'
            );
            $this->addForeignKey(
                'fk_forms_fields_to_users2',
                '{{%forms_fields}}',
                'updated_by',
                $userTable,
                'id',
                'NO ACTION',
                'CASCADE'
            );
        }

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('{{%idx_forms_fields}}', '{{%forms_fields}}');

        $this->dropForeignKey(
            'fk_forms_fields_to_forms',
            '{{%forms_fields}}'
        );

        if (class_exists('\wdmg\users\models\Users')) {
            $this->dropIndex('{{%idx_forms_fields_created}}', '{{%forms_fields}}');
            $this->dropIndex('{{%idx_forms_fields_updated}}', '{{%forms_fields}}');
            $userTable = \wdmg\users\models\Users::tableName();
            if (!(Yii::$app->db->getTableSchema($userTable, true) === null)) {
                $this->dropForeignKey(
                    'fk_forms_fields_to_users1',
                    '{{%forms_fields}}'
                );
                $this->dropForeignKey(
                    'fk_forms_fields_to_users2',
                    '{{%forms_fields}}'
                );
            }
        }

        $this->truncateTable('{{%forms_fields}}');
        $this->dropTable('{{%forms_fields}}');
    }

}
