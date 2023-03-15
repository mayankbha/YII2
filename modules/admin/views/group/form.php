<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 */

/* @var $this yii\web\View */
/* @var $model \app\modules\admin\models\forms\GroupForm */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$this->registerJs("$('#groupform-document_group').trigger('change')");
?>

<?php $form = ActiveForm::begin() ?>
    <?= $form->field($model, 'group_name')->input('text'); ?>
    <?= $form->field($model, 'group_description')->textarea(); ?>

    <div class="button-block">
        <?= Html::a(Yii::t('app', 'Back'), Url::toRoute('index'), ['class' => 'btn btn-link']); ?>
        <?= Html::submitButton(Yii::t('app', 'Submit'), ['class' => 'btn btn-primary']); ?>
    </div>
<?php ActiveForm::end() ?>
