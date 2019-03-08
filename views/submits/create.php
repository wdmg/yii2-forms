<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\vendor\wdmg\forms\models\Submits */

$this->title = Yii::t('app/modules/forms', 'Create Submits');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app/modules/forms', 'Submits'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="submits-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
