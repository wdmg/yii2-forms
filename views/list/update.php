<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\vendor\wdmg\forms\models\Forms */

$this->title = Yii::t('app/modules/forms', 'Update Forms: {name}', [
    'name' => $model->name,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app/modules/forms', 'Forms'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app/modules/forms', 'Update');
?>
<div class="forms-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
