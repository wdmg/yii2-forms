<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use wdmg\widgets\Editor;
use wdmg\widgets\LangSwitcher;
use wdmg\widgets\SelectInput;

/* @var $this yii\web\View */
/* @var $model app\vendor\wdmg\forms\models\Forms */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="forms-form">
    <?php
        echo LangSwitcher::widget([
            'label' => Yii::t('app/modules/forms', 'Language version'),
            'model' => $model,
            'renderWidget' => 'button-group',
            'createRoute' => 'list/create',
            'updateRoute' => 'list/update',
            'supportLocales' => $this->context->module->supportLocales,
            //'currentLocale' => $this->context->getLocale(),
            'versions' => (isset($model->source_id)) ? $model->getAllVersions($model->source_id, true) : $model->getAllVersions($model->id, true),
            'options' => [
                'id' => 'locale-switcher',
                'class' => 'pull-right'
            ]
        ]);
    ?>

    <?php $form = ActiveForm::begin([
        'id' => "addNewForm",
        'enableAjaxValidation' => true,
        'options' => [
            'enctype' => 'multipart/form-data'
        ]
    ]); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'lang' => ($model->locale ?? Yii::$app->language)]) ?>

    <?= $form->field($model, 'alias')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true, 'lang' => ($model->locale ?? Yii::$app->language)]) ?>

    <?= $form->field($model, 'description')->widget(Editor::class, [
        'options' => [
            'lang' => ($model->locale ?? Yii::$app->language)
        ],
        'pluginOptions' => []
    ]) ?>

    <?= $form->field($model, 'status')->widget(SelectInput::class, [
        'items' => $model->getStatusesList(false),
        'options' => [
            'class' => 'form-control'
        ]
    ]); ?>

    <hr/>
    <div class="form-group">
        <?= Html::a(Yii::t('app/modules/forms', '&larr; Back to list'), ['list/index'], ['class' => 'btn btn-default pull-left']) ?>&nbsp;
        <?php if ((Yii::$app->authManager && $this->context->module->moduleExist('rbac') && Yii::$app->user->can('updatePosts', [
                    'created_by' => $model->created_by,
                    'updated_by' => $model->updated_by
                ])) || !$model->id) : ?>
            <?= Html::submitButton(Yii::t('app/modules/forms', 'Save'), ['class' => 'btn btn-save btn-success pull-right']) ?>
        <?php endif; ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>

<?php $this->registerJs(<<< JS
    $(document).ready(function() {
        function afterValidateAttribute(event, attribute, messages)
        {
            if (attribute.name && !attribute.alias && messages.length == 0) {
                var form = $(event.target);
                $.ajax({
                    type: form.attr('method'),
                    url: form.attr('action'),
                    data: form.serializeArray(),
                }).done(function(data) {
                    if (data.alias && form.find('#forms-alias').val().length == 0) {
                        form.find('#forms-alias').val(data.alias);
                        form.find('#forms-alias').change();
                        form.yiiActiveForm('validateAttribute', 'forms-alias');
                    }
                }).fail(function () {
                    /*form.find('#options-type').val("");
                    form.find('#options-type').trigger('change');*/
                });
                return false; // prevent default form submission
            }
        }
        $("#addNewForm").on("afterValidateAttribute", afterValidateAttribute);
    });
JS
); ?>