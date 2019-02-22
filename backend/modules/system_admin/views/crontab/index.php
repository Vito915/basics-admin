<?php

use backend\modules\system_admin\assets\SystemAssets;
use common\models\searchs\CrontabSearch;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\View;

/* @var $this View */
/* @var $searchModel CrontabSearch */
/* @var $dataProvider ActiveDataProvider */

SystemAssets::register($this);

$this->title = Yii::t('app', 'Crontabs');
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="crontab-index">

    <?= $this->render('_search', [
        'model' => $searchModel,
    ]) ?>
    
    <div class="panel pull-left">
        
        <div class="title">
            <div class="pull-right">
                <?= Html::a(Yii::t('app', '{Create} {Crontab}', ['Create' => Yii::t('app', 'Create'), 
                    'Crontab' => Yii::t('app', 'Crontab'),]), ['create'], ['class' => 'btn btn-success btn-flat']) ?>
            </div>
        </div>

        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            //'filterModel' => $searchModel,
            'layout' => "{items}\n{summary}\n{pager}",
            'columns' => [
                [
                    'attribute' => 'id',
                    'headerOptions' => [
                        'style' => [
                            'width' => '100px',
                        ]
                    ],
                ],
                'name',
                'route',
                'crontab_str',
                [
                    'attribute' => 'last_rundate',
                    'headerOptions' => [
                        'style' => [
                            'width' => '140px',
                        ]
                    ],
                ],
                [
                    'attribute' => 'next_rundate',
                    'headerOptions' => [
                        'style' => [
                            'width' => '140px',
                        ]
                    ],
                ],
                //'exec_memory',
                [
                    'attribute' => 'exec_time',
                    'headerOptions' => [
                        'style' => [
                            'width' => '100px',
                        ]
                    ],
                ],
                [
                    'attribute' => 'status',
                    'headerOptions' => [
                        'style' => [
                            'width' => '100px',
                        ]
                    ],
                ],
                [
                    'attribute' => 'is_del',
                    'headerOptions' => [
                        'style' => [
                            'width' => '100px',
                        ]
                    ],
                ],
                //'created_at',
                //'updated_at',
                [
                    'class' => 'yii\grid\ActionColumn',
                    'headerOptions' => [
                        'style' => [
                            'width' => '100px',
                        ]
                    ],
                ],
            ],
        ]);?>
    </div>
</div>
