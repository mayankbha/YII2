<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 */

namespace app\assets;

use yii\helpers\Url;
use yii\web\AssetBundle;
use yii\web\View;

class ScreenBuilderAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $js = [
        'js/builder/creator.js',
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
        $view->registerJs("screenCreator.setParamsUrl('" . Url::to(['/admin/screen/lib-function-params'], true) . "');", View::POS_HEAD);
        $view->registerJs("screenCreator.setFuncUrl('" . Url::to(['/admin/screen/lib-functions'], true) . "');", View::POS_HEAD);
        $view->registerJs("screenCreator.setFuncExtensionUrl('" . Url::to(['/admin/screen/lib-function-extension'], true) . "');", View::POS_HEAD);
        $view->registerJs("screenCreator.setLinkUrl('" . Url::to(['/admin/screen/get-function-extension'], true) . "');", View::POS_HEAD);
        $view->registerJs("screenCreator.setSearchConfigParamUrl('" . Url::to(['/admin/screen/get-screen-search-params'], true) . "');", View::POS_HEAD);
        $view->registerJs(" $('#duration').durationPicker({
                              showSeconds: false
                            });", View::POS_END);

        return parent::registerAssetFiles($view);
    }
}
