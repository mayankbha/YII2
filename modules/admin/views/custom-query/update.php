<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 */

/**
 * @var $this yii\web\View
 * @var $model \app\modules\admin\models\forms\CustomQueryForm
 * @var $builder boolean
 */

$this->title = Yii::t('app', 'Update custom query');
?>

<h1><?= $this->title ?></h1>
<?php \app\components\ThemeHelper::printFlashes(); ?>

<div class="border-form-block">
    <?= $this->render('form', [
        'model' => $model,
        'builder' => (isset($builder)) ? 'update' : false,
        'tablesInfo' => (isset($tablesInfo)) ? $tablesInfo : false
    ]); ?>
</div>