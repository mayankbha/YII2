<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 */

/**
 * @var $this yii\web\View
 * @var $models array
 * @var $families array
 * @var $categories array
 */

$this->title = Yii::t('app', 'Update document group');
?>

<h1><?= $this->title ?> - <?= $models[0]->group_name ?></h1>
<?php \app\components\ThemeHelper::printFlashes(); ?>

<div class="border-form-block">
    <?= $this->render('form', ['models' => $models, 'families' => $families, 'categories' => $categories]); ?>
</div>