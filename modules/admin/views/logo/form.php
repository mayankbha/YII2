<?php
/* @var $this yii\web\View */
/* @var $model \app\modules\admin\models\forms\ImageForm */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
?>

<?php $form = ActiveForm::begin() ?>
<div class="row">
    <div class="col-sm-6">
        <?= $form->field($model, 'list_name')->input('text'); ?>
    </div>
    <div class="col-sm-6">
        <?= $form->field($model, 'entry_name')->input('text'); ?>
    </div>
</div>
<?= $form->field($model, 'description')->textarea(); ?>
<?= $form->field($model, 'type')->radioList($model::$types, ['class' => 'nt-save-form']); ?>

<?= $form->field($model, 'logo_image_body')->fileInput(['value' => '']); ?>
<?= (!empty($model->logo_image_body)) ? Html::img('data:image/gif;base64,' . $model->logo_image_body, ['class' => 'img-thumbnail', 'style' => ['max-width' => '280px']]) : null ?>

<div class="button-block">
    <?= Html::a(Yii::t('app', 'Back'), Url::toRoute('index'), ['class' => 'btn btn-link']); ?>
    <?= Html::submitButton(Yii::t('app', 'Submit'), ['class' => 'btn btn-primary']); ?>
</div>
<?php ActiveForm::end() ?>
