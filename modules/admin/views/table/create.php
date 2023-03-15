<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 */

/* @var $this yii\web\View */

$this->title = Yii::t('app', 'Create Table');

?>

<h1><?= $this->title ?></h1>
<?php \app\components\ThemeHelper::printFlashes(); ?>

<div class="border-form-block">
	<?= $this->render('form', ['model' => $model, 'tables' => $tables, 'dataTypes' => $dataTypes]); ?>
</div>