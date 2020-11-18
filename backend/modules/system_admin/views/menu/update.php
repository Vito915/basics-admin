<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Menu */

$this->title = Yii::t('app', '{Update}{Menu}: {name}', [
    'Update' => Yii::t('app', 'Update'),
    'Menu' => Yii::t('app', 'Menu'),
    'name' => $model->menu_name,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', '{Menu}{Administration}',[
    'Menu' => Yii::t('app', 'Menu'),
    'Administration' => Yii::t('app', 'Administration'),
]), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->menu_name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="menu-update">

    <?= $this->render('_form', [
        'model' => $model,
        'parentMenus' => $parentMenus,
    ]) ?>

</div>
