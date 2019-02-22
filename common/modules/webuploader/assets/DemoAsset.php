<?php

namespace common\modules\webuploader\assets;

use yii\web\AssetBundle;

/**
 * Description of FrameworkImportAsset
 *
 * @author Administrator
 */
class DemoAsset extends AssetBundle{
    public $sourcePath = '@common/modules/webuploader/assets';
    public $css = [
       'css/demo.css',
       'styles/default.css',
    ];
    public $js = [
        'js/highlight.pack.js'
    ];
    public $depends = [
        'yii\web\YiiAsset'
    ];
    public $publishOptions = [
        'forceCopy' => YII_DEBUG,
    ];
}
