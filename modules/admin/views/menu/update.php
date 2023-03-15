<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 */

/* @var $this yii\web\View */
/* @var $model \app\modules\admin\models\forms\MenuForm */

$this->title = Yii::t('app', 'Update menu');
?>

<h1><?= $this->title ?> - <?= $model->menu_name ?></h1>
<?php \app\components\ThemeHelper::printFlashes(); ?>

<div class="border-form-block">
    <?= $this->render('form', ['model' => $model]); ?>
</div>