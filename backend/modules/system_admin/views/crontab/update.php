<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Crontab */

$this->title = Yii::t('app', '{Update} {Crontab}: {name}', [
    'Update' => Yii::t('app', 'Update'),
    'Crontab' => Yii::t('app', 'Crontab'),
    'name' => $model->name,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Crontabs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="crontab-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
