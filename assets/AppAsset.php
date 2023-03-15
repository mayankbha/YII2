<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 */

namespace app\assets;

use yii\bootstrap\BootstrapAsset;
use yii\jui\JuiAsset;
use yii\web\AssetBundle;
use yii\web\View;
use yii\web\YiiAsset;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/site.css',
        'css/styles.css',
    ];
    public $js = [
        'js/jquery.cookie.js',
    ];
    public $depends = [
        YiiAsset::class,
        BootstrapAsset::class,
        JuiAsset::class
    ];

    public $jsOptions = [
        'position' => View::POS_HEAD
    ];
}
