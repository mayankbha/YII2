<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 */

/**
 * @var $this yii\web\View
 * @var $model \app\modules\admin\models\forms\ScreenForm
 * @var $constructor bool|null
 * @var $libraries array,
 * @var $customQueries array,
 * @var $groupScreens array
 */

$this->title = Yii::t('app', 'Create screen');
$model->screen_tab_devices =  array_keys($model::$devices);
?>

<h1><?= $this->title ?></h1>
<?php \app\components\ThemeHelper::printFlashes(); ?>

<div class="border-form-block">
    <?= $this->render('form', [
        'model' => $model,
        'update' => false,
        'builder' => isset($builder) ? $builder : null,
        'customQueryList' => isset($customQueryList) ? $customQueryList : [],
    ]); ?>
</div>