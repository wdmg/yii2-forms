<?php

namespace wdmg\forms;

use yii\base\BootstrapInterface;
use Yii;


class Bootstrap implements BootstrapInterface
{
    public function bootstrap($app)
    {
        // Get the module instance
        $module = Yii::$app->getModule('forms');

        // Get URL path prefix if exist
        $prefix = (isset($module->routePrefix) ? $module->routePrefix . '/' : '');

        // Add module URL rules
        $app->getUrlManager()->addRules(
            [
                $prefix . '<module:forms>/' => '<module>/list/all',
                $prefix . '<module:forms>/<controller:(list)>/' => '<module>/<controller>',
                $prefix . '<module:forms>/<controller:(item)>/<action:(view|update|delete|set)>' => '<module>/<controller>/<action>',
            ],
            true
        );
    }
}
