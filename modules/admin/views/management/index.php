<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 * @var $this yii\web\View
 */

use yii\helpers\Html;
use app\components\ThemeHelper;

$this->title = Yii::t('app', 'Management');
?>

<?= ThemeHelper::printFlashes(); ?>

<div class="management-info-block">
    <div class="alert-wrap">
        <div class="alert alert-warning alert-dismissible" role="alert">
            <span class="alert-icon">
                <span class="icon"></span>
            </span>
            Loading...
        </div>
    </div>
</div>

<script>
    function getInfo() {
        $.get("<?= \yii\helpers\Url::toRoute('get-info') ?>", function (data) {
            $('.management-info-block').html(data);
        });
    }
    setInterval(getInfo, 10000);
    getInfo();
</script>
