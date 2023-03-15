<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 */

namespace app\modules\admin\models\forms;

use app\modules\admin\models\Image;
use app\modules\admin\models\User;
use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\web\UploadedFile;

/**
 * Class ChatSettingsForm
 */
class ChatSettingsForm extends Model
{
    public $enabledNotifications;
    public $refreshInterval = 5;

    public function rules()
    {
        return [
            [['refreshInterval'], 'integer'],
            [['enabledNotifications'], 'boolean'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'refreshInterval' => Yii::t('app', 'Refresh interval'),
            'enabledNotifications' => Yii::t('app', 'Enabled notifications'),
        ];
    }
}
