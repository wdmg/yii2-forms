<?php

namespace wdmg\forms\models;

use Yii;

/**
 * This is the model class for table "{{%forms_content}}".
 *
 * @property int $id
 * @property int $submit_id
 * @property int $input_id
 * @property string $value
 *
 * @property FormsFields $input
 * @property FormsSubmits $submit
 */
class Content extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%forms_content}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['submit_id', 'input_id'], 'required'],
            [['submit_id', 'input_id'], 'integer'],
            [['value'], 'string'],
            [['input_id'], 'exist', 'skipOnError' => true, 'targetClass' => FormsFields::className(), 'targetAttribute' => ['input_id' => 'id']],
            [['submit_id'], 'exist', 'skipOnError' => true, 'targetClass' => FormsSubmits::className(), 'targetAttribute' => ['submit_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app/modules/forms', 'ID'),
            'submit_id' => Yii::t('app/modules/forms', 'Submit ID'),
            'input_id' => Yii::t('app/modules/forms', 'Input ID'),
            'value' => Yii::t('app/modules/forms', 'Value'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInput()
    {
        return $this->hasOne(FormsFields::className(), ['id' => 'input_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSubmit()
    {
        return $this->hasOne(FormsSubmits::className(), ['id' => 'submit_id']);
    }
}
