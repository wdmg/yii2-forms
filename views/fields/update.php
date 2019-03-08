<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\vendor\wdmg\forms\models\Fields */

$this->title = Yii::t('app/modules/forms', 'Update Fields: {name}', [
    'name' => $model->id,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app/modules/forms', 'Fields'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app/modules/forms', 'Update');
?>
<div class="fields-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
