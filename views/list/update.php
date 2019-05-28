<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\vendor\wdmg\forms\models\Forms */

$this->title = Yii::t('app/modules/forms', 'Update Forms: {name}', [
    'name' => $model->name,
]);
$this->params['breadcrumbs'][] = ['label' => $this->context->module->name, 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app/modules/forms', 'Update');
?>
<div class="page-header">
    <h1>
        <?= Html::encode($this->title) ?> <small class="text-muted pull-right">[v.<?= $this->context->module->version ?>]</small>
    </h1>
</div>
<div class="forms-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>

<?php echo $this->render('../_debug'); ?>
