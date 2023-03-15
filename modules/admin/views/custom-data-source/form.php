<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 *
 * @var $this yii\web\View
 * @var $model \app\modules\admin\models\forms\CustomDataSourceForm
 */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use app\models\BaseModel;
use app\models\CustomLibs;
?>

<?php $form = ActiveForm::begin() ?>
    <div class="row">
        <div class="col-sm-6">
            <?= $form->field($model, 'func_name')->input('text') ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'func_table')->input('text') ?>
        </div>
    </div>
    <?= $form->field($model, 'func_descr')->textarea() ?>
    <div class="row">
        <div class="col-sm-6">
            <?= $form->field($model, 'func_type')->dropDownList(array_combine(BaseModel::$functionTypes, BaseModel::$functionTypes)) ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'func_direction_type')->dropDownList(array_combine(CustomLibs::$directionTypes, CustomLibs::$directionTypes)) ?>
        </div>
    </div>
    <?= $form->field($model, 'func_layout_type')->input('text') ?>
    <div class="row">
        <div class="col-sm-6">
            <?= $form->field($model, 'related_func')->input('text') ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'related_field')->input('text') ?>
        </div>
    </div>

    <div class="button-block">
        <?= Html::a(Yii::t('app', 'Back'), Url::toRoute('index'), ['class' => 'btn btn-link']); ?>
        <?= Html::submitButton(Yii::t('app', 'Submit'), ['class' => 'btn btn-primary']); ?>
    </div>
<?php ActiveForm::end() ?>
