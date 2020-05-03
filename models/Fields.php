<?php

namespace wdmg\forms\models;

use Yii;
use wdmg\base\models\ActiveRecordML;
use wdmg\forms\models\Forms;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use wdmg\base\behaviors\SluggableBehavior;

/**
 * This is the model class for table "{{%forms_fields}}".
 *
 * @property int $id
 * @property int $form_id
 * @property string $name
 * @property string $label
 * @property string $description
 * @property int $type
 * @property int $sort_order
 * @property string $params
 * @property int $is_required
 * @property int $status
 *
 * @property FormsContent[] $formsContents
 * @property FormsList $form
 */
class Fields extends ActiveRecordML
{

    const STATUS_DRAFT = 0; // Form field has draft
    const STATUS_PUBLISHED = 1; // Form field has been published

    private $fieldsTypes = [
        1 => 'text',
        2 => 'textarea',
        3 => 'checkbox',
        4 => 'file',
        5 => 'hidden',
        6 => 'password',
        7 => 'radio',
        8 => 'color',
        9 => 'date',
        10 => 'datetime',
        11 => 'datetime-local',
        12 => 'email',
        13 => 'number',
        14 => 'range',
        15 => 'search',
        16 => 'tel',
        17 => 'time',
        18 => 'url',
        19 => 'month',
        20 => 'week'
    ];

    public $attribute;

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
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['sluggable'] = [
            'class' => SluggableBehavior::class,
            'attribute' => 'label',
            'slugAttribute' => 'name',
            'replacement' => '_'
        ];
        return $behaviors;
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $rules = [
            [['form_id', 'label', 'type'], 'required'],
            [['form_id', 'type', 'sort_order'], 'integer'],
            [['params'], 'string'],
            [['label', 'name'], 'string', 'max' => 64],
            ['name', 'match', 'pattern' => '/^[A-za-z]/', 'message' => Yii::t('app/modules/forms','The attribute must begin with a letter.')],
            ['name', 'match', 'pattern' => '/^[A-Za-z0-9\_]+$/', 'message' => Yii::t('app/modules/forms','It allowed only Latin alphabet, numbers and «_» character.')],
            [['status', 'is_required'], 'boolean'],
            [['placeholder'], 'string', 'max' => 124],
            [['description'], 'string', 'max' => 255],
            [['form_id'], 'exist', 'skipOnError' => false, 'targetClass' => Forms::class, 'targetAttribute' => ['form_id' => 'id']],
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
            'form_id' => Yii::t('app/modules/forms', 'Form ID'),
            'label' => Yii::t('app/modules/forms', 'Field label'),
            'name' => Yii::t('app/modules/forms', 'Input name'),
            'placeholder' => Yii::t('app/modules/forms', 'Placeholder'),
            'description' => Yii::t('app/modules/forms', 'Description'),
            'type' => Yii::t('app/modules/forms', 'Type'),
            'sort_order' => Yii::t('app/modules/forms', 'Sort order'),
            'params' => Yii::t('app/modules/forms', 'Params'),
            'is_required' => Yii::t('app/modules/forms', 'Is required?'),
            'status' => Yii::t('app/modules/forms', 'Status'),
            'created_at' => Yii::t('app/modules/forms', 'Created at'),
            'created_by' => Yii::t('app/modules/forms', 'Created by'),
            'updated_at' => Yii::t('app/modules/forms', 'Updated at'),
            'updated_by' => Yii::t('app/modules/forms', 'Updated by'),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function afterFind()
    {
        parent::afterFind();
        $this->attribute = str_replace('-', '_', $this->name);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFormsContents()
    {
        return $this->hasMany(Content::class, ['input_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getForm()
    {
        return $this->hasOne(Forms::class, ['id' => 'form_id']);
    }

    /**
     * @return array
     */
    public function getFieldsTypes()
    {
        return $this->fieldsTypes;
    }

    /**
     * @return array
     */
    public function getFieldType($type = null)
    {
        if (is_null($type))
            return null;

        $types = $this->getFieldsTypes();
        if (isset($types[$type]))
            return $types[$type];
        else
            return null;
    }

    /**
     * @return array
     */
    public function getFieldsTypesList($allTypes = false)
    {
        $list = [];
        if ($allTypes)
            $list['*'] = Yii::t('app/modules/forms', 'All types');

        $list = ArrayHelper::merge($list, $this->getFieldsTypes());

        return $list;
    }

    /**
     *
     */
    public function getValidator()
    {
        switch ($this->type) {
            case 1: // 'text'
                return 'string';
            case 2: // 'textarea'
                return 'string';
            case 3: // 'checkbox'
                return 'string';
            case 4: // 'file'
                return 'string';
            case 5: // 'hidden'
                return 'string';
            case 6: // 'password'
                return 'string';
            case 7: // 'radio'
                return 'string';
            case 8: // 'color'
                return 'string';
            case 9: // 'date'
                return 'string';
            case 10: // 'datetime'
                return 'string';
            case 11: // 'datetime-local'
                return 'string';
            case 12: // 'email'
                return 'string';
            case 13: // 'number'
                return 'string';
            case 14: // 'range'
                return 'string';
            case 15: // 'search'
                return 'string';
            case 16: // 'tel'
                return 'string';
            case 17: // 'time'
                return 'string';
            case 18: // 'url'
                return 'string';
            case 19: // 'month'
                return 'string';
            case 20: // 'week'
                return 'string';
            default:
                return 'string';
        }
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
     * @return array
     */
    public function getStatusesList($allStatuses = false)
    {
        if ($allStatuses)
            return [
                '*' => Yii::t('app/modules/forms', 'All statuses'),
                self::STATUS_DRAFT => Yii::t('app/modules/forms', 'Draft'),
                self::STATUS_PUBLISHED => Yii::t('app/modules/forms', 'Published'),
            ];
        else
            return [
                self::STATUS_DRAFT => Yii::t('app/modules/forms', 'Draft'),
                self::STATUS_PUBLISHED => Yii::t('app/modules/forms', 'Published'),
            ];
    }
}
