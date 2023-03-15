<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 *
 * @var $this yii\web\View
 * @var $model \app\modules\admin\models\forms\UserForm
 * @var $isCreate boolean
 * @var $isUpdate boolean
 */

use app\models\GetListList;
use app\models\UserAccount;
use app\modules\admin\models\Group;
use app\modules\admin\models\DocumentGroup;
use app\modules\admin\models\Tenant;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\web\View;
use kartik\password\PasswordInput;
use kartik\color\ColorInput;
use app\modules\admin\models\Image;

$groupList = ($groupList = Group::getData()) ? $groupList->list : [];
$groupList = ArrayHelper::map($groupList, 'group_name', 'group_name');

$documentGroupList = ($documentGroupList = DocumentGroup::getData()) ? $documentGroupList->list : [];
$documentGroupList = ArrayHelper::map($documentGroupList, 'group_name', 'group_name');

$baseLists = GetListList::getArrayForSelectByNames(GetListList::$baseListNames);
$UserTypeLists = GetListList::getArrayForSelectByNames([GetListList::BASE_NAME_USER_TYPE], true, false);

$tenantArray = [];
$tenantArrayJs = [];
$tenantList = ($tenant_list = Tenant::getData()) ? $tenant_list->list : [];
foreach ($tenantList as $tenantItem) {
    $tenantArray[$tenantItem['pk']] = $tenantItem['pk'] . ': ' . $tenantItem['Name'];
    $tenantArrayJs[$tenantItem['pk']] = $tenantItem;
}

$this->registerJs("var tenantOptions =  " . json_encode($tenantArrayJs) . ";", View::POS_BEGIN);
?>

<?php $form = ActiveForm::begin(['action' => (Yii::$app->getRequest()->getQueryParam('action') == 'copy_user') ? Url::toRoute(['/admin/user/create']) : '', 'options' => [
        'id' => 'create-user-form',
        'data-model-id' => $model->id,
        'data-del-image-url' => Url::toRoute('delete-image'),
        'enctype' => 'multipart/form-data'
]]) ?>
    <!-- Nav tabs -->
    <ul class="nav nav-tabs" role="tablist">
        <li role="presentation" class="active"><a href="#personal" aria-controls="personal" role="tab" data-toggle="tab">Personal</a></li>
        <li role="presentation"><a href="#regional" aria-controls="regional" role="tab" data-toggle="tab">Regional</a></li>
        <li role="presentation"><a href="#styles" aria-controls="styles" role="tab" data-toggle="tab">Styles</a></li>
    </ul>

    <!-- Tab panes -->
    <div class="tab-content">
        <div role="tabpanel" class="tab-pane active" id="personal">
            <?= $form->field($model, 'user_name')->input('text'); ?>
            <?= $form->field($model, 'account_password')->widget(PasswordInput::class, [
                'options' => [
                    'value' => '',
                    'required' => isset($isCreate)
                ]
            ]) ?>
            <br />
            <?= $form->field($model, 'account_status')->checkbox(['value' => $model::ACTIVE_ACCOUNT, 'uncheck' => $model::INACTIVE_ACCOUNT]); ?>
            <br />
            <?= $form->field($model, 'account_type')->dropDownList($UserTypeLists[GetListList::BASE_NAME_USER_TYPE],[
                'id'=>'user_account_type',
                'class' => 'form-control js-security-filter',
                'data-security-filter-url' => Url::toRoute('get-user-form-list'),
            ]);?>
            <?= $form->field($model, 'tenant_code')->dropDownList($tenantArray, [
                'prompt' => '-- Select --',
                'class' => 'form-control js-tenant-options js-security-filter',
                'data-security-filter-url' => Url::toRoute('get-user-form-list'),
                'data-url' => Url::toRoute('set-tenant-settings'),
                'id'=>'user_tenant',
            ]); ?>
            <?= $form->field($model, 'account_security_type')->dropDownList([],[
                'id'=>'ast',
                'disabled'=>true,
                'data-value' => $model->account_security_type,
                'class' => 'form-control js-switch-security',
            ]); ?>
            <?= $form->field($model, 'security1')->input('text',['id'=>'sec1', 'disabled' => true,'class' => 'form-control js-disabled-input',]); ?>
            <?= $form->field($model, 'security2')->input('text',['id'=>'sec2', 'disabled' => true,'class' => 'form-control js-disabled-input',]); ?>
            <?= $form->field($model, 'security1_length')->input('hidden',['id'=>'sec1_len', 'disabled' => true,'class' => 'form-control js-disabled-input',])->label(false); ?>
            <?= $form->field($model, 'security2_length')->input('hidden',['id'=>'sec2_len', 'disabled' => true,'class' => 'form-control js-disabled-input',])->label(false); ?>
            <br />
            <?= $form->field($model, 'last_login')->input('datetime-local', ['disabled' => true]); ?>
            <?= $form->field($model, 'group_area')->dropDownList($groupList, ['multiple' => 'multiple']); ?>
            <?= $form->field($model, 'document_group')->dropDownList($documentGroupList, ['multiple' => 'multiple']); ?>

            <div class="row">
                <div class="col-sm-6">
                    <?= $form->field($model, 'phone')->input('text'); ?>
                </div>
                <div class="col-sm-6">
                    <?= $form->field($model, 'email')->input('email'); ?>
                </div>
            </div>
            <?= $form->field($model, 'account_name')->input('text'); ?>
        </div>
        <div role="tabpanel" class="tab-pane" id="regional">
            <?php //$form->field($model, 'language')->dropDownList($baseLists[GetListList::BASE_NAME_LANGUAGE]) ?>
            <div class="panel panel-default">
                <div class="panel-heading">CURRENCY</div>
                <div class="panel-body">
                    <div class="col-sm-6">
                        <?= $form->field($model, 'currencyformat_code')->dropDownList($baseLists[GetListList::BASE_NAME_CURRENCY]); ?>
                    </div>
                    <div class="col-sm-6">
                        <?= $form->field($model, 'currencytype_code')->dropDownList($baseLists[GetListList::BASE_NAME_CURRENCY_TYPE]); ?>
                    </div>
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading">DATE/TIME</div>
                <div class="panel-body">
                    <div class="col-sm-4">
                        <?= $form->field($model, 'timezone_code')->dropDownList($baseLists[GetListList::BASE_NAME_TIMEZONE]); ?>
                    </div>
                    <div class="col-sm-4">
                        <?= $form->field($model, 'timeformat_code')->dropDownList($baseLists[GetListList::BASE_NAME_TIME]); ?>
                    </div>
                    <div class="col-sm-4">
                        <?= $form->field($model, 'dateformat_code')->dropDownList($baseLists[GetListList::BASE_NAME_DATE]); ?>
                    </div>
                </div>
            </div>
        </div>
        <div role="tabpanel" class="tab-pane" id="styles">
            <div class="panel panel-default">
                <div class="panel-heading">MAIN FORMAT</div>
                <div class="panel-body">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <?= Html::label('Avatar', null, ['class' => 'control-label']) ?>
                            <?= Html::fileInput('avatar_array[]', null, ['multiple' => true, 'accept' => '.png, .jpg, .jpeg']) ?>
                        </div>
                    </div>
                    <div class="col-sm-12 img-section-wrapper">
                        <?php foreach($model->style_template->avatar_array as $image): ?>
                            <label class="img-thumbnail-wrapper">
                                <?= Html::img("data:image/gif;base64, {$image['logo_image_body']}", [
                                    'class' => 'img-thumbnail',
                                    'style' => ['max-height' => '100px']
                                ]) ?>
                                <?= Html::radio("{$model->style_template->formName()}[avatar]", $model->style_template->avatar == $image['pk'], ['value' => $image['pk']]) ?>
                                <button type="button" class="close js-delete-image"
                                        data-model-attr="avatar"
                                        data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </label>
                        <?php endforeach ?>
                    </div>
                    <div class="col-sm-6">
                        <?= $form->field($model, 'menutype_code')->dropDownList($baseLists[GetListList::BASE_NAME_MENU_TYPE]); ?>
                    </div>
                    <div class="col-sm-6">
                        <?= $form->field($model, 'button_style_code')->dropDownList($baseLists[GetListList::BASE_NAME_BUTTON_TYPE]); ?>
                    </div>
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading">MENU</div>
                <div class="panel-body">
                    <div class="col-sm-12 style-switcher">
                        <div class="btn-group" data-toggle="buttons">
                            <label class="btn btn-primary js-style-switcher <?php if(!$model->style_template->use_menu_images): ?>active<?php endif; ?>">
                                <?= Html::radio("{$model->style_template->formName()}[use_menu_images]", !$model->style_template->use_menu_images, ['value' => false]) ?>
                                Use styles
                            </label>
                            <label class="btn btn-primary js-style-switcher <?php if($model->style_template->use_menu_images): ?>active<?php endif; ?>">
                                <?= Html::radio("{$model->style_template->formName()}[use_menu_images]", $model->style_template->use_menu_images, ['value' => true]) ?>
                                Use images
                            </label>
                        </div>
                    </div>
                    <div class="style-case-wrapper" <?php if($model->style_template->use_menu_images): ?> style="display:none" <?php endif; ?>>
                        <div class="col-sm-4">
                            <?= $form->field($model->style_template, 'menu_background')->widget(ColorInput::class); ?>
                        </div>
                        <div class="col-sm-4">
                            <?= $form->field($model->style_template, 'menu_text_color')->widget(ColorInput::class); ?>
                        </div>
                        <div class="col-sm-4">
                            <?= $form->field($model->style_template, 'highlight_color_selection')->widget(ColorInput::class); ?>
                        </div>
                    </div>
                    <div class="image-case-wrapper" <?php if(!$model->style_template->use_menu_images): ?> style="display:none" <?php endif; ?>>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <?= Html::label('Menu background image', null, ['class' => 'control-label']) ?>
                                <?= Html::fileInput('menu_background_image_array[]', null, ['multiple' => true, 'accept' => '.png, .jpg, .jpeg']) ?>
                            </div>
                        </div>
                        <div class="col-sm-12 img-section-wrapper">
                            <?php foreach($model->style_template->menu_background_image_array as $image): ?>
                                <label class="img-thumbnail-wrapper">
                                    <?= Html::img("data:image/gif;base64, {$image['logo_image_body']}", [
                                        'class' => 'img-thumbnail',
                                        'style' => ['max-height' => '100px']
                                    ]) ?>
                                    <?= Html::radio("{$model->style_template->formName()}[menu_background_image]",  $model->style_template->menu_background_image == $image['pk'], ['value' => $image['pk']]) ?>
                                    <button type="button" class="close js-delete-image"
                                            data-model-attr="menu_background_image"
                                            data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </label>
                            <?php endforeach ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading">BODY</div>
                <div class="panel-body">
                    <div class="col-sm-12 style-switcher">
                        <div class="btn-group" data-toggle="buttons">
                            <label class="btn btn-primary js-style-switcher <?php if(!$model->style_template->use_body_images): ?>active<?php endif; ?>">
                                <?= Html::radio("{$model->style_template->formName()}[use_body_images]", !$model->style_template->use_body_images, ['value' => false]) ?>
                                Use styles
                            </label>
                            <label class="btn btn-primary js-style-switcher <?php if($model->style_template->use_body_images): ?>active<?php endif; ?>">
                                <?= Html::radio("{$model->style_template->formName()}[use_body_images]", $model->style_template->use_body_images, ['value' => true]) ?>
                                Use images
                            </label>
                        </div>
                    </div>
                    <div class="style-case-wrapper" <?php if($model->style_template->use_body_images): ?> style="display:none" <?php endif; ?>>
                        <div class="col-sm-4">
                            <?= $form->field($model->style_template, 'border_size')->dropDownList(UserAccount::getBorderSizeAllowed()); ?>
                        </div>
                        <div class="col-sm-4">
                            <?= $form->field($model->style_template, 'border_color')->widget(ColorInput::class); ?>
                        </div>
                        <div class="col-sm-4">
                            <?= $form->field($model->style_template, 'background_color')->widget(ColorInput::class); ?>
                        </div>
                        <div class="col-sm-4">
                            <?= $form->field($model->style_template, 'link_color')->widget(ColorInput::class); ?>
                        </div>
                        <div class="col-sm-4">
                            <?= $form->field($model->style_template, 'text_color')->widget(ColorInput::class); ?>
                        </div>
                        <div class="col-sm-4">
                            <?= $form->field($model->style_template, 'info_color')->widget(ColorInput::class); ?>
                        </div>
                    </div>
                    <div class="image-case-wrapper" <?php if(!$model->style_template->use_body_images): ?> style="display:none" <?php endif; ?>>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <?= Html::label('Background image', null, ['class' => 'control-label']) ?>
                                <?= Html::fileInput('background_image_array[]', null, ['multiple' => true, 'accept' => '.png, .jpg, .jpeg']) ?>
                            </div>
                        </div>
                        <div class="col-sm-12 img-section-wrapper">
                            <?php foreach($model->style_template->background_image_array as $image): ?>
                                <label class="img-thumbnail-wrapper">
                                    <?= Html::img("data:image/gif;base64, {$image['logo_image_body']}", [
                                        'class' => 'img-thumbnail',
                                        'style' => ['max-height' => '100px']
                                    ]) ?>
                                    <?= Html::radio("{$model->style_template->formName()}[background_image]", $model->style_template->background_image == $image['pk'], ['value' => $image['pk']]) ?>
                                    <button type="button" class="close js-delete-image"
                                            data-model-attr="background_image"
                                            data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </label>
                            <?php endforeach ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading">SECTION</div>
                <div class="panel-body">
                    <div class="col-sm-4">
                        <?= $form->field($model->style_template, 'section_header_color')->widget(ColorInput::class, [
                        ]); ?>
                    </div>
                    <div class="col-sm-4">
                        <?= $form->field($model->style_template, 'section_header_background')->widget(ColorInput::class); ?>
                    </div>
                    <div class="col-sm-4">
                        <?= $form->field($model->style_template, 'section_background_color')->widget(ColorInput::class); ?>
                    </div>
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading">HEADER</div>
                <div class="panel-body">
                    <div class="col-sm-4">
                        <?= $form->field($model->style_template, 'header_border_size')->dropDownList(UserAccount::getBorderSizeAllowed()); ?>
                    </div>
                    <div class="col-sm-4">
                        <?= $form->field($model->style_template, 'header_color')->widget(ColorInput::class); ?>
                    </div>
                    <div class="col-sm-4">
                        <?= $form->field($model->style_template, 'header_border_color')->widget(ColorInput::class); ?>
                    </div>
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading">SEARCH FIELD</div>
                <div class="panel-body">
                    <div class="col-sm-6">
                        <?= $form->field($model->style_template, 'search_border_color')->widget(ColorInput::class); ?>
                    </div>
                    <div class="col-sm-6">
                        <?= $form->field($model->style_template, 'search_border_selected_color')->widget(ColorInput::class); ?>
                    </div>
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading">FIELDS</div>
                <div class="panel-body">
                    <div class="col-sm-6">
                        <?= $form->field($model->style_template, 'field_border_color')->widget(ColorInput::class); ?>
                    </div>
                    <div class="col-sm-6">
                        <?= $form->field($model->style_template, 'field_border_selected_color')->widget(ColorInput::class); ?>
                    </div>
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading">TAB</div>
                <div class="panel-body">
                    <div class="col-sm-6">
                        <?= $form->field($model->style_template, 'tab_selected_color')->widget(ColorInput::class); ?>
                    </div>
                    <div class="col-sm-6">
                        <?= $form->field($model->style_template, 'tab_unselected_color')->widget(ColorInput::class); ?>
                    </div>
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading">MESSAGE LINE</div>
                <div class="panel-body">
                    <div class="col-sm-6">
                        <?= $form->field($model->style_template, 'message_line_color')->widget(ColorInput::class); ?>
                    </div>
                    <div class="col-sm-6">
                        <?= $form->field($model->style_template, 'message_line_background')->widget(ColorInput::class); ?>
                    </div>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">CHART COLOR</div>
                <div class="panel-body">
                    <div class="col-sm-6">
                        <?= $form->field($model->style_template, 'chart_color_first')->widget(ColorInput::class); ?>
                    </div>
                    <div class="col-sm-6">
                        <?= $form->field($model->style_template, 'chart_color_second')->widget(ColorInput::class); ?>
                    </div>
                    <div class="col-sm-6">
                        <?= $form->field($model->style_template, 'chart_color_third')->widget(ColorInput::class); ?>
                    </div>
                    <div class="col-sm-6">
                        <?= $form->field($model->style_template, 'chart_color_fourth')->widget(ColorInput::class); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="button-block">
        <?= Html::a(Yii::t('app', 'Back'), Url::toRoute('index'), ['class' => 'btn btn-link']); ?>
        <?= Html::submitButton(Yii::t('app', 'Submit'), ['class' => 'btn btn-primary js-user-submit']); ?>
    </div>
<?php ActiveForm::end() ?>