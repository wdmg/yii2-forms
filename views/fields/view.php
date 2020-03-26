<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\vendor\wdmg\forms\models\Fields */

$this->title = $model->label;
$this->params['breadcrumbs'][] = ['label' => $this->context->module->name, 'url' => ['list/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app/modules/forms', 'All fields'), 'url' => ['fields/index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="page-header">
    <h1>
        <?= Html::encode($this->title) ?> <small class="text-muted pull-right">[v.<?= $this->context->module->version ?>]</small>
    </h1>
</div>
<div class="forms-fields-view">
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
            'label',
            'description',
            [
                'attribute' => 'type',
                'value' => function($data) {
                    $types = $data->getFieldsTypesList(true);
                    if (isset($types[$data->type]))
                        return $types[$data->type];
                    else
                        return $data->type;
                }
            ],
            'sort_order',
            'params:ntext',

            [
                'attribute' => 'is_required',
                'format' => 'html',
                'value' => function($data) {
                    if ($data->is_required)
                        return '<span class="fa fa-check text-success"></span>';
                    else
                        return '';
                }
            ],
            [
                'attribute' => 'status',
                'format' => 'html',
                'value' => function($data) {
                    if ($data->status == $data::FIELD_STATUS_PUBLISHED)
                        return '<span class="label label-success">'.Yii::t('app/modules/forms','Published').'</span>';
                    elseif ($data->status == $data::FIELD_STATUS_DRAFT)
                        return '<span class="label label-default">'.Yii::t('app/modules/forms','Draft').'</span>';
                    else
                        return $data->status;
                }
            ],
            [
                'attribute' => 'created',
                'label' => Yii::t('app/modules/forms','Created'),
                'format' => 'html',
                'value' => function($data) {

                    $output = "";
                    if ($user = $data->createdBy) {
                        $output = Html::a($user->username, ['../admin/users/view/?id='.$user->id], [
                            'target' => '_blank',
                            'data-pjax' => 0
                        ]);
                    } else if ($data->created_by) {
                        $output = $data->created_by;
                    }

                    if (!empty($output))
                        $output .= ", ";

                    $output .= Yii::$app->formatter->format($data->updated_at, 'datetime');
                    return $output;
                }
            ],
            [
                'attribute' => 'updated',
                'label' => Yii::t('app/modules/forms','Updated'),
                'format' => 'html',
                'value' => function($data) {

                    $output = "";
                    if ($user = $data->updatedBy) {
                        $output = Html::a($user->username, ['../admin/users/view/?id='.$user->id], [
                            'target' => '_blank',
                            'data-pjax' => 0
                        ]);
                    } else if ($data->updated_by) {
                        $output = $data->updated_by;
                    }

                    if (!empty($output))
                        $output .= ", ";

                    $output .= Yii::$app->formatter->format($data->updated_at, 'datetime');
                    return $output;
                }
            ],
        ],
    ]); ?>
    <hr/>
    <div>
        <?= Html::a(Yii::t('app/modules/forms', '&larr; Back to list'), ['fields/index'], ['class' => 'btn btn-default pull-left']) ?>&nbsp;
        <div class="form-group pull-right">
            <?= Html::a(Yii::t('app/modules/forms', 'Delete'), ['fields/delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => Yii::t('app/modules/forms', 'Are you sure you want to delete this item?'),
                    'method' => 'post',
                ],
            ]); ?>
            <?= Html::a(Yii::t('app/modules/forms', 'Update'), ['fields/update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        </div>
    </div>
</div>

<?php echo $this->render('../_debug'); ?>
