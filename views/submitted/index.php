<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\vendor\wdmg\forms\models\SubmitsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app/modules/forms', 'All results');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app/modules/forms', 'Forms'), 'url' => ['list/index']];
$this->params['breadcrumbs'][] = Yii::t('app/modules/forms', 'Filling results');

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

            'form_id',
            'user_id',
            'access_token',
            'created_at',
            //'updated_at',
            //'status',

            ['class' => 'yii\grid\ActionColumn'],
        ],
        'pager' => [
            'options' => [
                'class' => 'pagination',
            ],
            'maxButtonCount' => 5,
            'activePageCssClass' => 'active',
            'prevPageCssClass' => '',
            'nextPageCssClass' => '',
            'firstPageCssClass' => 'previous',
            'lastPageCssClass' => 'next',
            'firstPageLabel' => Yii::t('app/modules/forms', 'First page'),
            'lastPageLabel'  => Yii::t('app/modules/forms', 'Last page'),
            'prevPageLabel'  => Yii::t('app/modules/forms', '&larr; Prev page'),
            'nextPageLabel'  => Yii::t('app/modules/forms', 'Next page &rarr;')
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>

<?php echo $this->render('../_debug'); ?>
