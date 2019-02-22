<?php

use backend\modules\system_admin\assets\SystemAssets;
use common\models\Banner;
use common\models\searchs\BannerSearch;
use common\widgets\grid\GridViewChangeSelfColumn;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\View;

/* @var $this View */
/* @var $searchModel BannerSearch */
/* @var $dataProvider ActiveDataProvider */

SystemAssets::register($this);

$this->title = Yii::t('app', '{Propaganda}{List}',[
    'Propaganda' => Yii::t('app', 'Propaganda'),
    'List' => Yii::t('app', 'List'),
]);
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="banner-index">
    
    <?= $this->render('_search', [
        'model' => $searchModel,
        'createdBy' => $createdBy,
    ]) ?>
    
    <div class="panel pull-left">
        
        <div class="title">
            <div class="pull-right">
                <?= Html::a(Yii::t('app', 'Create'), ['create'], ['class' => 'btn btn-success btn-flat']) ?>
            </div>
        </div>
        
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
//            'filterModel' => $searchModel,
            'layout' => "{items}\n{summary}\n{pager}",
            'columns' => [
                [
                    'class' => 'yii\grid\SerialColumn',
                    'headerOptions' => [
                        'style' => 'width: 35px'
                    ],
                ],
                [
                    'attribute' => 'title',
                    'label' => Yii::t('app', 'Name'),
                    'headerOptions' => [
                        'style' => 'width: 180px'
                    ],
                    'contentOptions' => [
                        'style' => [
                            'text-align' => 'center',
                        ],
                    ],
                ],
                [
                    'attribute' => 'path',
                    'label' => Yii::t('app', 'Path'),
                    'contentOptions' => [
                        'style' => [
                            'text-align' => 'center',
                            'white-space' => 'unset',
                            'word-break' => 'break-word',
                        ],
                    ],
                ],
                [
                    'attribute' => 'link',
                    'label' => Yii::t('app', 'Href'),
                    'headerOptions' => [
                        'style' => [
                            'min-width' => '90px'
                        ],
                    ],
                    'value' => function ($data){
                        return !empty($data['link']) ? $data['link'] : null;
                    },
                    'contentOptions' => [
                        'style' => [
                            'text-align' => 'center',
                            'white-space' => 'unset',
                            'word-break' => 'break-word',
                        ],
                    ],
                ],
                [
                    'attribute' => 'target',
                    'label' => Yii::t('app', '{Open}{Mode}',[
                        'Open' => Yii::t('app', 'Open'),
                        'Mode' => Yii::t('app', 'Mode'),
                    ]),
                    'headerOptions' => [
                        'style' => 'width: 90px'
                    ],
                    'value' => function ($data) {
                        return Banner::$targetType[$data->target];
                    },
                    'contentOptions' => [
                        'style' => [
                            'text-align' => 'center',
                        ],
                    ],
                ],
                [
                    'attribute' => 'sort_order',
                    'headerOptions' => [
                        'style' => 'width: 35px'
                    ],
                    'headerOptions' => [
                        'style' => [
                            'width' => '55px'
                        ],
                    ],
                    'class' => GridViewChangeSelfColumn::class,
                    'plugOptions' => [
                        'type' => 'input',
                    ],
                ],
                [
                    'attribute' => 'type',
                    'headerOptions' => [
                        'style' => 'width: 90px'
                    ],
                    'value' => function ($data){
                        return Banner::$contentType[$data->type];
                    },
                    'contentOptions' => [
                        'style' => [
                            'text-align' => 'center',
                        ],
                    ],
                ],
                [
                    'attribute' => 'is_publish',
                    'label' => Yii::t('app', '{Is}{Publish}',[
                        'Is' => Yii::t('app', 'Is'),
                        'Publish' => Yii::t('app', 'Publish'),
                    ]),
                    'headerOptions' => [
                        'style' => 'width: 80px'
                    ],
                    'class' => GridViewChangeSelfColumn::class,
                    'value' => function ($data){
                        return Banner::$publishStatus[$data->is_publish];
                    },
                    'contentOptions' => [
                        'style' => [
                            'text-align' => 'center',
                        ],
                    ],
                ],
                [
                    'attribute' => 'created_by',
                    'headerOptions' => [
                        'style' => 'width:90px'
                    ],
                    'value' => function ($data) {
                        return !empty($data->created_by) ? $data->created_by : null;
                    },
                    'contentOptions' => [
                        'style' => [
                            'text-align' => 'center',
                        ],
                    ],
                ],
                [
                    'attribute' => 'created_at',
                    'headerOptions' => [
                        'style' => 'width: 90px'
                    ],
                    'value' => function ($data){
                        return date('Y-m-d H:i', $data->created_at);
                    },
                    'contentOptions' => [
                        'style' => [
                            'text-align' => 'center',
                            'white-space' => 'unset',
                        ],
                    ],
                ],
                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{view} {update}{delete}',
                    'headerOptions' => [
                        'style' => 'width: 90px'
                    ],
                ],
            ],
        ]); ?>
    </div>
</div>
