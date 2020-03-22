<?php

namespace wdmg\forms\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\SluggableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "{{%forms_list}}".
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string $title
 * @property string $description
 * @property int $status
 * @property string $created_at
 * @property int $created_by
 * @property string $updated_at
 * @property int $updated_by
 *
 * @property FormsFields[] $formsFields
 * @property FormsSubmits[] $formsSubmits
 */
class Forms extends \yii\db\ActiveRecord
{

    const FORM_STATUS_DRAFT = 0; // Form has draft
    const FORM_STATUS_PUBLISHED = 1; // Form has been published

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
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'created_at',
                    ActiveRecord::EVENT_BEFORE_UPDATE => 'updated_at',
                ],
                'value' => new Expression('NOW()'),
            ],
            'blameable' => [
                'class' => BlameableBehavior::class,
                'createdByAttribute' => 'created_by',
                'updatedByAttribute' => 'updated_by',
            ],
            'sluggable' =>  [
                'class' => SluggableBehavior::class,
                'attribute' => ['name'],
                'slugAttribute' => 'alias',
                'ensureUnique' => true,
                'skipOnEmpty' => true,
                'immutable' => true,
                'value' => function ($event) {
                    return mb_substr($this->name, 0, 32);
                }
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $rules = [
            [['name', 'alias'], 'required'],
            [['description'], 'string'],
            [['status'], 'boolean'],
            [['name', 'alias'], 'string', 'max' => 64],
            [['title'], 'string', 'max' => 255],
            [['alias'], 'unique', 'message' => Yii::t('app/modules/forms', 'Param attribute must be unique.')],
            [['created_at', 'updated_at'], 'safe'],
        ];

        if (class_exists('\wdmg\users\models\Users')) {
            $rules[] = [['created_by', 'updated_by'], 'safe'];
        }

        return $rules;
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app/modules/forms', 'ID'),
            'name' => Yii::t('app/modules/forms', 'Name'),
            'alias' => Yii::t('app/modules/forms', 'Alias'),
            'title' => Yii::t('app/modules/forms', 'Title'),
            'description' => Yii::t('app/modules/forms', 'Description'),
            'status' => Yii::t('app/modules/forms', 'Status'),
            'created_at' => Yii::t('app/modules/forms', 'Created at'),
            'created_by' => Yii::t('app/modules/forms', 'Created by'),
            'updated_at' => Yii::t('app/modules/forms', 'Updated at'),
            'updated_by' => Yii::t('app/modules/forms', 'Updated by'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFormsFields()
    {
        return $this->hasMany(FormsFields::class, ['form_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFormsSubmits()
    {
        return $this->hasMany(FormsSubmits::class, ['form_id' => 'id']);
    }

    /**
     * @return object of \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        if (class_exists('\wdmg\users\models\Users'))
            return $this->hasOne(\wdmg\users\models\Users::class, ['id' => 'created_by']);
        else
            return $this->created_by;
    }

    /**
     * @return object of \yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        if (class_exists('\wdmg\users\models\Users'))
            return $this->hasOne(\wdmg\users\models\Users::class, ['id' => 'updated_by']);
        else
            return $this->updated_by;
    }

    /**
     * @return array
     */
    public function getStatusesList($allStatuses = false)
    {
        if ($allStatuses)
            return [
                '*' => Yii::t('app/modules/forms', 'All statuses'),
                self::FORM_STATUS_DRAFT => Yii::t('app/modules/forms', 'Draft'),
                self::FORM_STATUS_PUBLISHED => Yii::t('app/modules/forms', 'Published'),
            ];
        else
            return [
                self::FORM_STATUS_DRAFT => Yii::t('app/modules/forms', 'Draft'),
                self::FORM_STATUS_PUBLISHED => Yii::t('app/modules/forms', 'Published'),
            ];
    }
}
