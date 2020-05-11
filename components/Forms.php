<?php

namespace wdmg\forms\components;


/**
 * Yii2 Forms
 *
 * @category        Component
 * @version         1.1.0
 * @author          Alexsander Vyshnyvetskyy <alex.vyshnyvetskyy@gmail.com>
 * @link            https://github.com/wdmg/yii2-forms
 * @copyright       Copyright (c) 2019 - 2020 W.D.M.Group, Ukraine
 * @license         https://opensource.org/licenses/MIT Massachusetts Institute of Technology (MIT) License
 *
 */

use Yii;
use yii\base\Component;
use wdmg\base\models\DynamicModel;
use yii\base\InvalidArgumentException;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

class Forms extends Component
{

    protected $model;

    /**
     * Initialize the component
     *
     * @throws \yii\base\InvalidConfigException
     */
    public function init()
    {
        parent::init();
        $this->model = new \wdmg\forms\models\Forms;
    }

    /**
     * Builds a html-collection of input widgets for the form.
     *
     * @param null|object $widget, object of ActiveForm widget
     * @param null|int|string $id, the form identifier or alias
     * @return string|null
     */
    public function build($widget = null, $id = null)
    {
        if (is_null($id))
            return null;

        if (is_string($id))
            $form = $this->model->getPublished(['alias' => $id], true);
        else
            $form = $this->model->getPublished(['id' => $id], true);

        if ($form) {

            $locale = Yii::$app->sourceLanguage;
            if (Yii::$app->language)
                $locale = Yii::$app->language;

            $output = '';
            if ($fields = $form->getFormsFields(['locale' => $locale], true, false)->all()) {

                $widget->options = [
                    'id' => 'form-' . $form->id,
                    'name' => $form->alias
                ];

                $model = new DynamicModel();
                foreach ($fields as $field) {

                    $formName = $this->getFormName($form->alias);
                    $model->setFormName($formName);
                    $model->defineAttribute($field->attribute);

                    if ($field->is_required)
                        $model->addRule($field->attribute, 'required');

                    $validator = $field->getValidator();
                    $model->addRule($field->attribute, $validator);

                    $model->setAttributeLabel($field->attribute, $field->label);

                    $options = [
                        'placeholder' => ($field->placeholder) ? $field->placeholder : null,
                        'required' => ($field->is_required) ? true : false,
                    ];

                    // @TODO: Add form fields validation etc.
                    // @TODO: Add custom options for fields

                    $type = $field->getFieldType($field->type);
                    $input = $widget->field($model, $field->attribute);

                    if ($field->type == 2 || $type == 'textarea')
                        $input->textarea($options);
                    elseif (!is_null($type))
                        $input->input($type, $options);
                    else
                        $input->textInput($options);

                    $output .= $input;
                }
            }
            return $output;
        }
        return null;
    }

    /**
     * Processes the form publication data submitted from the controller, validates it and writes it to the database.
     *
     * @param string|null $id, the form identifier or alias
     * @param array|null $data, post data, like ['MyCustomForm' => ['name' => 'John', 'email' => 'john@example.com']]
     * @return array|bool|null, true - success publish, false - if failure, array - contains list of validation errors
     * @throws \Throwable
     * @throws \yii\base\Exception
     * @throws \yii\db\StaleObjectException
     */
    public function submit($id = null, $data = null) {

        if (is_null($id) ||is_null($data))
            return null;

        if (is_string($id))
            $form = $this->model->getPublished(['alias' => $id], true);
        else
            $form = $this->model->getPublished(['id' => $id], true);

        if ($form) {

            // Check of current form exist and collect data
            $collect = null;
            $formName = $this->getFormName($form->alias);
            if (isset($data[$formName]))
                $collect[$formName] = $data[$formName];
            else
                return null;

            $locale = Yii::$app->sourceLanguage;
            if (Yii::$app->language)
                $locale = Yii::$app->language;

            $errors = [];
            if (is_countable($collect) && $fields = $form->getFormsFields(['locale' => $locale], true, false)->all()) {
            //if (is_countable($collect) && $fields = $form->getFormsFields(['source_id' => null], true, false)->all()) {

                $fields_ids = [];
                $model = new DynamicModel();
                foreach ($fields as $field) {

                    $formName = $this->getFormName($form->alias);
                    $model->setFormName($formName);
                    $model->defineAttribute($field->attribute);
                    $model->addRule($field->attribute, $field->getValidator());
                    $model->setAttributeLabel($field->attribute, $field->label);

                    $field_name = str_replace('-', '_', $field->name);
                    $fields_ids[$field_name] = $field->id;
                }

                if ($model->load($collect) && $model->validate()) {

                    if (!empty($model->errors))
                        $errors[$formName] = $model->errors;

                    $attributes = $model->getAttributes();


                    if (is_countable($attributes)) {

                        $hasError = false;

                        $submit = new \wdmg\forms\models\Submits();
                        $submit->form_id = $form->id;
                        $submit->user_id = (Yii::$app->getUser()) ? Yii::$app->getUser()->id : null;
                        $submit->access_token = Yii::$app->security->generateRandomString();
                        $submit->status = 0;

                        if ($submit->validate()) {

                            if (!empty($submit->errors))
                                $errors['Submits'] = $submit->errors;

                            if ($submit->save()) {

                                foreach ($attributes as $attribute => $value) {

                                    $content = new \wdmg\forms\models\Content();
                                    $content->input_id = ArrayHelper::getValue($fields_ids, $attribute);
                                    $content->submit_id = $submit->id;
                                    $content->value = $value;

                                    if ($content->validate()) {
                                        if (!$content->save()) {
                                            $hasError = true;
                                        }
                                    } else {
                                        if (!empty($content->errors)) {
                                            $errors['Content'] = ArrayHelper::merge($content->errors, $errors);
                                        }
                                        $hasError = true;
                                    }
                                }

                                if (!$hasError) {
                                    $submit->status = 1;

                                    if ($submit->update())
                                        return true;

                                }

                                return (!empty($errors)) ? $errors : false;
                            }
                        }
                    }
                }
            }
        }

        return false;
    }

    /**
     * Returns the name of the form based on an alias
     *
     * @param null $formAlias
     * @return mixed|null
     */
    private static function getFormName($formAlias = null) {

        if (is_null($formAlias))
            return null;

        return str_replace(' ', '', \yii\helpers\Inflector::camel2words($formAlias));
    }

    /**
     * Returns the alias of the form based on an form name
     *
     * @param null $formName
     * @return mixed|null
     */
    private static function getFormAlias($formName = null) {

        if (is_null($formName))
            return null;

        return str_replace(' ', '', \yii\helpers\Inflector::camel2id($formName));
    }
}

?>