<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 *
 * @var $this yii\web\View
 * @var $room array
 * @var $messages array
 * @var $groups array
 */

use yii\helpers\Html;
use yii\helpers\Url;
use app\modules\chat\models\Room;
use app\components\ThemeHelper;
use yii\bootstrap\ActiveForm;

$this->title = Yii::t('app', "Create new system room");
?>

<h1><?= $this->title ?></h1>
<?php ThemeHelper::printFlashes(); ?>

<div class="border-form-block">
  <?php echo $this->render('_form', ['model' =>$model]); ?>
</div>