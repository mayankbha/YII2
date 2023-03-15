<?php
/* @var $this yii\web\View */
/* @var $model \app\modules\admin\models\forms\ImageForm */

$this->title = Yii::t('app', 'Update logo');
?>

<h1><?= $this->title ?></h1>
<?php \app\components\ThemeHelper::printFlashes(); ?>

<div class="border-form-block">
    <?= $this->render('form', ['model' => $model]); ?>
</div>