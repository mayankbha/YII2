<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 */

/* @var $this yii\web\View */
/* @var $model \app\modules\admin\models\forms\ListsForm */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use app\modules\admin\models\SecurityQuestions;
?>

<?php $form = ActiveForm::begin() ?>
    <?= $form->field($model, 'list_name')->hiddenInput(['value' => SecurityQuestions::LIST_NAME])->label(false); ?>
    <?= $form->field($model, 'entry_name')->input('text'); ?>
    <?= $form->field($model, 'description')->textarea()->label('Question'); ?>

    <div class="button-block">
        <?= Html::a(Yii::t('app', 'Back'), Url::toRoute('index'), ['class' => 'btn btn-link']); ?>
        <?= Html::submitButton(Yii::t('app', 'Submit'), ['class' => 'btn btn-primary']); ?>
    </div>
<?php ActiveForm::end() ?>
