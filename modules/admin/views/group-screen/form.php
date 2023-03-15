<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 */

/* @var $this yii\web\View */
/* @var $model \app\modules\admin\models\forms\GroupScreenForm */

use app\modules\admin\models\Group;
use app\modules\admin\models\Menu;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$groupList = Group::getData()->list;
$groupList = ArrayHelper::map($groupList, 'group_name', 'group_name');

$menuList = Menu::getData()->list;
$menuList = ArrayHelper::map($menuList, 'menu_name', 'menu_name');
?>

<?php $form = ActiveForm::begin() ?>
    <div class="row">
        <div class="col-sm-6">
            <?= $form->field($model, 'add')->checkbox(['value' => $model::BOOL_API_TRUE, 'uncheck' => $model::BOOL_API_FALSE]); ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'add_show')->checkbox(['value' => $model::BOOL_API_TRUE, 'uncheck' => $model::BOOL_API_FALSE]); ?>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-6">
            <?= $form->field($model, 'delete')->checkbox(['value' => $model::BOOL_API_TRUE, 'uncheck' => $model::BOOL_API_FALSE]); ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'delete_show')->checkbox(['value' => $model::BOOL_API_TRUE, 'uncheck' => $model::BOOL_API_FALSE]); ?>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-6">
            <?= $form->field($model, 'edit')->checkbox(['value' => $model::BOOL_API_TRUE, 'uncheck' => $model::BOOL_API_FALSE]); ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'edit_show')->checkbox(['value' => $model::BOOL_API_TRUE, 'uncheck' => $model::BOOL_API_FALSE]); ?>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-6">
            <?= $form->field($model, 'copy')->checkbox(['value' => $model::BOOL_API_TRUE, 'uncheck' => $model::BOOL_API_FALSE]); ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'copy_show')->checkbox(['value' => $model::BOOL_API_TRUE, 'uncheck' => $model::BOOL_API_FALSE]); ?>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-6">
            <?= $form->field($model, 'inquire')->checkbox(['value' => $model::BOOL_API_TRUE, 'uncheck' => $model::BOOL_API_FALSE]); ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'inquire_show')->checkbox(['value' => $model::BOOL_API_TRUE, 'uncheck' => $model::BOOL_API_FALSE]); ?>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-6">
            <?= $form->field($model, 'execute')->checkbox(['value' => $model::BOOL_API_TRUE, 'uncheck' => $model::BOOL_API_FALSE]); ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'execute_show')->checkbox(['value' => $model::BOOL_API_TRUE, 'uncheck' => $model::BOOL_API_FALSE]); ?>
        </div>
    </div>

    <?= $form->field($model, 'group_name')->dropDownList($groupList, ['prompt' => '-- Select --']); ?>
    <?= $form->field($model, 'menu_name')->dropDownList($menuList, ['prompt' => '-- Select --']); ?>
    <?= $form->field($model, 'screen_text')->input('text'); ?>
    <?= $form->field($model, 'weight')->input('text'); ?>

    <div class="button-block">
        <?= Html::a(Yii::t('app', 'Back'), Url::toRoute('index'), ['class' => 'btn btn-link']); ?>
        <?= Html::submitButton(Yii::t('app', 'Submit'), ['class' => 'btn btn-primary']); ?>
    </div>
<?php ActiveForm::end() ?>
