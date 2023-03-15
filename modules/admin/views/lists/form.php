<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 */

/* @var $this yii\web\View */
/* @var $model \app\modules\admin\models\forms\ListsForm */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
?>

<?php $form = ActiveForm::begin() ?>
    <?= $form->field($model, 'list_name')->input('text'); ?>
    <?= $form->field($model, 'description')->textarea(); ?>
    <?= $form->field($model, 'entry_name')->input('text'); ?>
    <?= $form->field($model, 'groups')->input('text'); ?>
    <?= $form->field($model, 'weight')->input('text'); ?>
    <?= $form->field($model, 'note')->textarea(); ?>
    <?= $form->field($model, 'products')->input('text'); ?>
    <?= $form->field($model, 'restrict_code')->input('text'); ?>

    <div class="button-block">
        <?= Html::a(Yii::t('app', 'Back'), Url::toRoute('index'), ['class' => 'btn btn-link']); ?>
        <?= Html::submitButton(Yii::t('app', 'Submit'), ['class' => 'btn btn-primary']); ?>
    </div>
<?php ActiveForm::end() ?>
