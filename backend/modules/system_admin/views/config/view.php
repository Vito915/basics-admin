<?php

use backend\modules\system_admin\assets\SystemAssets;
use common\models\Config;
use yii\helpers\Html;
use yii\web\View;
use yii\web\YiiAsset;
use yii\widgets\DetailView;

/* @var $this View */
/* @var $model Config */

YiiAsset::register($this);
SystemAssets::register($this);

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', '{Config}{Administration}',[
    'Config' => Yii::t('app', 'Config'),
    'Administration' => Yii::t('app', 'Administration'),
]), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="config-view system-view">

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
                    'config_name',
                    'config_value:ntext',
                    'des:ntext',
                    'created_at:datetime',
                    'updated_at:datetime',
                ],
            ]) ?>
        </div>
    </div>

</div>
