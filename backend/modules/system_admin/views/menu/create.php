<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Menu */

$this->title = Yii::t('app', '{Add}{Menu}', [
        'Add' => Yii::t('app', 'Add'),
        'Menu' => Yii::t('app', 'Menu'),
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', '{Menu}{Administration}',[
    'Menu' => Yii::t('app', 'Menu'),
    'Administration' => Yii::t('app', 'Administration'),
]), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="menu-create">

    <?= $this->render('_form', [
        'model' => $model,
        'parentMenus' => $parentMenus,
    ]) ?>

</div>
