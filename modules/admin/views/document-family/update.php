<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 */

/* @var $this yii\web\View */
/* @var $models array */

$this->title = Yii::t('app', 'Update document family');
?>

<h1><?= $this->title ?> - <?= $models[0]->family_name ?></h1>
<?php \app\components\ThemeHelper::printFlashes(); ?>

<div class="border-form-block">
    <?= $this->render('form', ['models' => $models]); ?>
</div>