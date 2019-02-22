<?php

use backend\modules\system_admin\assets\SystemAssets;
use common\models\Banner;
use yii\helpers\Html;
use yii\web\View;
use yii\web\YiiAsset;
use yii\widgets\DetailView;

/* @var $this View */
/* @var $model Banner */

YiiAsset::register($this);
SystemAssets::register($this);

$this->title = Yii::t('app', '{Propaganda}{Page}{Detail}',[
    'Propaganda' => Yii::t('app', 'Propaganda'),
    'Page' => Yii::t('app', 'Page'),
    'Detail' => Yii::t('app', 'Detail'),
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', '{Propaganda}{List}',[
    'Propaganda' => Yii::t('app', 'Propaganda'),
    'List' => Yii::t('app', 'List'),
]), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="banner-view system-view">

    <p>
        <?= Html::a(Yii::t('app', 'Edit'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary btn-flat']) ?>
        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger btn-flat',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>
    
    <ul class="nav nav-tabs" role="tablist">
        <li role="presentation" class="active">
            <a href="#basics" role="tab" data-toggle="tab" aria-controls="basics" aria-expanded="true">基本信息</a>
        </li>
    </ul>
    
    <div class="tab-content">
        <!--基本信息-->
        <div role="tabpanel" class="tab-pane fade active in" id="basics" aria-labelledby="basics-tab">
            <?= DetailView::widget([
                'model' => $model,
                'template' => '<tr><th class="detail-th">{label}</th><td class="detail-td">{value}</td></tr>',
                'attributes' => [
                    'title',
                    [
                        'attribute' => 'path',
                        'format' => 'raw',
                        'value' => Html::img($model->path, ['style' => ['max-width' => '680px']]),
                    ],
                    'link',
                    [
                        'attribute' => 'target',
                        'format' => 'raw',
                        'value' => Banner::$targetType[$model->target],
                    ],
                    [
                        'attribute' => 'type',
                        'format' => 'raw',
                        'value' => Banner::$contentType[$model->type],
                    ],
                    [
                        'attribute' => 'is_publish',
                        'label' => Yii::t('app', 'Publish'),
                        'format' => 'raw',
                        'value' => Banner::$publishStatus[$model->is_publish],
                    ],
                    'sort_order',
                    [
                        'attribute' => 'created_by',
                        'format' => 'raw',
                        'value' => !empty($model->created_by) ? $model->adminUser->nickname : 'null',
                    ],
                    'des:ntext',
                    'created_at:datetime',
                    'updated_at:datetime',
                ],
            ]) ?>
        </div>
    </div>
</div>
