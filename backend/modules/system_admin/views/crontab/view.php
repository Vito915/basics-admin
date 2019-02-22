<?php

use backend\modules\system_admin\assets\SystemAssets;
use common\models\Crontab;
use yii\helpers\Html;
use yii\web\View;
use yii\web\YiiAsset;
use yii\widgets\DetailView;

/* @var $this View */
/* @var $model Crontab */

YiiAsset::register($this);
SystemAssets::register($this);

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Crontabs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="crontab-view system-view">

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary btn-flat']) ?>
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
                    'id',
                    'name',
                    'route',
                    'crontab_str',
                    'last_rundate',
                    'next_rundate',
                    'exec_memory',
                    'exec_time',
                    'status',
                    'is_del',
                    'created_at',
                    'updated_at',
                ],
            ]) ?>
        </div>
    </div>
    
</div>
