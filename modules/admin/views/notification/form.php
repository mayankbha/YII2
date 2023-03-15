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
use app\models\GetListList;

$baseLists = GetListList::getArrayForSelectByNames(GetListList::$notifyListName);
?>

<?php $form = ActiveForm::begin() ?>
    <div class="row">
        <div class="col-sm-6">
            <?= $form->field($model, 'notify_name')->input('text') ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'notify_type')->dropDownList($baseLists[GetListList::BASE_NAME_NOTIFY_TYPE]) ?>
        </div>
    </div>
    <?= $form->field($model, 'description')->input('text') ?>
    <br />
    <div class="row">
        <div class="col-sm-6">
            <?= $form->field($model, 'recipient_type')->dropDownList($baseLists[GetListList::BASE_NAME_NOTIFY_RECIPIENT_TYPE]) ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'recipient_list')->input('text') ?>
        </div>
    </div>
    <br />
    <?= $form->field($model, 'params')->input('text') ?>
    <?= $form->field($model, 'body')->textarea(['rows' => 7]) ?>
    <?= $form->field($model, 'note')->textarea() ?>

    <div class="button-block">
        <?= Html::a(Yii::t('app', 'Back'), Url::toRoute('index'), ['class' => 'btn btn-link']); ?>
        <?= Html::submitButton(Yii::t('app', 'Submit'), ['class' => 'btn btn-primary']); ?>
    </div>
<?php ActiveForm::end() ?>
