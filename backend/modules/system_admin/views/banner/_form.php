<?php

use common\models\Banner;
use common\widgets\webuploader\ImagePicker;
use kartik\widgets\SwitchInput;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this View */
/* @var $model Banner */
/* @var $form ActiveForm */
?>

<div class="banner-form">

    <?php $form = ActiveForm::begin([
        'options' => [
            'class' => 'form-horizontal',
            'enctype' => 'multipart/form-data',
        ],
        'fieldConfig' => [
            'template' => "{label}\n<div class=\"col-lg-9 col-md-9\">{input}</div>\n<div class=\"col-lg-7 col-md-7\">{error}</div>",
            'labelOptions' => ['class' => 'col-lg-2 col-md-2 control-label', 'style' => ['color' => '#999999', 'font-weight' => 'normal', 'padding-left' => '0']],
        ],
    ]); ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'path')->widget(ImagePicker::class)->label('路径（建议尺寸：1920*400）'); ?>

    <?= $form->field($model, 'link')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'type')->radioList(Banner::$contentType) ?>
    
    <?= $form->field($model, 'target')->radioList(Banner::$targetType) ?>
    
    <?= $form->field($model, 'is_publish')->widget(SwitchInput::class, [
        'pluginOptions' => [
            'onText' => Yii::t('app', 'Y'),
            'offText' => Yii::t('app', 'N'),
        ],
        'containerOptions' => [
            'class' => '',
        ],
    ])->label(Yii::t('app', '{Is}{Publish}',[
                'Is' => Yii::t('app', 'Is'), 'Publish' => Yii::t('app', 'Publish')])) ?>
    
    <?= $form->field($model, 'sort_order')->textInput() ?>

    <?= $form->field($model, 'des')->textarea(['rows' => 5]) ?>

    <div class="form-group" style="padding-left: 50px;">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), 
                ['class' => $model->isNewRecord ? 'btn btn-success btn-flat' : 'btn btn-primary btn-flat']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
