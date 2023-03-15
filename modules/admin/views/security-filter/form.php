<?php

/* @var $this yii\web\View */
/* @var $model \app\modules\admin\models\forms\SecurityFilterForm */

use app\models\GetListList;
use app\modules\admin\models\Tenant;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use app\modules\admin\models\Screen;
use yii\helpers\ArrayHelper;

$baseLists = GetListList::getArrayForSelectByNames([
    GetListList::BASE_NAME_USER_TYPE,
    GetListList::BASE_NAME_SECURITY_QUESTIONS,
    GetListList::BASE_NAME_AUTHORIZATION_TYPE
]);

$screenList = ($screenList = Screen::getData()) ? $screenList->list : [];
$tenantList = ($tenantList = Tenant::getData()) ? $tenantList->list : [];

$tenantArray = array();
foreach ($tenantList as $tenantItem) {
    $tenantArray[$tenantItem['pk']] = $tenantItem['pk'] . ': ' . $tenantItem['Name'];
}

if ($screenList) {
    $screenList = ArrayHelper::map($screenList, 'id', function ($data) {
        return "{$data['screen_tab_text']} - {$data['screen_lib']}";
    }, 'screen_name');
}
?>

<?php $form = ActiveForm::begin() ?>
    <?= $form->field($model, 'tenant')->dropDownList($tenantArray); ?>
    <?= $form->field($model, 'account_type')->dropDownList($baseLists[GetListList::BASE_NAME_USER_TYPE]); ?>
    <?= $form->field($model, 'user_type')->input('text'); ?>
    <?= $form->field($model, 'description')->textarea(); ?>
    <div class="row">
        <div class="col-sm-6">
            <?= $form->field($model, 'filter1')->input('text'); ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'filter1_length')->input('number'); ?>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-6">
            <?= $form->field($model, 'filter2')->input('text'); ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'filter2_length')->input('number'); ?>
        </div>
    </div>
    <div class="row" style="border-bottom: 1px solid #3275a3;">
        <div class="col-sm-6">
            <?= $form->field($model, 'allow_password_change')->checkbox(['value' => $model::VALUE_TRUE, 'uncheck' => $model::VALUE_FALSE]); ?>
            <?= $form->field($model, 'allow_settings_change')->checkbox(['value' => $model::VALUE_TRUE, 'uncheck' => $model::VALUE_FALSE]); ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'allow_self_registration')->checkbox(['value' => $model::VALUE_TRUE, 'uncheck' => $model::VALUE_FALSE]); ?>
            <?= $form->field($model, 'ldap')->checkbox(['value' => $model::VALUE_TRUE, 'uncheck' => $model::VALUE_FALSE]); ?>
            <?= $form->field($model, 'allow_chat')->checkbox(['value' => $model::VALUE_TRUE, 'uncheck' => $model::VALUE_FALSE]); ?>
        </div>
    </div>
    <br />
    <div class="row">
        <div class="col-sm-6">
            <?= $form->field($model, 'auth_types')->dropDownList($baseLists[GetListList::BASE_NAME_AUTHORIZATION_TYPE], ['multiple' => true]); ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'secret_questions')->dropDownList($baseLists[GetListList::BASE_NAME_SECURITY_QUESTIONS], ['multiple' => true]); ?>
        </div>
    </div>
    <?= $form->field($model, 'registration_screen_id')->dropDownList($screenList); ?>

    <div class="button-block">
        <?= Html::a(Yii::t('app', 'Back'), Url::toRoute('index'), ['class' => 'btn btn-link']); ?>
        <?= Html::submitButton(Yii::t('app', 'Submit'), ['class' => 'btn btn-primary']); ?>
    </div>
<?php ActiveForm::end() ?>