<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\widgets\grid\GridViewChangeSelfColumn;

/* @var $this yii\web\View */
/* @var $searchModel common\models\searchs\MenuSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', '{Menu}{Administration}',[
    'Menu' => Yii::t('app', 'Menu'),
    'Administration' => Yii::t('app', 'Administration'),
]);
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="menu-index">

    <p>
        <?= Html::a(Yii::t('app', '{Add}{Menu}',[
            'Add' => Yii::t('app', 'Add'),
            'Menu' => Yii::t('app', 'Menu'),
        ]), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'layout' => "{items}\n{summary}\n{pager}",
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'id',
                'filter' => false,
                'headerOptions' => [
                    'style' => [
                        'width' => '50px'
                    ]
                ]
            ],
            [
                'attribute' => 'menu_name',
                'headerOptions' => [
                    'style' => [
                        'min-width' => '180px'
                    ]
                ]
            ],
            [
                'attribute' => 'parent_id',
                'value' => function ($model) {
                    return $model->parent_id == 0 ? '顶级菜单' : $model->parent->menu_name;
                },
                'filter' => $parentMenus,
                'headerOptions' => [
                    'style' => [
                        'min-width' => '180px'
                    ]
                ]
            ],
            [
                'attribute' => 'link',
                'filter' => false,
                'class' => GridViewChangeSelfColumn::class,
                'options' => ['style' => ['width' => '260px']],
                'contentOptions' => ['style' => ['padding-top' => '0px', 'padding-bottom' => '0px;']],
                'plugOptions' => [
                    'type' => 'input',
                ]
            ],
            [
                'attribute' => 'icon',
                'filter' => false,
                'class' => GridViewChangeSelfColumn::class,
                'options' => ['style' => ['width' => '200px']],
                'contentOptions' => ['style' => ['padding-top' => '0px', 'padding-bottom' => '0px;']],
                'plugOptions' => [
                    'type' => 'input',
                ]
            ],
            [
                'attribute' => 'is_show',
                'class' => GridViewChangeSelfColumn::class,
                'options' => ['style' => ['width' => '80px']],
                'filter' => [Yii::t('app', 'N'), Yii::t('app','Yes')],
            ],
            [
                'attribute' => 'sort_order',
                'filter' => false,
                'class' => GridViewChangeSelfColumn::class,
                'options' => ['style' => ['width' => '80px']],
                'contentOptions' => ['style' => ['padding-top' => '0px', 'padding-bottom' => '0px;']],
                'plugOptions' => [
                    'type' => 'input',
                ]
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'options' => ['width' => '70px'],
            ],
        ],
    ]); ?>
</div>
