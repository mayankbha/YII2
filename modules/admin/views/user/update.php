<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 */

/* @var $this yii\web\View */
/* @var $model \app\modules\admin\models\forms\UserForm */

if(Yii::$app->getRequest()->getQueryParam('action') == 'copy_user') {
	$this->title = Yii::t('app', 'Create user');
 } else {
	$this->title = Yii::t('app', 'Update user');
}
?>

<h1><?= $this->title ?> - <?= $model->user_name ?></h1>
<?php \app\components\ThemeHelper::printFlashes(); ?>

<div class="border-form-block">
    <?= $this->render('form', ['model' => $model, 'isUpdate' => true]); ?>
</div>