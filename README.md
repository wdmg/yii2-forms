[![Yii2](https://img.shields.io/badge/required-Yii2_v2.0.33-blue.svg)](https://packagist.org/packages/yiisoft/yii2)
[![Downloads](https://img.shields.io/packagist/dt/wdmg/yii2-forms.svg)](https://packagist.org/packages/wdmg/yii2-forms)
[![Packagist Version](https://img.shields.io/packagist/v/wdmg/yii2-forms.svg)](https://packagist.org/packages/wdmg/yii2-forms)
![Progress](https://img.shields.io/badge/progress-in_development-red.svg)
[![GitHub license](https://img.shields.io/github/license/wdmg/yii2-forms.svg)](https://github.com/wdmg/yii2-forms/blob/master/LICENSE)

# Yii2 Forms
Creation and management of custom user forms

# Requirements 
* PHP 5.6 or higher
* Yii2 v.2.0.33 and newest
* [Yii2 Base](https://github.com/wdmg/yii2-base) module (required)

# Installation
To install the module, run the following command in the console:

`$ composer require "wdmg/yii2-forms"`

After configure db connection, run the following command in the console:

`$ php yii forms/init`

And select the operation you want to perform:
  1) Apply all module migrations
  2) Revert all module migrations
  3) Add demo data

# Migrations
In any case, you can execute the migration and create the initial data, run the following command in the console:

`$ php yii migrate --migrationPath=@vendor/wdmg/yii2-forms/migrations`

# Configure

To add a module to the project, add the following data in your configuration file:

    'modules' => [
        ...
        'forms' => [
            'class' => 'wdmg\forms\Module',
            'routePrefix' => 'admin'
        ],
        ...
    ],

# Usage examples
To build the ActiveForm with fields you may use the component method Yii::$app->forms->build() with `id` or `alias` of form item.

**View in frontend**

    <?php
        use yii\widgets\ActiveForm;
        ...
    ?>
        
    <h3>Feedback</h3>
    <?php $form = ActiveForm::begin(); ?>
    <?= Yii::$app->forms->build($form, 'feedback-form'); ?>
    <?= Html::submitButton('Submit') ?>
    <?php ActiveForm::end(); ?>
    ...
    
    <h3>Callback</h3>
    <?php $form = ActiveForm::begin(); ?>
    <?= Yii::$app->forms->build($form, 'сallback-form'); ?>
    <?= Html::submitButton('Submit') ?>
    <?php ActiveForm::end(); ?>

**Action in Controller**

    <?php
        if (!is_null($result = Yii::$app->forms->submit('feedback-form', $data))) {
            if ($result === true) {
                Yii::$app->session->setFlash('success', 'Your Feedback form successfully submitted!');
            } else {
                Yii::$app->session->setFlash('danger', 'An error occurred while sending the Feedback form.');
            }
        }
    ?>

# Routing
Use the `Module::dashboardNavItems()` method of the module to generate a navigation items list for NavBar, like this:

    <?php
        echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
            'label' => 'Modules',
            'items' => [
                Yii::$app->getModule('forms')->dashboardNavItems(),
                ...
            ]
        ]);
    ?>


# Status and version [in progress development]
* v.1.0.13 - Update dependencies
* v.1.0.12 - Log activity and translations, added forms component
* v.1.0.11 - Added pagination, up to date dependencies
* v.1.0.10 - Rebuild migrations, views
* v.1.0.9 - Fixed deprecated class declaration
* v.1.0.8 - Added extra options to composer.json and navbar menu icon
* v.1.0.7 - Added choice param for non interactive mode