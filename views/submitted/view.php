<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\vendor\wdmg\forms\models\Submits */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => $this->context->module->name, 'url' => ['forms/list']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app/modules/forms', 'Submitted forms'), 'url' => ['submitted/index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="page-header">
    <h1>
        <?= Html::encode($this->title) ?> <small class="text-muted pull-right">[v.<?= $this->context->module->version ?>]</small>
    </h1>
</div>
<div class="forms-submits-view">

    <h3><?= Yii::t('app/modules/forms', 'Form fill data') ?></h3>
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => $model->contents
    ]); ?>

    <h3><?= Yii::t('app/modules/forms', 'Form submission details') ?></h3>
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            [
                'attribute' => 'form',
                'format' => 'html',
                'value' => function($data) {
                    $output = "";
                    $output = Html::a($data->form->name, ['list/view', 'id' => $data->form->id], [
                        'target' => '_blank',
                        'data-pjax' => 0
                    ]);
                    return $output;
                }
            ],
            [
                'attribute' => 'user',
                'format' => 'html',
                'value' => function($data) {
                    $output = "";
                    if ($user = $data->user) {
                        $output = Html::a($user->username, ['../admin/users/view/?id='.$user->id], [
                            'target' => '_blank',
                            'data-pjax' => 0
                        ]);
                    } else if ($data->user_id) {
                        $output = $data->user_id;
                    } else {
                        $output = Yii::t('app/modules/forms','Guest');
                    }
                    return $output;
                }
            ],
            'access_token',
            'created_at:datetime',
            'updated_at:datetime',
            [
                'attribute' => 'status',
                'format' => 'html',
                'value' => function($data) {
                    if ($data->status == $data::STATUS_SUBMITTED)
                        return '<span class="label label-success">'.Yii::t('app/modules/forms','Submitted').'</span>';
                    elseif ($data->status == $data::STATUS_NOT_SUBMITTED)
                        return '<span class="label label-danger">'.Yii::t('app/modules/forms','Not submitted').'</span>';
                    else
                        return $data->status;
                }
            ],
        ],
    ]); ?>
    <hr/>
    <div>
        <?= Html::a(Yii::t('app/modules/forms', '&larr; Back to list'), ['submitted/index'], ['class' => 'btn btn-default pull-left']) ?>&nbsp;
        <?php if (Yii::$app->authManager && $this->context->module->moduleExist('rbac') && Yii::$app->user->can('updatePosts')) : ?>
            <?= Html::a(Yii::t('app/modules/forms', 'Delete'), ['delete', 'id' => $model->id], [
                'class' => 'btn btn-delete btn-danger pull-right',
                'data' => [
                    'confirm' => Yii::t('app/modules/forms', 'Are you sure you want to delete this item?'),
                    'method' => 'post',
                ],
            ]); ?>
        <?php endif; ?>
    </div>
</div>

<?php echo $this->render('../_debug'); ?>
