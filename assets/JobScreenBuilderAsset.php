<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 */

namespace app\assets;

use yii\helpers\Url;
use yii\web\AssetBundle;
use yii\web\View;

class JobScreenBuilderAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $js = [
        'js/builder/job_creator.js',
        'js/jquery.ui.touch-punch.min.js',
        'js/jstree.min.js',
        'js/lodash.js',
        'js/gridstack.min.js',
        'js/gridstack.jQueryUI.min.js',
        'js/bootstrap-duration-picker.js'
    ];
    public $css = [
        'css/jstree/style.min.css',
        'css/gridstack.min.css',
        'css/bootstrap-duration-picker.css'
    ];
    public $depends = [
        AppAsset::class,
    ];

    public $jsOptions = [
        'position' => View::POS_HEAD
    ];

    /**
     * Registers the CSS and JS files with the given view.
     * @param \yii\web\View $view the view that the asset files are to be registered with.
     */
    public function registerAssetFiles($view)
    {
        $view->registerJs("screenCreator.setParamsUrl('" . Url::to(['/admin/job-scheduler/lib-function-params'], true) . "');", View::POS_HEAD);
        $view->registerJs("screenCreator.setFuncUrl('" . Url::to(['/admin/job-scheduler/lib-functions'], true) . "');", View::POS_HEAD);
        $view->registerJs("screenCreator.setFuncExtensionUrl('" . Url::to(['/admin/job-scheduler/lib-function-extension'], true) . "');", View::POS_HEAD);
        $view->registerJs("screenCreator.setLinkUrl('" . Url::to(['/admin/job-scheduler/get-function-extension'], true) . "');", View::POS_HEAD);
        $view->registerJs("screenCreator.setSearchConfigParamUrl('" . Url::to(['/admin/job-scheduler/get-screen-search-params'], true) . "');", View::POS_HEAD);
        $view->registerJs(" $('#duration').durationPicker({ showSeconds: false });", View::POS_END);

        return parent::registerAssetFiles($view);
    }
}
