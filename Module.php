<?php

namespace wdmg\forms;

/**
 * Yii2 Forms
 *
 * @category        Module
 * @version         1.0.11
 * @author          Alexsander Vyshnyvetskyy <alex.vyshnyvetskyy@gmail.com>
 * @link            https://github.com/wdmg/yii2-forms
 * @copyright       Copyright (c) 2019 - 2020 W.D.M.Group, Ukraine
 * @license         https://opensource.org/licenses/MIT Massachusetts Institute of Technology (MIT) License
 *
 */

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
    public $description = "Creation and management of user forms";

    /**
     * @var string the module version
     */
    private $version = "1.0.11";

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
    public function dashboardNavItems($createLink = false)
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
        return $items;
    }

    /**
     * {@inheritdoc}
     */
    public function bootstrap($app) {
        parent::bootstrap($app);
    }
}