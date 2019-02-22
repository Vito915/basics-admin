<?php

namespace common\widgets\pagination;

use yii\web\AssetBundle;
use const YII_DEBUG;

/**
 * Main backend application asset bundle.
 */
class PaginationAsset extends AssetBundle
{
    public $sourcePath = '@common/widgets/pagination';
    public $baseUrl = '@common/widgets/pagination';
    public $css = [
        'css/pagination.css',
    ];
    public $js = [
        'js/pagination.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'rmrevin\yii\fontawesome\AssetBundle',
        'yii\bootstrap\BootstrapAsset',
    ];
    public $publishOptions = [
        'forceCopy' => YII_DEBUG,
    ];
}
