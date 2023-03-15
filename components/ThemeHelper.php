<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 */

namespace app\components;

use Yii;
use yii\base\Component;
use yii\helpers\Html;

class ThemeHelper extends Component
{
    public static function printFlashes()
    {
        $flashes = [];
        foreach (Yii::$app->session->getAllFlashes() as $type => $message) {
            $button = Html::button('<span aria-hidden="true">×</span>', ['class' => 'close', 'data-dismiss' => 'alert', 'aria-label' => 'Close']);
            $icon = '<span class="alert-icon"><span class="icon"></span></span>';

            $flashes[] = Html::tag('div', $button . $icon . $message, ['class' => "alert alert-$type alert-dismissible", 'role' => 'alert']);
        }

        echo Html::tag('div', implode('', $flashes), ['class' => 'alert-wrap']);
    }
}