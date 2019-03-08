<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\vendor\wdmg\forms\models\Forms */

$this->title = Yii::t('app/modules/forms', 'Create Forms');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app/modules/forms', 'Forms'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="forms-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
