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
            'alias' => $this->string(64)->notNull()->unique(),
            'title' => $this->string(255),
            'description' => $this->text(),
            'status' => $this->tinyInteger(1)->null()->defaultValue(0),
            'created_at' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
            'created_by' => $this->integer(11)->null(),
            'updated_at' => $this->datetime()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_by' => $this->integer(11)->null(),
        ], $tableOptions);

        $this->createIndex('{{%idx-forms-alias}}', '{{%forms_list}}', ['name', 'alias', 'status']);

        // If exist module `Users` set foreign key `created_by`, `updated_by` to `users.id`
        if (class_exists('\wdmg\users\models\Users')) {
            $this->createIndex('{{%idx-forms-created}}','{{%forms_list}}', ['created_by'],false);
            $this->createIndex('{{%idx-forms-updated}}','{{%forms_list}}', ['updated_by'],false);
            $userTable = \wdmg\users\models\Users::tableName();
            $this->addForeignKey(
                'fk_forms_to_users1',
                '{{%forms_list}}',
                'created_by',
                $userTable,
                'id',
                'NO ACTION',
                'CASCADE'
            );
            $this->addForeignKey(
                'fk_forms_to_users2',
                '{{%forms_list}}',
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
        $this->dropIndex('{{%idx-forms-alias}}', '{{%forms_list}}');

        if (class_exists('\wdmg\users\models\Users')) {
            $this->dropIndex('{{%idx-forms-created}}', '{{%forms_list}}');
            $this->dropIndex('{{%idx-forms-updated}}', '{{%forms_list}}');
            $userTable = \wdmg\users\models\Users::tableName();
            if (!(Yii::$app->db->getTableSchema($userTable, true) === null)) {
                $this->dropForeignKey(
                    'fk_forms_to_users1',
                    '{{%forms_list}}'
                );
                $this->dropForeignKey(
                    'fk_forms_to_users2',
                    '{{%forms_list}}'
                );
            }
        }

        $this->truncateTable('{{%forms_list}}');
        $this->dropTable('{{%forms_list}}');
    }

}
