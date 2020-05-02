<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use wdmg\widgets\LangSwitcher;
use wdmg\widgets\SelectInput;

/* @var $this yii\web\View */
/* @var $model app\vendor\wdmg\forms\models\Fields */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="fields-form">
    <?php
        echo LangSwitcher::widget([
            'label' => Yii::t('app/modules/forms', 'Language version'),
            'model' => $model,
            'renderWidget' => 'button-group',
            'createRoute' => 'fields/create',
            'updateRoute' => 'fields/update',
            'supportLocales' => $this->context->module->supportLocales,
            //'currentLocale' => $this->context->getLocale(),
            'versions' => (isset($model->source_id)) ? $model->getAllVersions($model->source_id, true) : $model->getAllVersions($model->id, true),
            'options' => [
                'id' => 'locale-switcher',
                'class' => 'pull-right'
            ]
        ]);
    ?>

    <?php

        $form = ActiveForm::begin([
            'id' => "addNewField",
            'enableAjaxValidation' => true,
            'options' => [
                'enctype' => 'multipart/form-data'
            ]
        ]);

        $readonly = false;
        if (is_null($model->id) && (!is_null($model->source_id) && !is_null($model->name)))
            $readonly = true;

    ?>

    <?= $form->field($model, 'form_id')->widget(SelectInput::class, [
        'items' => $model->getAllFormsList(false),
        'options' => [
            'class' => 'form-control',
            'readonly' => $readonly
        ]
    ])->label(Yii::t('app/modules/forms', 'Form')) ?>

    <?= $form->field($model, 'label')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'name')->textInput([
        'maxlength' => true,
        'readonly' => $readonly
    ]) ?>
    <?= $form->field($model, 'placeholder')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'description')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'type')->widget(SelectInput::class, [
        'items' => $model->getFieldsTypesList(false),
        'options' => [
            'class' => 'form-control',
            'readonly' => $readonly
        ]
    ]) ?>

    <?= $form->field($model, 'is_required')->checkbox([
        'label' => Yii::t('app/modules/forms', '- is required'),
        'readonly' => $readonly
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
        <?= Html::submitButton(Yii::t('app/modules/forms', 'Save'), ['class' => 'btn btn-save btn-success pull-right']) ?>
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
                    if (data.name && form.find('#fields-name').val().length == 0) {
                        form.find('#fields-name').val(data.name);
                        form.find('#fields-name').change();
                        form.yiiActiveForm('validateAttribute', 'fields-name');
                    }
                }).fail(function () {
                    /*form.find('#options-type').val("");
                    form.find('#options-type').trigger('change');*/
                });
                return false; // prevent default form submission
            }
        }
        $("#addNewField").on("afterValidateAttribute", afterValidateAttribute);
    });
JS
); ?>