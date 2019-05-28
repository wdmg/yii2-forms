<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\vendor\wdmg\forms\models\Submits */

$this->title = Yii::t('app/modules/forms', 'Create Submits');
$this->params['breadcrumbs'][] = $this->context->module->name, 'url' => ['forms/list']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app/modules/forms', 'Submits'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="page-header">
    <h1>
        <?= Html::encode($this->title) ?> <small class="text-muted pull-right">[v.<?= $this->context->module->version ?>]</small>
    </h1>
</div>
<div class="submits-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>

<?php echo $this->render('../_debug'); ?>
