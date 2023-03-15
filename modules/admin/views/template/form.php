<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 *
 * @var $this yii\web\View
 * @var $model \app\modules\admin\models\forms\TemplateForm
 */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
?>

<?php $form = ActiveForm::begin() ?>
    <div class="row">
        <div class="col-sm-6">
            <?= $form->field($model, 'lib_name')->input('text') ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'data_source')->input('text') ?>
        </div>
    </div>
    <br />
    <div class="row">
        <div class="col-sm-4">
            <?= $form->field($model, 'alias_table')->input('text') ?>
        </div>
        <div class="col-sm-4">
            <?= $form->field($model, 'alias_field')->input('text') ?>
        </div>
        <div class="col-sm-4">
            <?= $form->field($model, 'field_type')->input('text') ?>
        </div>
    </div>

    <div class="button-block">
        <?= Html::a(Yii::t('app', 'Back'), Url::toRoute('index'), ['class' => 'btn btn-link']); ?>
        <?= Html::submitButton(Yii::t('app', 'Submit'), ['class' => 'btn btn-primary']); ?>
    </div>
<?php ActiveForm::end() ?>
