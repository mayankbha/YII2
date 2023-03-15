<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 */

namespace app\assets;

use yii\bootstrap\BootstrapAsset;
use yii\web\AssetBundle;
use yii\web\JqueryAsset;
use yii\web\View;

class CustomQueryAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $js = [
        'js/query_builder/app.js',
        'js_query_builder/js/sql-parser.min.js',
        'js_query_builder/js/query-builder.standalone.min.js',
    ];
    public $css = [
        'js_query_builder/css/query-builder.default.min.css',
    ];
    public $depends = [
        JqueryAsset::class,
        BootstrapAsset::class,
    ];

    public $jsOptions = [
        'position' => View::POS_HEAD
    ];
}
