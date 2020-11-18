<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Menu */

$this->title = $model->menu_name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', '{Menu}{Administration}',[
    'Menu' => Yii::t('app', 'Menu'),
    'Administration' => Yii::t('app', 'Administration'),
]), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="menu-view">

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            [
                'attribute' => 'parent_id',
                'value' => $model->parent_id == 0 ? '顶级菜单' : $model->parent->menu_name,
            ],
            'menu_name',
            'alias_name',
            'link',
            'icon',
            [
                'attribute' => 'is_show',
                'value' => $model->is_show == 0 ? Yii::t('app', 'N') : Yii::t('app', 'Y'),
            ],
            [
                'attribute' => 'is_jump',
                'value' => $model->is_jump == 0 ? Yii::t('app', 'N') : Yii::t('app', 'Y'),
            ],
            [
                'attribute' => 'level',
                'value' => $model->level == 0 ? Yii::t('app', 'Top Level') : Yii::t('app', 'Sublevel'),
            ],
            'sort_order',
            [
                'attribute' => 'is_del',
                'value' => $model->is_del == 0 ? Yii::t('app', 'N') : Yii::t('app', 'Y'),
            ],
            'created_at:datetime',
            'updated_at:datetime',
        ],
    ]) ?>

</div>
