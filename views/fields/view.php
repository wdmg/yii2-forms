<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\vendor\wdmg\forms\models\Fields */

$this->title = $model->id;
$this->params['breadcrumbs'][] = $this->context->module->name, 'url' => ['forms/list']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app/modules/forms', 'Fields'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="page-header">
    <h1>
        <?= Html::encode($this->title) ?> <small class="text-muted pull-right">[v.<?= $this->context->module->version ?>]</small>
    </h1>
</div>
<div class="fields-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'form_id',
            'label',
            'description',
            'type',
            'sort_order',
            'params:ntext',
            'is_required',
        ],
    ]); ?>
    <hr/>
    <div>
        <?= Html::a(Yii::t('app/modules/forms', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']); ?>
        <?= Html::a(Yii::t('app/modules/forms', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app/modules/forms', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]); ?>
    </div>
</div>

<?php echo $this->render('../_debug'); ?>
