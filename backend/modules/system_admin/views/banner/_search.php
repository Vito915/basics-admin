<?php

use common\models\Banner;
use common\models\searchs\BannerSearch;
use kartik\widgets\Select2;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this View */
/* @var $model BannerSearch */
/* @var $form ActiveForm */
?>

<div class="banner-search system-search">

    <?php $form = ActiveForm::begin([
        'options'=>[
            'id' => 'banner-search-form',
            'class' => 'form form-horizontal',
        ],
        'action' => ['index'],
        'method' => 'get',
        'fieldConfig' => [  
            'template' => "{label}\n<div class=\"col-lg-10 col-md-10\">{input}</div>",  
            'labelOptions' => [
                'class' => 'col-lg-2 col-md-2 control-label form-label',
            ],  
        ], 
    ]); ?>
    
    <div class="col-lg-12 col-md-12 panel">
    
        <div class="clo-lg-6 col-md-6 clear-padding">
            <!--banner名称-->
            <?= $form->field($model, 'title')->textInput([
                'placeholder' => '请输Banner名称', 'onchange' => 'submitForm()'
            ])->label(Yii::t('app', 'Name') . '：') ?>
            
            <!--创建人-->
            <?= $form->field($model, 'created_by', [
                'template' => "{label}\n<div class=\"col-lg-3 col-md-3\">{input}</div>",  
            ])->widget(Select2::class, [
                'data' => $createdBy,
                'hideSearch' => true,
                'options' => ['placeholder' => Yii::t('app', 'All')],
                'pluginOptions' => ['allowClear' => true],
                'pluginEvents' => ['change' => 'function(){ submitForm()}']
            ])->label(Yii::t('app', 'Created By') . '：') ?>
        </div>
        
        <div class="clo-lg-6 col-md-6 clear-padding">
            <!--是否发布-->
            <?php echo $form->field($model, 'is_publish', [
                'template' => "{label}\n<div class=\"col-lg-3 col-md-3\">{input}</div>",  
            ])->widget(Select2::class, [
                'model' => $model,
                'attribute' => 'is_publish',
                'data' => Banner::$publishStatus,
                'hideSearch' => true,
                'options' => ['placeholder' => Yii::t('app', 'All')],
                'pluginOptions' => [
                    'allowClear' => true,
                ],
                'pluginEvents' => ['change' => 'function(){ submitForm()}']
            ])->label(Yii::t('app', '{Is}{Publish}',[
                'Is' => Yii::t('app', 'Is'), 'Publish' => Yii::t('app', 'Publish')]) . '：') ?>
        </div>
        
    </div>

    <?php ActiveForm::end(); ?>

</div>
    
<script type="text/javascript">
    
    // 提交表单    
    function submitForm (){
        $('#banner-search-form').submit();
    }   
    
</script>