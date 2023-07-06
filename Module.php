<?php

namespace wdmg\forms;

/**
 * Yii2 Forms
 *
 * @category        Module
 * @version         1.2.0
 * @author          Alexsander Vyshnyvetskyy <alex.vyshnyvetskyy@gmail.com>
 * @link            https://github.com/wdmg/yii2-forms
 * @copyright       Copyright (c) 2019 - 2023 W.D.M.Group, Ukraine
 * @license         https://opensource.org/licenses/MIT Massachusetts Institute of Technology (MIT) License
 *
 */

use wdmg\helpers\ArrayHelper;
use Yii;
use wdmg\base\BaseModule;

/**
 * Forms module definition class
 */
class Module extends BaseModule
{
    /**
     * {@inheritdoc}
     */
    public $controllerNamespace = 'wdmg\forms\controllers';

    /**
     * {@inheritdoc}
     */
    public $defaultRoute = "list/index";

    /**
     * @var string, the name of module
     */
    public $name = "Forms";

    /**
     * @var string, the description of module
     */
    public $description = "Creating and manage composite forms";

    /**
     * @var array, the list of support locales for multi-language versions of form fields.
     * @note This variable will be override if you use the `wdmg\yii2-translations` module.
     */
    public $supportLocales = ['ru-RU', 'uk-UA', 'en-US'];

    /**
     * @var string the module version
     */
    private $version = "1.2.0";

    /**
     * @var integer, priority of initialization
     */
    private $priority = 10;

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        // Set version of current module
        $this->setVersion($this->version);

        // Set priority of current module
        $this->setPriority($this->priority);

    }

    /**
     * {@inheritdoc}
     */
    public function dashboardNavItems($options = false)
    {
        $items = [
            'label' => $this->name,
            'url' => [$this->routePrefix . '/'. $this->id],
            'icon' => 'fa fa-fw fa-paper-plane',
            'active' => in_array(\Yii::$app->controller->module->id, [$this->id]),
            'items' => [
                [
                    'label' => Yii::t('app/modules/forms', 'Forms list'),
                    'url' => [$this->routePrefix . '/forms/list/'],
                    'active' => (in_array(\Yii::$app->controller->module->id, ['forms']) &&  Yii::$app->controller->id == 'list'),
                ],
                [
                    'label' => Yii::t('app/modules/forms', 'Fields list'),
                    'url' => [$this->routePrefix . '/forms/fields/'],
                    'active' => (in_array(\Yii::$app->controller->module->id, ['forms']) &&  Yii::$app->controller->id == 'fields'),
                ],
                [
                    'label' => Yii::t('app/modules/forms', 'Filling results'),
                    'url' => [$this->routePrefix . '/forms/submitted/'],
                    'active' => (in_array(\Yii::$app->controller->module->id, ['forms']) &&  Yii::$app->controller->id == 'submitted'),
                ]
            ]
        ];

	    if (!is_null($options)) {

		    if (isset($options['count'])) {
			    $items['label'] .= '<span class="badge badge-default float-right">' . $options['count'] . '</span>';
			    unset($options['count']);
		    }

		    if (is_array($options))
			    $items = ArrayHelper::merge($items, $options);

	    }

	    return $items;
    }

    /**
     * {@inheritdoc}
     */
    public function bootstrap($app) {
        parent::bootstrap($app);

        // Configure forms component
        $app->setComponents([
            'forms' => [
                'class' => 'wdmg\forms\components\Forms'
            ]
        ]);
    }
}