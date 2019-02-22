<?php

namespace common\modules\webuploader\controllers;

use yii\web\Controller;

/**
 * Webuploader Demo
 *
 * @author Administrator
 */
class DemoController extends Controller{
    
    public function actionIndex(){
        return $this->render('index');
    }
    
    public function actionFilelist(){
        return $this->render('filelist');
    }
    
    public function actionImagePicker(){
        return $this->render('image-picker');
    }    
}
