<?php

namespace wdmg\forms;

/**
 * Yii2 Forms
 *
 * @category        Module
 * @version         1.0.5
 * @author          Alexsander Vyshnyvetskyy <alex.vyshnyvetskyy@gmail.com>
 * @link            https://github.com/wdmg/yii2-forms
 * @copyright       Copyright (c) 2019 W.D.M.Group, Ukraine
 * @license         https://opensource.org/licenses/MIT Massachusetts Institute of Technology (MIT) License
 *
 */

use Yii;
use wdmg\base\BaseModule;

/**
 * Tickets module definition class
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
    public $defaultRoute = "forms/index";

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
    private $version = "1.0.5";

    /**
     * @var integer, priority of initialization
     */
    private $priority = 10;

    public function bootstrap($app) {
        parent::bootstrap($app);
    }
}