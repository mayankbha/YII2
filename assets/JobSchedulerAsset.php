<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 */

namespace app\assets;

use yii\bootstrap\BootstrapAsset;
use yii\web\AssetBundle;
use yii\web\JqueryAsset;
use yii\web\YiiAsset;

class JobSchedulerAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $js = [
        'js/moment.js',
        'js/bootstrap-datetimepicker.min.js',
        'js/jstree.min.js',
        'js/typeahead.bundle.js',
        'js/bootstrap3-typeahead.js',
		'js/job_scheduler.js',
    ];
    public $css = [
        'css/bootstrap-datetimepicker.min.css',
        'css/jstree/style.min.css',
    ];
    public $depends = [
        AppAsset::class,
        YiiAsset::class,
        JqueryAsset::class,
        BootstrapAsset::class
    ];
}
