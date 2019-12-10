<?php

namespace wdmg\forms\models;

use Yii;

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
            //[['form_id'], 'exist', 'skipOnError' => true, 'targetClass' => FormsList::class, 'targetAttribute' => ['form_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app/modules/forms', 'ID'),
            'form_id' => Yii::t('app/modules/forms', 'Form ID'),
            'user_id' => Yii::t('app/modules/forms', 'User ID'),
            'access_token' => Yii::t('app/modules/forms', 'Access Token'),
            'created_at' => Yii::t('app/modules/forms', 'Created At'),
            'updated_at' => Yii::t('app/modules/forms', 'Updated At'),
            'status' => Yii::t('app/modules/forms', 'Status'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFormsContents()
    {
        return $this->hasMany(FormsContent::class, ['submit_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getForm()
    {
        return $this->hasOne(FormsList::class, ['id' => 'form_id']);
    }
}
