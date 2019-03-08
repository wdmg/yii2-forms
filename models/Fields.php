<?php

namespace wdmg\forms\models;

use Yii;

/**
 * This is the model class for table "{{%forms_fields}}".
 *
 * @property int $id
 * @property int $form_id
 * @property string $label
 * @property string $description
 * @property int $type
 * @property int $sort_order
 * @property string $params
 * @property int $is_required
 *
 * @property FormsContent[] $formsContents
 * @property FormsList $form
 */
class Fields extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%forms_fields}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['form_id', 'type'], 'required'],
            [['form_id', 'type', 'sort_order', 'is_required'], 'integer'],
            [['params'], 'string'],
            [['label'], 'string', 'max' => 64],
            [['description'], 'string', 'max' => 255],
            //[['form_id'], 'exist', 'skipOnError' => true, 'targetClass' => FormsList::className(), 'targetAttribute' => ['form_id' => 'id']],
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
            'label' => Yii::t('app/modules/forms', 'Label'),
            'description' => Yii::t('app/modules/forms', 'Description'),
            'type' => Yii::t('app/modules/forms', 'Type'),
            'sort_order' => Yii::t('app/modules/forms', 'Sort Order'),
            'params' => Yii::t('app/modules/forms', 'Params'),
            'is_required' => Yii::t('app/modules/forms', 'Is Required'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFormsContents()
    {
        return $this->hasMany(FormsContent::className(), ['input_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getForm()
    {
        return $this->hasOne(FormsList::className(), ['id' => 'form_id']);
    }
}
