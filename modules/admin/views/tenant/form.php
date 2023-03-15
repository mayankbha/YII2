<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 * @var $this yii\web\View
 * @var $model \app\modules\admin\models\forms\TenantForm
 */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use app\models\GetListList;
use app\modules\admin\models\Image;
use app\modules\admin\models\forms\ImageForm;
use app\models\UserAccount;
use kartik\color\ColorInput;

$baseLists = GetListList::getArrayForSelectByNames(GetListList::$baseListNames);
$addressLists = GetListList::getArrayForSelectByNames(GetListList::$addressListName, false);

$logos = [ImageForm::TYPE_LOGO_HEADER => [], ImageForm::TYPE_LOGO_MAIN => []];
$logosArray = ($logosArray = Image::getData()) ? $logosArray->list : [];
foreach ($logosArray as $item) {
    $logos[$item['type']]["{$item['list_name']}.{$item['entry_name']}"] = "{$item['list_name']}: {$item['entry_name']}";
}

?>

<?php $form = ActiveForm::begin() ?>
    <?= $form->field($model, 'Tenant')->input('text'); ?>
    <?= $form->field($model, 'Name')->input('text'); ?>

    <ul class="nav nav-tabs" role="tablist">
        <li role="presentation" class="active"><a href="#personal" aria-controls="personal" role="tab" data-toggle="tab">Personal</a></li>
        <li role="presentation"><a href="#regional" aria-controls="regional" role="tab" data-toggle="tab">Regional</a></li>
        <li role="presentation"><a href="#styles" aria-controls="styles" role="tab" data-toggle="tab">Styles</a></li>
        <li role="presentation"><a href="#email-server" aria-controls="styles" role="tab" data-toggle="tab">Email server</a></li>
        <li role="presentation"><a href="#sms-server" aria-controls="styles" role="tab" data-toggle="tab">SMS server</a></li>
		<li role="presentation"><a href="#ldap-setting" aria-controls="styles" role="tab" data-toggle="tab">LDAP setting</a></li>
    </ul>

    <!-- Tab panes -->
    <div class="tab-content">
        <div role="tabpanel" class="tab-pane active" id="personal">
            <?= $form->field($model, 'PinExpirationTime')->input('number'); ?>
            <?= $form->field($model, 'Contact')->input('text'); ?>

            <div class="row">
                <div class="col-sm-6">
                    <?= $form->field($model, 'Email')->input('email'); ?>
                </div>
                <div class="col-sm-6">
                    <?= $form->field($model, 'Phone')->input('text'); ?>
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading">LOGO</div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-sm-6">
                            <?= Html::label('Header') ?>
                            <?= Html::dropDownList('Logos[]', $model->Logos[0], $logos[ImageForm::TYPE_LOGO_HEADER], ['class' => 'form-control', 'prompt' => '-- Default --']); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= Html::label('Main') ?>
                            <?= Html::dropDownList('Logos[]', $model->Logos[1], $logos[ImageForm::TYPE_LOGO_MAIN], ['class' => 'form-control', 'prompt' => '-- Default --']); ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading">CHAT</div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model->ChatSettings, 'enabledNotifications')->checkbox(); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model->ChatSettings, 'refreshInterval')->input('text'); ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading">ADDRESS</div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-sm-4">
                            <?= $form->field($model, 'Address1')->input('text'); ?>
                        </div>
                        <div class="col-sm-4">
                            <?= $form->field($model, 'Address2')->input('text'); ?>
                        </div>
                        <div class="col-sm-4">
                            <?= $form->field($model, 'Address3')->input('text'); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4">
                            <?= $form->field($model, 'City')->dropDownList($addressLists[GetListList::BASE_NAME_CITY]) ?>
                        </div>
                        <div class="col-sm-4">
                            <?= $form->field($model, 'StateRegion')->dropDownList($addressLists[GetListList::BASE_NAME_STATE]) ?>
                        </div>
                        <div class="col-sm-4">
                            <?= $form->field($model, 'Country')->dropDownList($addressLists[GetListList::BASE_NAME_COUNTRY]) ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'Postal')->input('text'); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'PostalExtend')->input('text'); ?>
                        </div>
                    </div>
                </div>
            </div>

            <?= $form->field($model, 'Comments')->textarea(); ?>
        </div>
        <div role="tabpanel" class="tab-pane" id="regional">
            <?= $form->field($model, 'DataLanguage')->dropDownList($baseLists[GetListList::BASE_NAME_LANGUAGE]) ?>
            <div class="panel panel-default">
                <div class="panel-heading">CURRENCY</div>
                <div class="panel-body">
                    <div class="col-sm-6">
                        <?= $form->field($model, 'DefaultCurrency')->dropDownList($baseLists[GetListList::BASE_NAME_CURRENCY]); ?>
                    </div>
                    <div class="col-sm-6">
                        <?= $form->field($model, 'DefaultCurrencyType')->dropDownList($baseLists[GetListList::BASE_NAME_CURRENCY_TYPE]); ?>
                    </div>
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading">DATE/TIME</div>
                <div class="panel-body">
                    <div class="col-sm-4">
                        <?= $form->field($model, 'DefaultTimeZone')->dropDownList($baseLists[GetListList::BASE_NAME_TIMEZONE]); ?>
                    </div>
                    <div class="col-sm-4">
                        <?= $form->field($model, 'DefaultTimeFormat')->dropDownList($baseLists[GetListList::BASE_NAME_TIME]); ?>
                    </div>
                    <div class="col-sm-4">
                        <?= $form->field($model, 'DefaultDate')->dropDownList($baseLists[GetListList::BASE_NAME_DATE]); ?>
                    </div>
                </div>
            </div>
        </div>
        <div role="tabpanel" class="tab-pane" id="styles">
            <div class="panel panel-default">
                <div class="panel-heading">BUTTONS</div>
                <div class="panel-body">
                    <div class="col-sm-12">
                        <?= $form->field($model, 'DefaultButtonStyle')->dropDownList($baseLists[GetListList::BASE_NAME_BUTTON_TYPE]); ?>
                    </div>
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading">MENU</div>
                <div class="panel-body">
                    <div class="col-sm-12">
                        <?= $form->field($model, 'DefaultMenuType')->dropDownList($baseLists[GetListList::BASE_NAME_MENU_TYPE]); ?>
                    </div>
                    <div class="col-sm-4">
                        <?= $form->field($model->StyleTemplate, 'menu_background')->widget(ColorInput::class); ?>
                    </div>
                    <div class="col-sm-4">
                        <?= $form->field($model->StyleTemplate, 'menu_text_color')->widget(ColorInput::class); ?>
                    </div>
                    <div class="col-sm-4">
                        <?= $form->field($model->StyleTemplate, 'highlight_color_selection')->widget(ColorInput::class); ?>
                    </div>
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading">BODY</div>
                <div class="panel-body">
                    <div class="col-sm-4">
                        <?= $form->field($model->StyleTemplate, 'border_size')->dropDownList(UserAccount::getBorderSizeAllowed()); ?>
                    </div>
                    <div class="col-sm-4">
                        <?= $form->field($model->StyleTemplate, 'border_color')->widget(ColorInput::class); ?>
                    </div>
                    <div class="col-sm-4">
                        <?= $form->field($model->StyleTemplate, 'background_color')->widget(ColorInput::class); ?>
                    </div>
                    <div class="col-sm-4">
                        <?= $form->field($model->StyleTemplate, 'link_color')->widget(ColorInput::class); ?>
                    </div>
                    <div class="col-sm-4">
                        <?= $form->field($model->StyleTemplate, 'text_color')->widget(ColorInput::class); ?>
                    </div>
                    <div class="col-sm-4">
                        <?= $form->field($model->StyleTemplate, 'info_color')->widget(ColorInput::class); ?>
                    </div>
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading">SECTION</div>
                <div class="panel-body">
                    <div class="col-sm-4">
                        <?= $form->field($model->StyleTemplate, 'section_header_color')->widget(ColorInput::class, [
                        ]); ?>
                    </div>
                    <div class="col-sm-4">
                        <?= $form->field($model->StyleTemplate, 'section_header_background')->widget(ColorInput::class); ?>
                    </div>
                    <div class="col-sm-4">
                        <?= $form->field($model->StyleTemplate, 'section_background_color')->widget(ColorInput::class); ?>
                    </div>
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading">HEADER</div>
                <div class="panel-body">
                    <div class="col-sm-4">
                        <?= $form->field($model->StyleTemplate, 'header_border_size')->dropDownList(UserAccount::getBorderSizeAllowed()); ?>
                    </div>
                    <div class="col-sm-4">
                        <?= $form->field($model->StyleTemplate, 'header_color')->widget(ColorInput::class); ?>
                    </div>
                    <div class="col-sm-4">
                        <?= $form->field($model->StyleTemplate, 'header_border_color')->widget(ColorInput::class); ?>
                    </div>
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading">SEARCH FIELD</div>
                <div class="panel-body">
                    <div class="col-sm-6">
                        <?= $form->field($model->StyleTemplate, 'search_border_color')->widget(ColorInput::class); ?>
                    </div>
                    <div class="col-sm-6">
                        <?= $form->field($model->StyleTemplate, 'search_border_selected_color')->widget(ColorInput::class); ?>
                    </div>
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading">FIELDS</div>
                <div class="panel-body">
                    <div class="col-sm-6">
                        <?= $form->field($model->StyleTemplate, 'field_border_color')->widget(ColorInput::class); ?>
                    </div>
                    <div class="col-sm-6">
                        <?= $form->field($model->StyleTemplate, 'field_border_selected_color')->widget(ColorInput::class); ?>
                    </div>
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading">TAB</div>
                <div class="panel-body">
                    <div class="col-sm-6">
                        <?= $form->field($model->StyleTemplate, 'tab_selected_color')->widget(ColorInput::class); ?>
                    </div>
                    <div class="col-sm-6">
                        <?= $form->field($model->StyleTemplate, 'tab_unselected_color')->widget(ColorInput::class); ?>
                    </div>
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading">MESSAGE LINE</div>
                <div class="panel-body">
                    <div class="col-sm-6">
                        <?= $form->field($model->StyleTemplate, 'message_line_color')->widget(ColorInput::class); ?>
                    </div>
                    <div class="col-sm-6">
                        <?= $form->field($model->StyleTemplate, 'message_line_background')->widget(ColorInput::class); ?>
                    </div>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">CHART COLOR</div>
                <div class="panel-body">
                    <div class="col-sm-6">
                        <?= $form->field($model->StyleTemplate, 'chart_color_first')->widget(ColorInput::class); ?>
                    </div>
                    <div class="col-sm-6">
                        <?= $form->field($model->StyleTemplate, 'chart_color_second')->widget(ColorInput::class); ?>
                    </div>
                    <div class="col-sm-6">
                        <?= $form->field($model->StyleTemplate, 'chart_color_third')->widget(ColorInput::class); ?>
                    </div>
                    <div class="col-sm-6">
                        <?= $form->field($model->StyleTemplate, 'chart_color_fourth')->widget(ColorInput::class); ?>
                    </div>
                </div>
            </div>
        </div>
        <div role="tabpanel" class="tab-pane" id="email-server">
            <?= $form->field($model, 'EmailServer')->textInput() ?>
            <?= $form->field($model, 'EmailAccount')->textInput() ?>
            <?= $form->field($model, 'EmailPassword')->passwordInput(['value' => '']) ?>
        </div>
        <div role="tabpanel" class="tab-pane" id="sms-server">
            <?= $form->field($model, 'TwilioSid')->textInput() ?>
            <?= $form->field($model, 'TwilioAuthToken')->textInput() ?>
            <?= $form->field($model, 'TwilioPhone')->textInput() ?>
        </div>
		<div role="tabpanel" class="tab-pane" id="ldap-setting">
            <?= $form->field($model, 'LDAPServer')->textInput() ?>
            <?= $form->field($model, 'LDAPSuperUserDN')->textInput() ?>
            <?= $form->field($model, 'LDAPPassword')->passwordInput(['value' => '']) ?>
        </div>
    </div>


    <div class="button-block">
        <?= Html::a(Yii::t('app', 'Back'), Url::toRoute('index'), ['class' => 'btn btn-link']); ?>
        <?= Html::submitButton(Yii::t('app', 'Submit'), ['class' => 'btn btn-primary']); ?>
    </div>
<?php ActiveForm::end() ?>
