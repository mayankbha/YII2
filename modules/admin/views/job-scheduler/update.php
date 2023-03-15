<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 *
 * @var $this yii\web\View
 * @var $model \app\modules\admin\models\forms\ExtensionFunctionForm
 */

$this->title = Yii::t('app', 'Update job scheduler');
?>

<h1><?= $this->title ?></h1>
<?php \app\components\ThemeHelper::printFlashes(); ?>

<div class="border-form-block">
    <?= $this->render('form', ['model' => $model, 'update' => true, 'builder' => isset($builder) ? $builder : null, 'customQueryList' => isset($customQueryList) ? $customQueryList : []]); ?>
</div>