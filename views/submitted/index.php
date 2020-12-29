<?php

use wdmg\helpers\StringHelper;
use wdmg\widgets\SelectInput;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\vendor\wdmg\forms\models\SubmitsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app/modules/forms', 'All results');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app/modules/forms', 'Forms'), 'url' => ['list/index']];
$this->params['breadcrumbs'][] = Yii::t('app/modules/forms', 'Submitted forms');

?>
<div class="page-header">
    <h1>
        <?= Html::encode($this->title) ?> <small class="text-muted pull-right">[v.<?= $this->context->module->version ?>]</small>
    </h1>
</div>
<div class="forms-submits-index">
    <?php Pjax::begin(); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'form_id',
                'format' => 'html',
                'label' => Yii::t('app/modules/forms', 'Form'),
                'filter' => SelectInput::widget([
                    'model' => $searchModel,
                    'attribute' => 'form_id',
                    'items' => $searchModel->getAllFormsList(true),
                    'options' => [
                        'class' => 'form-control'
                    ]
                ]),
                'headerOptions' => [
                    'class' => 'text-center'
                ],
                'contentOptions' => [
                    'class' => 'text-center'
                ],
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
                'attribute' => 'contents',
                'format' => 'html',
                'label' => Yii::t('app/modules/forms', 'Contents'),
                'filter' => true,
                'contentOptions' => [
                    'style' => 'width:50%;'
                ],
                'value' => function($data) {
                    $count = 0;
                    $output = '';
                    if (is_countable($data->contents)) {
                        foreach ($data->contents as $content) {

                            if ($count >= 6) {
                                $lost = abs(count($data->contents) - $count);
                                if ($lost > 0) {
                                    $output .= '<em class="text-danger">';
                                    $output .= Yii::t('app/modules/forms', ' … and {count, number} more {count, plural, one{field} few{fields} other{fields}}', [
                                        'count' => $lost
                                    ]);
                                    $output .= '</em>';
                                }
                                break;
                            }

                            if (!empty($output))
                                $output .= ', ';

                            $output .= '<span><b>'.$content['label'].': </b>'.' '.StringHelper::truncateWords($content['value'],12,'…').'</span>';
                            $count++;
                        }
                    }
                    return $output;
                }
            ],

            //'access_token',
            //'status',

            [
                'attribute' => 'user_id',
                'label' => Yii::t('app/modules/forms','Submitted'),
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

                    if (!empty($output))
                        $output .= ", ";

                    $output .= Yii::$app->formatter->format($data->created_at, 'datetime');
                    return $output;
                }
            ],
            [
                'attribute' => 'status',
                'format' => 'html',
                'filter' => SelectInput::widget([
                    'model' => $searchModel,
                    'attribute' => 'status',
                    'items' => $searchModel->getStatusesList(true),
                    'options' => [
                        'class' => 'form-control'
                    ]
                ]),
                'headerOptions' => [
                    'class' => 'text-center'
                ],
                'contentOptions' => [
                    'class' => 'text-center'
                ],
                'value' => function($data) {
                    if ($data->status == $data::STATUS_SUBMITTED)
                        return '<span class="label label-success">'.Yii::t('app/modules/forms','Submitted').'</span>';
                    elseif ($data->status == $data::STATUS_NOT_SUBMITTED)
                        return '<span class="label label-danger">'.Yii::t('app/modules/forms','Not submitted').'</span>';
                    else
                        return $data->status;
                }
            ],
            ['class' => 'yii\grid\ActionColumn'],
        ],
        'pager' => [
            'options' => [
                'class' => 'pagination',
            ],
            'maxButtonCount' => 5,
            'activePageCssClass' => 'active',
            'prevPageCssClass' => 'prev',
            'nextPageCssClass' => 'next',
            'firstPageCssClass' => 'first',
            'lastPageCssClass' => 'last',
            'firstPageLabel' => Yii::t('app/modules/forms', 'First page'),
            'lastPageLabel'  => Yii::t('app/modules/forms', 'Last page'),
            'prevPageLabel'  => Yii::t('app/modules/forms', '&larr; Prev page'),
            'nextPageLabel'  => Yii::t('app/modules/forms', 'Next page &rarr;')
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>

<?php echo $this->render('../_debug'); ?>
