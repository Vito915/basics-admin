<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Crontab */

$this->title = Yii::t('app', '{Create} {Crontab}', ['Create' => Yii::t('app', 'Create'),'Crontab' => Yii::t('app', 'Crontab'),]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Crontabs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="crontab-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?=
    $this->render('_form', [
        'model' => $model,
    ])
    ?>

</div>
