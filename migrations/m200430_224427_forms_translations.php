<?php

use yii\db\Migration;

/**
 * Class m200430_224427_forms_translations
 */
class m200430_224427_forms_translations extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $defaultLocale = null;
        if (isset(Yii::$app->sourceLanguage))
            $defaultLocale = Yii::$app->sourceLanguage;

        if (is_null($this->getDb()->getSchema()->getTableSchema('{{%forms_list}}')->getColumn('source_id'))) {
            $this->addColumn('{{%forms_list}}', 'source_id', $this->integer()->null()->after('id'));

            // Setup foreign key to source id
            $this->createIndex('{{%idx-forms_list-source}}', '{{%forms_list}}', ['source_id']);
            $this->addForeignKey(
                'fk_forms_list_to_source',
                '{{%forms_list}}',
                'source_id',
                '{{%forms_list}}',
                'id',
                'NO ACTION',
                'CASCADE'
            );
        }
        
        if (is_null($this->getDb()->getSchema()->getTableSchema('{{%forms_list}}')->getColumn('locale'))) {

            $this->addColumn('{{%forms_list}}', 'locale', $this->string(10)->defaultValue($defaultLocale)->after('status'));
            $this->createIndex('{{%idx-forms_list-locale}}', '{{%forms_list}}', ['locale']);

            // If module `Translations` exist setup foreign key `locale` to `trans_langs.locale`
            if (class_exists('\wdmg\translations\models\Languages')) {
                $langsTable = \wdmg\translations\models\Languages::tableName();
                $this->addForeignKey(
                    'fk_forms_list_to_langs',
                    '{{%forms_list}}',
                    'locale',
                    $langsTable,
                    'locale',
                    'NO ACTION',
                    'CASCADE'
                );
            }
        }

        if (is_null($this->getDb()->getSchema()->getTableSchema('{{%forms_fields}}')->getColumn('source_id'))) {
            $this->addColumn('{{%forms_fields}}', 'source_id', $this->integer()->null()->after('id'));

            // Setup foreign key to source id
            $this->createIndex('{{%idx-forms_fields-source}}', '{{%forms_fields}}', ['source_id']);
            $this->addForeignKey(
                'fk_forms_fields_to_source',
                '{{%forms_fields}}',
                'source_id',
                '{{%forms_fields}}',
                'id',
                'NO ACTION',
                'CASCADE'
            );
        }
        
        if (is_null($this->getDb()->getSchema()->getTableSchema('{{%forms_fields}}')->getColumn('locale'))) {

            $this->addColumn('{{%forms_fields}}', 'locale', $this->string(10)->defaultValue($defaultLocale)->after('status'));
            $this->createIndex('{{%idx-forms_fields-locale}}', '{{%forms_fields}}', ['locale']);

            // If module `Translations` exist setup foreign key `locale` to `trans_langs.locale`
            if (class_exists('\wdmg\translations\models\Languages')) {
                $langsTable = \wdmg\translations\models\Languages::tableName();
                $this->addForeignKey(
                    'fk_forms_fields_to_langs',
                    '{{%forms_fields}}',
                    'locale',
                    $langsTable,
                    'locale',
                    'NO ACTION',
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
        if (!is_null($this->getDb()->getSchema()->getTableSchema('{{%forms_list}}')->getColumn('source_id'))) {
            $this->dropIndex('{{%idx-forms_list-source}}', '{{%forms_list}}');
            $this->dropColumn('{{%forms_list}}', 'source_id');
            $this->dropForeignKey(
                'fk_forms_list_to_source',
                '{{%forms_list}}'
            );
        }

        if (!is_null($this->getDb()->getSchema()->getTableSchema('{{%forms_list}}')->getColumn('locale'))) {
            $this->dropIndex('{{%idx-forms_list-locale}}', '{{%forms_list}}');
            $this->dropColumn('{{%forms_list}}', 'locale');

            if (class_exists('\wdmg\translations\models\Languages')) {
                $langsTable = \wdmg\translations\models\Languages::tableName();
                if (!(Yii::$app->db->getTableSchema($langsTable, true) === null)) {
                    $this->dropForeignKey(
                        'fk_forms_list_to_langs',
                        '{{%forms_list}}'
                    );
                }
            }
        }

        if (!is_null($this->getDb()->getSchema()->getTableSchema('{{%forms_fields}}')->getColumn('source_id'))) {
            $this->dropIndex('{{%idx-forms_fields-source}}', '{{%forms_fields}}');
            $this->dropColumn('{{%forms_fields}}', 'source_id');
            $this->dropForeignKey(
                'fk_forms_fields_to_source',
                '{{%forms_fields}}'
            );
        }

        if (!is_null($this->getDb()->getSchema()->getTableSchema('{{%forms_fields}}')->getColumn('locale'))) {
            $this->dropIndex('{{%idx-forms_fields-locale}}', '{{%forms_fields}}');
            $this->dropColumn('{{%forms_fields}}', 'locale');

            if (class_exists('\wdmg\translations\models\Languages')) {
                $langsTable = \wdmg\translations\models\Languages::tableName();
                if (!(Yii::$app->db->getTableSchema($langsTable, true) === null)) {
                    $this->dropForeignKey(
                        'fk_forms_fields_to_langs',
                        '{{%forms_fields}}'
                    );
                }
            }
        }
    }
}
