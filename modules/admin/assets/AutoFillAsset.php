<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 */

namespace app\modules\admin\assets;

use yii\bootstrap\BootstrapAsset;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\AssetBundle;
use yii\web\View;
use yii\web\YiiAsset;

class AutoFillAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $js = [
        'js/autofill.js',
    ];
    public $depends = [
        YiiAsset::class,
        BootstrapAsset::class,
    ];

    /**
     * Registers the CSS and JS files with the given view.
     * @param \yii\web\View $view the view that the asset files are to be registered with.
     */
    public function registerAssetFiles($view)
    {
        $autoFillConfig = Json::encode([
            'url' =>  Url::to(['autofill/fill-tables']),
            'urlCheckStatus' =>  Url::to(['autofill/execute-check-status'])
        ]);
        $view->registerJs("var autoFillConfig = $autoFillConfig", View::POS_HEAD);

        return parent::registerAssetFiles($view);
    }
}
