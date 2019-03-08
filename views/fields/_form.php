<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\vendor\wdmg\forms\models\Fields */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="fields-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'form_id')->textInput() ?>

    <?= $form->field($model, 'label')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'description')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'type')->textInput() ?>

    <?= $form->field($model, 'sort_order')->textInput() ?>

    <?= $form->field($model, 'params')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'is_required')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app/modules/forms', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
