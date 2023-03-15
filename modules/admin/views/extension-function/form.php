<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 *
 * @var $this yii\web\View
 * @var $model \app\modules\admin\models\forms\ExtensionFunctionForm
 */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use app\models\GetListList;

$extensionsList = GetListList::getArrayForSelectByNames([GetListList::BASE_NAME_EXTENSION], true, false);
?>

<?php $form = ActiveForm::begin() ?>
    <div class="row">
        <div class="col-sm-6">
            <?= $form->field($model, 'datasource_lib')->input('text') ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'datasource_func')->input('text') ?>
        </div>
    </div>
    <br />
    <div class="row">
        <div class="col-sm-6">
            <?= $form->field($model, 'extension_lib')->input('text') ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'extension_func')->input('text') ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'extension_freemem_func')->input('text') ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'extension_type')->dropDownList($extensionsList[GetListList::BASE_NAME_EXTENSION]) ?>
        </div>
    </div>

    <div class="button-block">
        <?= Html::a(Yii::t('app', 'Back'), Url::toRoute('index'), ['class' => 'btn btn-link']); ?>
        <?= Html::submitButton(Yii::t('app', 'Submit'), ['class' => 'btn btn-primary']); ?>
    </div>
<?php ActiveForm::end() ?>
