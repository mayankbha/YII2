<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 *
 * @var $this yii\web\View
 * @var $model \app\modules\admin\models\forms\ServersForm
 */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
?>

<?php $form = ActiveForm::begin() ?>
    <div class="row">
        <div class="col-sm-6">
            <?= $form->field($model, 'address')->input('text') ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'port')->input('text') ?>
        </div>
    </div>
    <?= $form->field($model, 'description')->textarea() ?>
    <?= $form->field($model, 'note')->textarea() ?>

    <div class="button-block">
        <?= Html::a(Yii::t('app', 'Back'), Url::toRoute('index'), ['class' => 'btn btn-link']); ?>
        <?= Html::submitButton(Yii::t('app', 'Submit'), ['class' => 'btn btn-primary']); ?>
    </div>
<?php ActiveForm::end() ?>
