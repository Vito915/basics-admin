<?php

use common\models\searchs\ConfigSearch;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this View */
/* @var $model ConfigSearch */
/* @var $form ActiveForm */
?>

<div class="config-search system-search">

    <?php $form = ActiveForm::begin([
        'options'=>[
            'id' => 'config-search-form',
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

            <?= $form->field($model, 'config_name') ?>

            <?= $form->field($model, 'des') ?>
            
        </div>
        
        <div class="clo-lg-6 col-md-6 clear-padding">
            
            <?= $form->field($model, 'config_value') ?>
            
        </div>
        
    </div>

    <?php ActiveForm::end(); ?>

</div>
    
<script type="text/javascript">
    
    // 提交表单    
    function submitForm (){
        $('#config-search-form').submit();
    }   
    
</script>
