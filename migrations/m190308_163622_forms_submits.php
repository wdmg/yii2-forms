<?php

use yii\db\Migration;

/**
 * Class m190308_163622_forms_submits
 */
class m190308_163622_forms_submits extends Migration
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

        $this->createTable('{{%forms_submits}}', [
            'id' => $this->primaryKey(11),
            'form_id' => $this->integer(11)->notNull(),
            'user_id' => $this->integer(11),
            'access_token' => $this->string(32)->notNull(),
            'created_at' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->datetime()->defaultExpression('CURRENT_TIMESTAMP'),
            'status' => $this->smallInteger()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->createIndex(
            'idx_forms_submits',
            '{{%forms_submits}}',
            [
                'id',
                'form_id',
                'user_id',
            ]
        );

        if (!(Yii::$app->db->getTableSchema('{{%forms_submits}}', true) === null)) {
            $this->addForeignKey(
                'fk_forms_submits_to_list',
                '{{%forms_submits}}',
                'form_id',
                '{{%forms_list}}',
                'id',
                'CASCADE',
                'CASCADE'
            );
        }

        // If exist module `Users` set foreign key `user_id` to `users.id`
        if(class_exists('\wdmg\users\models\Users') && isset(Yii::$app->modules['users'])) {
            $userTable = \wdmg\users\models\Users::tableName();
            if (!(Yii::$app->db->getTableSchema('{{%forms_submits}}', true) === null)) {
                $this->addForeignKey(
                    'fk_forms_submits_to_users',
                    '{{%forms_submits}}',
                    'user_id',
                    $userTable,
                    'id',
                    'CASCADE',
                    'CASCADE'
                );
            }
        }

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->truncateTable('{{%forms_submits}}');
        $this->dropTable('{{%forms_submits}}');
    }

}
