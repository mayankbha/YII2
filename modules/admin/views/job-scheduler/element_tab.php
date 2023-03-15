<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 */

/**
 * @var $this yii\web\View
 * @var $selfTab array
 * @var $mode string
 * @var $id boolean
 * @var $cache boolean
 * @var $lastFoundData string
 */

use app\components\RenderTabHelper;
use yii\helpers\Url;

//echo '<pre>'; print_r($selfTab);

?>

<?php $template = (new RenderTabHelper())->render(['template_layout' => $selfTab]); ?>

<?php echo $template; ?>
