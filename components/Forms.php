<?php

namespace wdmg\forms\components;


/**
 * Yii2 Forms
 *
 * @category        Component
 * @version         1.0.13
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

                // @TODO: Add default controller for form submits and storage data
                // @TODO: Add custom options for forms

                /*$widget->action = '';
                $widget->method = '';
                $widget->options = [
                    'id' => $form->alias
                ];*/

                $model = new DynamicModel();
                foreach ($fields as $field) {
                    $model->defineAttribute($field->attribute);
                    $model->addRule($field->attribute, $field->getValidator());
                    $model->setAttributeLabel([$field->attribute => $field->label]);

                    $options = [
                        'placeholder' => ($field->placeholder) ? $field->placeholder : null,
                        'required' => ($field->is_required) ? true : false,
                    ];

                    $input = $widget->field($model, $field->attribute);

                    // @TODO: Add form fields validation etc.
                    // @TODO: Add custom options for fields

                    if ($field->type == 2)
                        $input->textarea($options);
                    else
                        $input->textInput($options);

                    $output .= $input;
                }
            }
            return $output;
        }
        return null;
    }

}

?>