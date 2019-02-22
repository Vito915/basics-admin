<?php

use backend\modules\system_admin\assets\SystemAssets;
use common\models\searchs\ConfigSearch;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\View;

/* @var $this View */
/* @var $searchModel ConfigSearch */
/* @var $dataProvider ActiveDataProvider */

SystemAssets::register($this);

$this->title = Yii::t('app', '{Config}{Administration}',[
    'Config' => Yii::t('app', 'Config'),
    'Administration' => Yii::t('app', 'Administration'),
]);
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="config-index">

    <?= $this->render('_search', [
        'model' => $searchModel,
    ]) ?>
    
    <div class="panel pull-left">
        
        <div class="title">
            <div class="pull-right">
                <?= Html::a(Yii::t('app', '{Create}{Config}',['Create' => Yii::t('app', 'Create'),
                    'Config' => Yii::t('app', 'Config'),]), ['create'], ['class' => 'btn btn-success btn-flat']) ?>
            </div>
        </div>
    
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            //'filterModel' => $searchModel,
            'layout' => "{items}\n{summary}\n{pager}",
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],

                //'id',
                [
                    'attribute' => 'config_name',
                    'label' => Yii::t('app', '{Config}{Name}',[
                        'Config' => Yii::t('app', 'Config'),
                        'Name' => Yii::t('app', 'Name'),
                    ])
                ],
                [
                    'attribute' => 'config_value',
                    'label' => Yii::t('app', '{Config}{Value}',[
                        'Config' => Yii::t('app', 'Config'),
                        'Value' => Yii::t('app', 'Value'),
                    ])
                ],
                //'config_name',
                //'config_value:ntext',
                'des:ntext',
                //'created_at',
                // 'updated_at',

                ['class' => 'yii\grid\ActionColumn'],
            ],
        ]); ?>
    </div>
</div>
