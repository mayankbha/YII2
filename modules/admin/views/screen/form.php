<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 *
 * @var $this yii\web\View
 * @var $model \app\modules\admin\models\forms\ScreenForm
 * @var $update bool
 * @var $customQueryList array
 */

use app\models\CustomLibs;
use app\models\GetListList;
use app\modules\admin\models\GroupScreen;
use app\modules\admin\models\forms\ScreenForm;
use kdn\yii2\JsonEditor;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use insolita\iconpicker\Iconpicker;
use app\models\GetAliasInfo;

if (($libraryList = CustomLibs::getModelInstance()) && !empty($libraryList->lib_list)) {
    $libraryList = ArrayHelper::map($libraryList->lib_list, 'lib_name', function ($data) {
        return $data['lib_name'] . (!empty($data['lib_descr']) ? ' - ' . $data['lib_descr'] : '');
    });
}

if (($groupScreenList = GroupScreen::getData()) && !empty($groupScreenList->list)) {
    $groupScreenList = ArrayHelper::map($groupScreenList->list, 'screen_name', 'screen_name');
}

if (($aliasList = GetAliasInfo::getData([],[],['field_out_list' => ['AliasDatabaseTable']])) && !empty($aliasList->list)) {
    $aliasList = ArrayHelper::map($aliasList->list, 'AliasDatabaseTable', 'AliasDatabaseTable');
} else {
    $aliasList = [];
}
?>

<?php $form = ActiveForm::begin(['options' => ['autocomplete' => 'on']]) ?>
<?= $form->field($model, 'screen_tab_devices')->checkboxList($model::$devices, ['class' => 'nt-save-form']); ?>
<?php if (!empty($builder)): ?>
    <div class="extensions-group form-group">
        <div class="row" style="border-top: 1px solid #3275a3; padding-top: 20px;">
            <div>
                <div class="col-sm-2 hidden-xs"><label class="control-label"><b>Action</b></label></div>
                <div class="col-sm-5 hidden-xs"><label class="control-label"><b>Extension Function - Pre</b></label></div>
                <div class="col-sm-5 hidden-xs"><label class="control-label"><b>Extension Function - Post</b></label></div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-2"><label class="control-label"><?= Yii::t('app','ADD') ?></label></div>
            <div class="col-sm-5 col-xs-6">
                <?= Html::hiddenInput('add_pre', json_encode(!empty($model->screen_tab_template->screen_extensions->add->pre) ? $model->screen_tab_template->screen_extensions->add->pre : null), ['class' => 'extensions-config extensions-config-add-pre nt-save-form']) ?>
                <button class="btn btn-default extension-settings-button" data-toggle="modal" data-target="#extensions-modal" type="button" data-func-type="Create" data-extension-method="pre" data-extensions="extensions-config-add-pre">
                    <span class="glyphicon glyphicon-cog"></span> Extensions
                </button>
            </div>
            <div class="col-sm-5 col-xs-6">
                <?= Html::hiddenInput('add_post', json_encode(!empty($model->screen_tab_template->screen_extensions->add->post) ? $model->screen_tab_template->screen_extensions->add->post : null), ['class' => 'extensions-config extensions-config-add-post nt-save-form']) ?>
                <button class="btn btn-default extension-settings-button" data-toggle="modal" data-target="#extensions-modal" type="button" data-func-type="Create" data-extension-method="post" data-extensions="extensions-config-add-post">
                    <span class="glyphicon glyphicon-cog"></span> Extensions
                </button>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-2"><label class="control-label"><?= Yii::t('app','DELETE') ?></label></div>
            <div class="col-sm-5 col-xs-6">
                <?= Html::hiddenInput('delete_pre', json_encode(!empty($model->screen_tab_template->screen_extensions->delete->pre) ? $model->screen_tab_template->screen_extensions->delete->pre : null), ['class' => 'extensions-config extensions-config-delete-pre nt-save-form']) ?>
                <button class="btn btn-default extension-settings-button" data-toggle="modal" data-target="#extensions-modal" type="button" data-func-type="Delete" data-extension-method="pre" data-extensions="extensions-config-delete-pre">
                    <span class="glyphicon glyphicon-cog"></span> Extensions
                </button>
            </div>
            <div class="col-sm-5 col-xs-6">
                <?= Html::hiddenInput('delete_post', json_encode(!empty($model->screen_tab_template->screen_extensions->delete->post) ? $model->screen_tab_template->screen_extensions->delete->post : null), ['class' => 'extensions-config extensions-config-delete-post nt-save-form']) ?>
                <button class="btn btn-default extension-settings-button" data-toggle="modal" data-target="#extensions-modal" type="button" data-func-type="Delete" data-extension-method="post" data-extensions="extensions-config-delete-post">
                    <span class="glyphicon glyphicon-cog"></span> Extensions
                </button>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-2"><label class="control-label"><?= Yii::t('app','EDIT') ?></label></div>
            <div class="col-sm-5 col-xs-6">
                <?= Html::hiddenInput('edit_pre', json_encode(!empty($model->screen_tab_template->screen_extensions->edit->pre) ? $model->screen_tab_template->screen_extensions->edit->pre : null), ['class' => 'extensions-config extensions-config-edit-pre nt-save-form']) ?>
                <button class="btn btn-default extension-settings-button" data-toggle="modal" data-target="#extensions-modal" type="button" data-func-type="Update" data-extension-method="pre" data-extensions="extensions-config-edit-pre">
                    <span class="glyphicon glyphicon-cog"></span> Extensions
                </button>
            </div>
            <div class="col-sm-5 col-xs-6">
                <?= Html::hiddenInput('edit_post', json_encode(!empty($model->screen_tab_template->screen_extensions->edit->post) ? $model->screen_tab_template->screen_extensions->edit->post : null), ['class' => 'extensions-config extensions-config-edit-post nt-save-form']) ?>
                <button class="btn btn-default extension-settings-button" data-toggle="modal" data-target="#extensions-modal" type="button" data-func-type="Update" data-extension-method="post" data-extensions="extensions-config-edit-post">
                    <span class="glyphicon glyphicon-cog"></span> Extensions
                </button>
            </div>
        </div>
        <div class="row" style="border-bottom: 1px solid #3275a3; padding: 0 0 10px 0;">
            <div class="col-sm-2"><label class="control-label"><?= Yii::t('app','INQUIRE')?></label></div>
            <div class="col-sm-5 col-xs-6">
                <?= Html::hiddenInput('inquire_pre', json_encode(!empty($model->screen_tab_template->screen_extensions->inquire->pre) ? $model->screen_tab_template->screen_extensions->inquire->pre : null), ['class' => 'extensions-config extensions-config-inquire-pre nt-save-form']) ?>
                <button class="btn btn-default extension-settings-button" data-toggle="modal" data-target="#extensions-modal" type="button" data-func-type="Search" data-extension-method="pre" data-extensions="extensions-config-inquire-pre">
                    <span class="glyphicon glyphicon-cog"></span> Extensions
                </button>
            </div>
            <div class="col-sm-5 col-xs-6">
                <?= Html::hiddenInput('inquire_post', json_encode(!empty($model->screen_tab_template->screen_extensions->inquire->post) ? $model->screen_tab_template->screen_extensions->inquire->post : null), ['class' => 'extensions-config extensions-config-inquire-post nt-save-form']) ?>
                <button class="btn btn-default extension-settings-button" data-toggle="modal" data-target="#extensions-modal" type="button" data-func-type="Search" data-extension-method="post" data-extensions="extensions-config-inquire-post">
                    <span class="glyphicon glyphicon-cog"></span> Extensions
                </button>
            </div>
        </div>

        <div class="row extension-function-block" style="border-bottom: 1px solid #3275a3; padding: 15px 0;">
            <div class="execute-block">
                <div class="col-sm-2"><label class="control-label"><?= Yii::t('app','EXECUTE')?></label></div>
                <div class="col-sm-3">
                    <?= Html::hiddenInput('execute_pre', json_encode(!empty($model->screen_tab_template->screen_extensions->execute->pre) ? $model->screen_tab_template->screen_extensions->execute->pre : null), ['class' => 'execute-btn extensions-config extensions-config-execute-pre nt-save-form']) ?>
                    <button class="btn btn-default extension-settings-button col-sm" data-toggle="modal" data-target="#extensions-modal" type="button" data-type="extension" data-func-type="GetList" data-extension-method="pre" data-extensions="extensions-config-execute-pre">
                        <span class="glyphicon glyphicon-cog"></span> Extensions
                    </button>
                </div>
                <div class="col-sm-4">
                    <?= Html::hiddenInput('execute_library_input', json_encode(!empty($model->screen_tab_template->screen_extensions->executeFunction->library) ? $model->screen_tab_template->screen_extensions->executeFunction->library : $model->screen_lib), ['class' => 'nt-save-form execute-library-input']) ?>
                    <?= Html::hiddenInput('execute_function_input', json_encode(!empty($model->screen_tab_template->screen_extensions->executeFunction->function) ? $model->screen_tab_template->screen_extensions->executeFunction->function : ''), ['class' => 'nt-save-form execute-function-input']) ?>
                    <?= Html::hiddenInput('execute_custom_input', json_encode(!empty($model->screen_tab_template->screen_extensions->executeFunction->custom) ? $model->screen_tab_template->screen_extensions->executeFunction->custom : ''), ['class' => 'nt-save-form execute-custom-input']) ?>
                    <button class="btn btn-default extension-function-btn" data-toggle="modal" data-target="#execute-function-modal" type="button">
                        <span class="glyphicon glyphicon-cog"></span> Function
                    </button>
                </div>
                <div class="col-sm-3">
                    <?= Html::hiddenInput('execute_post', json_encode(!empty($model->screen_tab_template->screen_extensions->execute->post) ? $model->screen_tab_template->screen_extensions->execute->post : null), ['class' => 'execute-btn extensions-config extensions-config-execute-post nt-save-form']) ?>
                    <button class="btn btn-default extension-settings-button" data-toggle="modal" data-target="#extensions-modal" type="button" data-type="extension" data-func-type="GetList" data-extension-method="post" data-extensions="extensions-config-execute-post">
                        <span class="glyphicon glyphicon-cog"></span> Extensions
                    </button>
                </div>
            </div>
        </div>
    </div>
<?php endif ?>
    <?= $form->field($model, 'screen_name')->dropDownList($groupScreenList, ['prompt' => '-- Select --', 'class' => 'form-control nt-save-form']); ?>

    <div class="form-group">
        <label class="control-label"><?= Yii::t('app','Library')?></label>
        <?php
            if (empty($update)) {
                echo Html::activeDropDownList($model, 'screen_lib', $libraryList, ['prompt' => '-- Select --', 'required' => true, 'class' => 'form-control library-first-step nt-save-form']);
            } else {
                echo Html::activeInput('text', $model, 'screen_lib', ['disabled' => true, 'required' => true, 'class' => 'form-control library-first-step nt-save-form']);
            }
        ?>
    </div>

    <?php if (!empty($builder)): ?>
        <div class="form-group">
            <label>Primary table</label>
            <?= Html::hiddenInput('is_use_alias_framework', !empty($model->screen_tab_template->alias_framework->enable), ['class' => 'is-use-alias-framework']) ?>
            <?= Html::dropDownList('request_primary_table', !empty($model->screen_tab_template->alias_framework->request_primary_table) ? $model->screen_tab_template->alias_framework->request_primary_table : [], $aliasList, ['class' => 'form-control primary-table', 'required' => true]) ?>
        </div>
        <div class="row alias-framework-functions hidden">
            <div class="col-sm-4">
                <div class="form-group">
                    <label><?= Yii::t('app','Function name for update')?></label>
                    <?= Html::dropDownList('alias_framework_func_update', null, [], [
                        'prompt' => '',
                        'class' => 'form-control alias-framework-func-update',
                        'required' => true,
                        'disabled' => empty($model->screen_tab_template->alias_framework->data_source_update) || empty($model->screen_tab_template->alias_framework->enable),
                        'data' => [
                            'value' => !empty($model->screen_tab_template->alias_framework->data_source_update) ? $model->screen_tab_template->alias_framework->data_source_update : null
                        ]
                    ]); ?>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    <label><?= Yii::t('app','Function name for delete') ?></label>
                    <?= Html::dropDownList('alias_framework_func_delete', null, [], [
                        'prompt' => '',
                        'class' => 'form-control alias-framework-func-delete',
                        'required' => true,
                        'disabled' => empty($model->screen_tab_template->alias_framework->data_source_delete) || empty($model->screen_tab_template->alias_framework->enable),
                        'data' => [
                            'value' => !empty($model->screen_tab_template->alias_framework->data_source_delete) ? $model->screen_tab_template->alias_framework->data_source_delete : null
                        ]
                    ]); ?>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    <label><?= Yii::t('app','Function name for insert') ?></label>
                    <?= Html::dropDownList('alias_framework_func_insert', null, [], [
                        'prompt' => '',
                        'class' => 'form-control alias-framework-func-insert',
                        'required' => true,
                        'disabled' => empty($model->screen_tab_template->alias_framework->data_source_insert) || empty($model->screen_tab_template->alias_framework->enable),
                        'data' => [
                            'value' => !empty($model->screen_tab_template->alias_framework->data_source_insert) ? $model->screen_tab_template->alias_framework->data_source_insert : null
                        ]
                    ]); ?>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-6">
                <div class="form-group field-tabform-functions">
                    <?= Html::label(Yii::t('app', 'Simple search')) ?>
                    <div class="input-group">
                        <span class="input-group-addon">
                            <input name="search-configuration-radio" class="search-configuration-radio" value="<?= ScreenForm::SIMPLE_SEARCH_TYPE ?>" type="radio" title="Enable search type">
                        </span>
                        <span class="input-group-btn">
                            <button class="btn btn-default library-first-step-settings" data-toggle="modal" data-target="#setting-library-modal" type="button" disabled>
                                <span class="glyphicon glyphicon-cog"></span>
                            </button>
                        </span>
                        <?= Html::dropDownList('search-function-name', null, [], [
                            'class' => 'form-control search-function-name nt-save-form',
                            'aria-required' => "true",
                            'disabled' => true
                        ]) ?>
                    </div>
                    <?= Html::hiddenInput(
                            'search_function_config',
                            json_encode(!empty($model->screen_tab_template->search_configuration) ? $model->screen_tab_template->search_configuration : null),
                            ['class' => 'search-function-config nt-save-form']
                    ) ?>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group search-block-wrapper">
                    <?= Html::label(Yii::t('app', 'Multi-search with custom query')) ?>
                    <div class="input-group">
                        <span class="input-group-addon">
                            <input name="search-configuration-radio" class="search-configuration-radio" value="<?= ScreenForm::CUSTOM_SEARCH_TYPE ?>" type="radio" title="Enable search type">
                        </span>
                        <span class="input-group-btn">
                            <button class="btn btn-default search-configuration-button" data-toggle="modal" data-target="#search-configuration-modal" type="button" disabled>
                                <span class="glyphicon glyphicon-cog"></span>
                            </button>
                        </span>
                        <?= Html::dropDownList(
                            'search_block_select',
                            !empty($model->screen_tab_template->search_custom_query->query_pk) ? $model->screen_tab_template->search_custom_query->query_pk : null,
                            $customQueryList,
                            [
                                'prompt' => '-- Select --',
                                'id' => 'search-block-select',
                                'class' => 'form-control nt-save-form',
                                'aria-required' => "true",
                                'disabled' => true
                            ]
                        ) ?>
                    </div>
                </div>
            </div>
        </div>
        <?= Html::hiddenInput('search_custom_query', json_encode(!empty($model->screen_tab_template->search_custom_query) ? $model->screen_tab_template->search_custom_query : NULL), ['class' => 'search-custom-query nt-save-form']) ?>
    <?php endif ?>

    <?= $form->field($model, 'screen_tab_text')->input('text', ['class' => 'form-control nt-save-form']); ?>
    <?= $form->field($model, 'screen_desc')->textarea(['class' => 'form-control nt-save-form']); ?>
    <?= $form->field($model, 'screen_tab_weight')->input('number', ['class' => 'form-control nt-save-form', 'min' => 0, 'required' => true]); ?>

    <?php if (!empty($builder)): ?>
        <div class="form-group">
            <label for="is_step_screen"><?= Yii::t('app','Is step screen')?></label>
            <div class="input-group">
                <span class="input-group-addon">
                    <input <?= (!empty($model->screen_tab_template->step_screen->enable)) ? 'checked' : '' ?> type="checkbox" name="is_step_screen" value="1" onchange="$('#iconpicker_screen-step-icon_jspicker').prop('disabled', !$(this).prop('checked'))" />
                </span>
                <?= Iconpicker::widget([
                    'id' => 'screen-step-icon',
                    'name' => 'screen_step_icon',
                    'containerOptions' => [
                        'class' => 'input-group-btn',
                        'style' => ['width' => '100%']
                    ],
                    'pickerOptions' => [
                        'class' => 'btn btn-default',
                        'disabled' => empty($model->screen_tab_template->step_screen->enable)
                    ],
                    'value' => !empty($model->screen_tab_template->step_screen->icon) ? $model->screen_tab_template->step_screen->icon : null
                ]) ?>
            </div>
        </div>
    <?php endif ?>

    <?php if (empty($builder)): ?>
        <?php
            $model->screen_tab_template = json_encode($model->screen_tab_template);
            echo $form->field($model, 'screen_tab_template')->widget(
                JsonEditor::class, [
                    'clientOptions' => ['mode' => 'code'],
                    'collapseAll' => ['view'],
                    'name' => 'editor',
                    'options' => ['class' => 'form-control', 'id' => 'template-textarea-update'],
                    'value' => $model->screen_tab_template
                ]
            );
        ?>
    <?php else: ?>
        <div class="form-group">
            <?php $layoutType = !empty($model->screen_tab_template->layout_type) ? $model->screen_tab_template->layout_type : null; ?>
            <?= Html::radioList('screen_tab_type', $layoutType, $model::$typeLabels,
                ['item' => function ($index, $label, $name, $checked, $value) use ($layoutType, $update) {
                    $dependentTypes = (!empty(ScreenForm::$dependentTypes[$layoutType])) ? ScreenForm::$dependentTypes[$layoutType] : [];
                    return '<label style="padding-right: 38px">' . $this->render('templates/type_' . $value) .
                        Html::radio($name, $checked, [
                            'class' => 'nt-save-form',
                            'required' => true,
                            'value' => $value,
                            'disabled' => !empty($update) && !in_array($value, $dependentTypes) && !$checked
                        ]) . ' ' . $label .
                        '</label>';
                }]
            ); ?>
            <div class="clearfix"></div>
        </div>
    <?php endif ?>

    <div class="button-block">
        <?= Html::a(Yii::t('app', 'Back'), Url::toRoute('index'), ['class' => 'btn btn-link']); ?>
        <?php if (empty($builder)): ?>
            <?= Html::submitButton(Yii::t('app', 'Submit'), ['class' => 'btn btn-primary']); ?>
        <?php else: ?>
            <?= Html::submitButton(Yii::t('app', 'Next'), ['class' => 'btn btn-primary next-step-btn']); ?>
        <?php endif ?>
    </div>
<?php ActiveForm::end() ?>

<?php if (!empty($builder)): ?>
    <?php if (empty($update) && !empty(Yii::$app->request->get()['return'])): ?>
        <script>screenCreator.ntSaveForms();</script>
    <?php elseif (empty($update)): ?>
        <script>screenCreator.ntClearForms();</script>
        <script>screenCreator.ntSaveForms();</script>
    <?php else: ?>
        <script>screenCreator.ntClearForms();</script>
    <?php endif; ?>
<?php endif; ?>

<?php
if (!empty($builder)) {
    echo $this->render('common/library-modal', ['model' => $model]);
    echo $this->render('common/extensions-modal');
    echo $this->render('common/execute-function-modal', ['model' => $model]);
    echo $this->render('common/custom-query-modal');
}
?>
