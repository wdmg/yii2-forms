<?php

namespace wdmg\forms\models;

use Yii;
use yii\helpers\ArrayHelper;
use wdmg\helpers\StringHelper;

/**
 * This is the model class for table "{{%forms_submits}}".
 *
 * @property int $id
 * @property int $form_id
 * @property int $user_id
 * @property string $access_token
 * @property string $created_at
 * @property string $updated_at
 * @property int $status
 *
 * @property FormsContent[] $formsContents
 * @property FormsList $form
 */
class Submits extends \yii\db\ActiveRecord
{

    const STATUS_NOT_SUBMITTED = 0; // Form data has not submitted
    const STATUS_SUBMITTED = 1; // Form data has been submitted

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%forms_submits}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['form_id', 'access_token'], 'required'],
            [['form_id', 'user_id', 'status'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['access_token'], 'string', 'max' => 32],
            [['form_id'], 'exist', 'skipOnError' => true, 'targetClass' => Forms::class, 'targetAttribute' => ['form_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app/modules/forms', 'ID'),
            'form' => Yii::t('app/modules/forms', 'Form'),
            'form_id' => Yii::t('app/modules/forms', 'Form ID'),
            'user' => Yii::t('app/modules/forms', 'User'),
            'user_id' => Yii::t('app/modules/forms', 'User ID'),
            'access_token' => Yii::t('app/modules/forms', 'Access token'),
            'created_at' => Yii::t('app/modules/forms', 'Created at'),
            'updated_at' => Yii::t('app/modules/forms', 'Updated at'),
            'status' => Yii::t('app/modules/forms', 'Status'),
        ];
    }

    /**
     * @return object of \yii\db\ActiveQuery
     */
    public function getAllForms($cond = null, $select = ['id', 'name'], $asArray = false)
    {
        if ($cond) {
            if ($asArray)
                return Forms::find()->select($select)->where($cond)->asArray()->indexBy('id')->all();
            else
                return Forms::find()->select($select)->where($cond)->all();

        } else {
            if ($asArray)
                return Forms::find()->select($select)->asArray()->indexBy('id')->all();
            else
                return Forms::find()->select($select)->all();
        }
    }

    /**
     * @return array
     */
    public function getAllFormsList($allTags = false)
    {
        $list = [];
        if ($allTags)
            $list['*'] = Yii::t('app/modules/forms', 'All forms');

        if ($tags = $this->getAllForms(['source_id' => null], ['id', 'name'], true)) {
            $list = ArrayHelper::merge($list, ArrayHelper::map($tags, 'id', 'name'));
        }

        return $list;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFields()
    {
        return $this->hasMany(Fields::class, ['form_id' => 'form_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFormsContents()
    {
        return $this->hasMany(Content::class, ['submit_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getContents()
    {
        $output = [];

        $inputs = [];
        $fields = $this->getFields()->asArray()->asArray()->all();
        foreach ($fields as $field) {
            $inputs[$field["id"]] = [
                'name' => $field["name"],
                'label' => $field["label"]
            ];
        }

        $submits = [];
        $contents = $this->getFormsContents()->asArray()->all();
        foreach ($contents as $content) {
            $submits[$content["input_id"]] = $content["value"];
        }

        foreach ($submits as $id => $value) {
            if (isset($inputs[$id])) {
                $field = $inputs[$id];
                $output[$field["name"]] = [
                    'label' => $field["label"],
                    'value' => StringHelper::stripTags($value, '', '')
                ];
            }
        }
        return $output;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getForm()
    {
        return $this->hasOne(Forms::class, ['id' => 'form_id']);
    }

    /**
     * @return object of \yii\db\ActiveQuery
     */
    public function getUser()
    {
        if (class_exists('\wdmg\users\models\Users'))
            return $this->hasOne(\wdmg\users\models\Users::class, ['id' => 'user_id']);
        else
            return $this->created_by;
    }

    /**
     * @return array
     */
    public function getStatusesList($allStatuses = false)
    {
        if ($allStatuses)
            return [
                '*' => Yii::t('app/modules/forms', 'All statuses'),
                self::STATUS_NOT_SUBMITTED => Yii::t('app/modules/forms', 'Not submitted'),
                self::STATUS_SUBMITTED => Yii::t('app/modules/forms', 'Submitted'),
            ];
        else
            return [
                self::STATUS_NOT_SUBMITTED => Yii::t('app/modules/forms', 'Not submitted'),
                self::STATUS_SUBMITTED => Yii::t('app/modules/forms', 'Submitted'),
            ];
    }
}
