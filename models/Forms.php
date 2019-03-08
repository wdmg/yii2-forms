<?php

namespace wdmg\forms\models;

use Yii;

/**
 * This is the model class for table "{{%forms_list}}".
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string $title
 * @property string $description
 * @property int $available
 * @property string $created_at
 * @property string $updated_at
 *
 * @property FormsFields[] $formsFields
 * @property FormsSubmits[] $formsSubmits
 */
class Forms extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%forms_list}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['slug'], 'required'],
            [['description'], 'string'],
            [['available'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['name', 'slug'], 'string', 'max' => 64],
            [['title'], 'string', 'max' => 255],
            [['slug'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app/modules/forms', 'ID'),
            'name' => Yii::t('app/modules/forms', 'Name'),
            'slug' => Yii::t('app/modules/forms', 'Slug'),
            'title' => Yii::t('app/modules/forms', 'Title'),
            'description' => Yii::t('app/modules/forms', 'Description'),
            'available' => Yii::t('app/modules/forms', 'Available'),
            'created_at' => Yii::t('app/modules/forms', 'Created At'),
            'updated_at' => Yii::t('app/modules/forms', 'Updated At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFormsFields()
    {
        return $this->hasMany(FormsFields::className(), ['form_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFormsSubmits()
    {
        return $this->hasMany(FormsSubmits::className(), ['form_id' => 'id']);
    }
}
