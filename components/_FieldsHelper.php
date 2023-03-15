<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 */

namespace app\components;

use Yii;
use app\models\CommandData;
use app\models\DocumentGroup;
use app\models\FileModel;
use app\models\GetListList;
use kartik\date\DatePicker;
use kartik\datetime\DateTimePicker;
use kartik\typeahead\Typeahead;
use kato\DropZone;
use yii\base\Widget;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\JsExpression;

/**
 * Class _FieldsHelper
 * @package app\components
 * @property _FormattedHelper $Formatted
 */
class _FieldsHelper extends Widget
{
    const TYPE_SELECT = 'List';
    const TYPE_MULTI_SELECT = 'Multi-select list';
    const TYPE_CHECKBOX = 'Checkbox';
    const TYPE_NUMERIC = 'Numeric';
    const TYPE_TEXT = 'Text';
    const TYPE_TEXTAREA = 'Textarea';
    const TYPE_RADIO = 'Radio';
    const TYPE_DOCUMENT = 'Document';
    const TYPE_HIDDEN = 'Hidden';
    const TYPE_INLINE_SEARCH = 'Inline search';
    const TYPE_ALERT = 'Alert';
    const TYPE_LINK = 'Link';
    const TYPE_LABEL = 'Label';
    const TYPE_BUTTON = 'Button';
    const TYPE_DATALIST = 'Datalist';

    const TYPE_FORMAT_LINK_LOCAL = 'Local';

    const TYPE_ALERT_SUCCESS = 'Success';
    const TYPE_ALERT_WARNING = 'Warning';
    const TYPE_ALERT_ERROR = 'Error';

    const MULTI_SELECT_DELIMITER = ';';

    const PROPERTY_READ_ONLY = 'R';
    const PROPERTY_TRUE = 'Y';
    const PROPERTY_FALSE = 'N';

    const FIELD_WIDTH_TYPE_LENGTH = 'L';
    const FIELD_WIDTH_TYPE_VALUE = 'V';

    public $dataField;
    public $dataAccess;
    public $mode;
    public $libName;
    public $value;
    public $config;
    public $dataId;
    public $isGridField = false;
    public $isKeyField = false;
    public $aliasFrameworkPKParts = false;

    public $data_source_get;
    public $data_source_update;
    public $data_source_delete;
    public $data_source_create;

    protected $Formatted;
    protected $configDefault = [
        'field_type' => self::TYPE_TEXT,
        'copyable_field' => self::PROPERTY_FALSE,
        'format_type' => _FormattedHelper::TEXT_FORMAT,
        'field_length' => false,
        'list_name' => false,
        'key_field' => false,
        'field_link_menu' => false,
        'field_group_screen_link' => false,
        'field_screen_link' => false,
        'field_settings_link' => false,
        'type-link' => false,
        'edit_type' => false
    ];

    public function run()
    {
        $this->Formatted = new _FormattedHelper();
        if (!empty($this->config['param_type'])) {
            $this->config['field_type'] = $this->config['param_type'];
        }

        $this->isKeyField = isset($this->config['key_field']) && ($this->config['key_field'] == self::PROPERTY_TRUE);
        $this->config = array_merge($this->configDefault, $this->config);

        $dataAccess = $this->accessFilter();
        switch($dataAccess) {
           case BaseRenderWidget::FIELD_ACCESS_NONE:
                return Html::tag('i', '(access denied)', ['style' => ['color' => 'red']]);
                break;
            case BaseRenderWidget::FIELD_ACCESS_READ:
                return $this->getFormattedData();
                break;
            default:
                return $this->isField() ? $this->getField() : $this->getFormattedData();
        }
    }

    private function accessFilter() {
        $accessRights = (!empty($this->dataAccess[$this->dataField])) ? $this->dataAccess[$this->dataField] : BaseRenderWidget::FIELD_ACCESS_FULL;
        if (empty(Yii::$app->getUser()->getIdentity()->group_area)) {
            return $accessRights;
        }

        $groupAreas = explode(';', Yii::$app->getUser()->getIdentity()->group_area);
        if (!empty($this->config['access_view'])) {
            $accessRights = BaseRenderWidget::FIELD_ACCESS_NONE;
            foreach($groupAreas as $group) {
                if (in_array($group, $this->config['access_view'])) {
                    $accessRights = BaseRenderWidget::FIELD_ACCESS_READ;
                    break;
                }
            }
        }

        $isGetFieldMode = in_array($this->mode, [RenderTabHelper::MODE_EDIT, RenderTabHelper::MODE_INSERT, RenderTabHelper::MODE_COPY]);
        $currentAccessCanUpdate = $accessRights != BaseRenderWidget::FIELD_ACCESS_NONE;

        if (!empty($this->config['access_update']) && $isGetFieldMode && $currentAccessCanUpdate) {
            $accessRights = BaseRenderWidget::FIELD_ACCESS_READ;
            foreach($groupAreas as $group) {
                if (in_array($group, $this->config['access_update'])) {
                    $accessRights = BaseRenderWidget::FIELD_ACCESS_FULL;
                    break;
                }
            }
        }

        return $accessRights;
    }

    /**
     * @return bool
     */
    private function isField()
    {
        $basicRule = in_array($this->mode, [RenderTabHelper::MODE_EDIT, RenderTabHelper::MODE_INSERT, RenderTabHelper::MODE_COPY]);
        if (!$basicRule && isset($this->config['always_show_field_border']) && $this->config['always_show_field_border'] == 'Y') {
            $this->config['edit_type'] = self::PROPERTY_READ_ONLY;
            return true;
        }

        return $basicRule || ($this->isKeyField && $this->config['field_type'] !== self::TYPE_DOCUMENT);
    }

    /**
     * @return bool
     */
    private function isDisabledField()
    {
        $isReadOnlyFieldConfig = $this->config['edit_type'] == self::PROPERTY_READ_ONLY;
        $isKeyField = $this->config['key_field'] == self::PROPERTY_TRUE;

        $isInsertMode = $this->mode !== RenderTabHelper::MODE_INSERT;
        $isEditMode = $this->mode === RenderTabHelper::MODE_EDIT;

        return (!$isKeyField && $isReadOnlyFieldConfig && $isInsertMode) || ($isKeyField && $isEditMode);
    }

    /**
     * @return string
     */
    private function getFieldName()
    {
        return $this->dataField;
    }

    private function getFieldFuncData()
    {
        $data = [];
        if ($this->data_source_get) {
            $data['get-func'] = $this->data_source_get;
        }

        if ($this->data_source_update) {
            $data['update-func'] = $this->data_source_update;
        }

        if ($this->data_source_delete) {
            $data['delete-func'] = $this->data_source_delete;
        }

        if ($this->data_source_create) {
            $data['create-func'] = $this->data_source_create;
        }

        if ($this->dataId) {
            $data['sub-id'] = $this->dataId;
        }

        if ($this->aliasFrameworkPKParts) {
            $data['af-pk-part'] = $this->aliasFrameworkPKParts;
        }

        return ['data' => $data];
    }

    private function getFieldID()
    {
        $filteredDataSource = str_replace([':', ',', '.'], '-', $this->data_source_get);
        $dataField = str_replace([':', ',', '.'], '-', $this->getFieldName());

        return $filteredDataSource . '--' . $dataField;
    }

    /**
     * @return array
     */
    private function getBasicStyles()
    {
        $option = ['style' => ['max-width' => '100%']];

        $textDecoration = '';
        $textDecoration .= (!empty($this->config['field_strike'])) ? 'line-through' : '';
        $textDecoration .= (!empty($this->config['field_underline'])) ? ' underline' : '';

        if (!empty($textDecoration)) {
            Html::addCssStyle($option, ['text-decoration' => $textDecoration]);
        }
        if (!empty($this->config['field_bold'])) {
            Html::addCssStyle($option, ['font-weight' => 'bold']);
        }
        if (!empty($this->config['field_italic'])) {
            Html::addCssStyle($option, ['font-style' => 'italic']);
        }

        if (!empty($this->config['field_text_color'])) {
            Html::addCssStyle($option, ['color' => $this->config['field_text_color']]);
        }
        if (!empty($this->config['field_bg_color'])) {
            Html::addCssStyle($option, ['background-color' => $this->config['field_bg_color']]);
        }

        if (!empty($this->config['field_font_family'])) {
            Html::addCssStyle($option, ['font-family' => $this->config['field_font_family']]);
        }
        if (!empty($this->config['field_font_size'])) {
            Html::addCssStyle($option, ['font-size' => $this->config['field_font_size'] . 'px']);
        }

        Html::addCssStyle($option, ['display' => 'inline-block', 'vertical-align' => 'top']);

        return $option;
    }

    /**
     * @return array
     */
    private function getFieldOptions()
    {
        $options = $this->getBasicStyles();
        $options += [
            'class' => 'form-control',
            'readonly' => $this->isDisabledField(),
            'maxlength' => isset($this->config['field_length']) ? $this->config['field_length'] : null,
            'id' => (is_null($this->dataId)) ? $this->getFieldID() : null
        ];

        $options['class'] .= ($this->isGridField) ? ' form-control-grid' : '';
        $options['class'] .= (isset($this->config['key_field']) && ($this->config['key_field'] == self::PROPERTY_TRUE)) ? ' form-control-key' : '';

        $options = array_merge($options, $this->getFieldFuncData());

        if (!empty($this->config['field_width_type'])) {
            if ($this->config['field_width_type'] === self::FIELD_WIDTH_TYPE_LENGTH) {
                $options['size'] = $this->config['field_length'];
                Html::addCssStyle($options, ['width' => 'auto']);
            } elseif ($this->config['field_width_type'] === self::FIELD_WIDTH_TYPE_VALUE && !empty($this->config['field_width_value'])) {
                Html::addCssStyle($options, ['width' => $this->config['field_width_value']]);
            }
        }

        return $options;
    }

    /**
     * @param array $options
     * @param string $type
     *
     * @return string
     */
    private function getSelectField(array $options, $type)
    {
        if (empty($this->config['num_rows'])) {
            if ($type === self::TYPE_MULTI_SELECT) {
                $options['size'] = 3;
            } else {
                $options['size'] = 1;
            }
        } else {
            $options['size'] = $this->config['num_rows'];
        }

        $options['multiple'] = $type === self::TYPE_MULTI_SELECT;

        $items = $this->getOptionsList($this->config['list_name']);
        $value = ($options['multiple']) ? explode(self::MULTI_SELECT_DELIMITER, $this->value) : $this->value;

        if (!empty($this->config['apply_input_mask']) && $this->config['apply_input_mask'] == self::PROPERTY_TRUE) {
            Html::addCssClass($options, 'apply-input-mask');
        }

        return Html::dropDownList($this->getFieldName(), $value, $items, $options);
    }

    /**
     * @param array $options
     *
     * @return string
     */
    private function getCheckboxField(array $options)
    {
        if (!empty($this->config['field_group'])) {
            $options['data']['field-group'] = $this->config['field_group'];
        }

        $value = (int)!empty($this->value);
        $options['checked'] = (boolean)$value;
        Html::removeCssClass($options, 'form-control');

        return Html::input('checkbox', $this->getFieldName(), $value, $options);
    }

    /**
     * @param array $options
     *
     * @return string
     */
    private function getRadioField(array $options)
    {
        if (!empty($this->config['field_group'])) {
            $options['data']['field-group'] = $this->config['field_group'];
        }

        $value = (int)!empty($this->value);
        $options['checked'] = (boolean)$value;
        Html::removeCssClass($options, 'form-control');

        return Html::input('radio', $this->getFieldName(), $value, $options);
    }

    /**
     * @param array $options
     *
     * @return string
     * @throws \Exception
     */
    private function getDateTimePicker(array $options)
    {
        $options['data']['save-format'] = _FormattedHelper::getDefaultDateTimeFormat();

        if (empty($this->value) && (is_null($this->dataId) || $this->dataId == -1)) {
            $this->value = date("Y-m-d H:i:s");
        }
        $this->value = $this->Formatted->run($this->value, _FormattedHelper::DATE_TIME_TEXT_FORMAT);

        if ($this->config['edit_type'] == self::PROPERTY_READ_ONLY) {
            return $this->getTextField($options, 'text');
        }

        return DateTimePicker::widget([
            'name' => $this->getFieldName(),
            'id' => 'w-' . str_replace(".", "", microtime(true)),
            'type' => DateTimePicker::TYPE_COMPONENT_PREPEND,
            'value' => $this->value,
            'removeButton' => false,
            'options' => $options,
            'pluginOptions' => [
                'autoclose' => true,
                'format' => $this->Formatted->getFormatDateTimeForPicker()
            ]
        ]);
    }

    /**
     * @param array $options
     *
     * @return string
     * @throws \Exception
     */
    private function getDatePicker(array $options)
    {
        $options['data']['save-format'] = _FormattedHelper::getDefaultDateFormat();

        if (empty($this->value) && (is_null($this->dataId) || $this->dataId == -1)) {
            $this->value = date("Y-m-d", time() - 86400);
        }
        $this->value = $this->Formatted->run($this->value, _FormattedHelper::DATE_TEXT_FORMAT);

        if ($this->config['edit_type'] == self::PROPERTY_READ_ONLY) {
            return $this->getTextField($options, 'text');
        }

        return DatePicker::widget([
            'name' => $this->getFieldName(),
            'id' => 'w-' . str_replace(".", "", microtime(true)),
            'type' => DatePicker::TYPE_COMPONENT_PREPEND,
            'value' => $this->value,
            'removeButton' => false,
            'options' => $options,
            'pluginOptions' => [
                'autoclose' => true,
                'format' => $this->Formatted->getFormatDateForPicker()
            ]
        ]);
    }

    /**
     * @param array $options
     *
     * @return string
     */
    private function getCurrencyField(array $options)
    {
        Html::addCssClass($options, 'currency-input');
        $options['required'] = true;

        if (!empty($options['maxlength'])) {
            $str = str_repeat('1', $options['maxlength']);
            $newLength = strlen($this->Formatted->run($str,
                    _FormattedHelper::CURRENCY_NUMERIC_FORMAT)) - strlen($this->Formatted->getCurrencySuffix()) - 4;

            $options['maxlength'] = $newLength;
            if (!empty($options['size'])) {
                $options['size'] = $newLength;
            }
        }

        if (!empty($this->config['apply_input_mask']) && $this->config['apply_input_mask'] == self::PROPERTY_TRUE) {
            Html::addCssClass($options, 'apply-input-mask');
        }

        $suffix = Html::tag('div', $this->Formatted->getCurrencySuffix(), ['class' => 'input-group-addon']);
        $input = Html::input('text', $this->getFieldName(), $this->value, $options);

        return Html::tag('div', $suffix . $input, ['class' => 'input-group']);
    }

    /**
     * @param array $options
     * @param string $formatType
     *
     * @return string
     */
    private function getTextField(array $options, $formatType = null)
    {
        switch ($formatType) {
            case _FormattedHelper::NUMERIC_FORMAT:
                $inputType = 'number';
                $options['required'] = true;
                break;
            case _FormattedHelper::EMAIL_TEXT_FORMAT:
                $inputType = 'email';
                break;
            default:
                $inputType = 'text';
        }

        if (!empty($this->config['apply_input_mask']) && $this->config['apply_input_mask'] == self::PROPERTY_TRUE) {
            Html::addCssClass($options, 'apply-input-mask');
        }

        return Html::input($inputType, $this->getFieldName(), $this->value, $options);
    }

    private function getTextAreaField(array $options)
    {
        $options['rows'] = !empty($this->config['num_rows']) ? $this->config['num_rows'] : 2;
        return Html::textarea($this->getFieldName(), $this->value, $options);
    }

    private function getHiddenField(array $options)
    {
        ArrayHelper::remove($options, 'style');
        return Html::hiddenInput($this->getFieldName(), $this->value, $options);
    }

    private function getDocumentField(array $options)
    {
        if (!empty($options['disabled'])) {
            return Html::tag('div', 'Read only field', ['style' => 'color: red; padding: 7px 0 0 0;']);
        }

        if (empty($this->config['field_document_family']) || empty($this->config['field_document_category'])) {
            return Html::tag('div', 'Access denied', ['style' => 'color: red; padding: 7px 0 0 0;']);
        }

        $accessRight = DocumentGroup::getAccessPermission($this->config['field_document_family'], $this->config['field_document_category']);
        if ($accessRight != DocumentGroup::ACCESS_RIGHT_FULL) {
            return Html::tag('div', 'Access denied', ['style' => 'color: red; padding: 7px 0 0 0;']);
        }

        $containerId = 'drop-zone-' . str_replace(".", "", microtime(true));

        $this->getView()->registerCss("#$containerId {{$options['style']}}");
        $dropZone = DropZone::widget([
            'dropzoneContainer' => $containerId,
            'uploadUrl' => Url::to(['/file/upload'], true),
            'options' => [
                'paramName' => "file",
                'maxFilesize' => '20',
                'uploadMultiple' => false,
                'maxFiles' => 1,
                'acceptedFiles' => '.xlsx,.xls,.doc, .docx,.ppt, .pptx, text/plain, application/pdf, image/*',
                'previewTemplate' => '
                    <div class="dz-preview dz-file-preview">
                        <div class="dz-details">
                            <div class="dz-remove" data-dz-remove><span class="glyphicon glyphicon-remove"></span></div>
                            <div class="dz-size"><span data-dz-size></span></div>
                            <div class="dz-filename"><span data-dz-name></span></div>
                        </div>
                        <div class="dz-progress"><span class="dz-upload" data-dz-uploadprogress></span></div>
                        <div class="dz-success-mark"><span class="glyphicon glyphicon-ok"></span></div>
                        <div class="dz-error-mark"><span class="glyphicon glyphicon-exclamation-sign"></span></div>
                        <div class="dz-error-message"><span data-dz-errormessage></span></div>
                    </div>
                '
            ],
            'clientEvents' => [
                'addedfile' => "function(file, xhr) {
                    $(file.previewElement).parents('.dropzone').find('.dz-message').hide();
                }",
                'success' => "function(file, xhr) {
                    var me = $(file.previewElement),
                        message = me.parents('.dropzone').find('.dz-message'),
                        uploadArrowButton = $(file.previewElement).parents('.dropzone').next('.upload-arrow-button');
                        
                    me.find('.dz-remove').click(function () {
                        message.show();
                        uploadArrowButton.show();
                        uploadArrowButton.prop('disabled', true).removeClass('is-active').removeClass('is-completed');
                        uploadArrowButton.attr('data-file-name', '');
                        uploadArrowButton.attr('data-original-file-name', '');
                        
                        uploadArrowButton.parent().find('input[type=\"hidden\"]').val('');
                    });
                    
                    uploadArrowButton.prop('disabled', false);
                    uploadArrowButton.attr('data-file-name', xhr.file_name);
                    uploadArrowButton.attr('data-original-file-name', xhr.original_file_name);
                }",
                'removedfile' => "function (file) {
                    var response = JSON.parse(file.xhr.response);
                    $.post('" . Url::to(['/file/delete'], true) . "', {file_name: response.file_name}).done(function(data) {
                        console.log('File \"' + response.file_name + '\" has been deleted');
                    });
                }"
            ],
        ]);

        $uploadButton = Html::button('<span class="glyphicon glyphicon-arrow-up"></span>', [
            'class' => 'btn btn-default upload-arrow-button',
            'data-family' => (!empty($this->config['field_document_family'])) ? $this->config['field_document_family'] : null,
            'data-category' => (!empty($this->config['field_document_category'])) ? $this->config['field_document_category'] : null,
            'disabled' => true
        ]);
        $hiddenInput = Html::hiddenInput($this->getFieldName(), $this->value, ['id' => $this->getFieldID(), 'class' => 'form-control', 'data' => $options['data']]);

        return Html::tag('div', $dropZone . $uploadButton . $hiddenInput, ['class' => 'upload-setting-block']);
    }

    private function getAlertField(array $options) {
        $button = Html::button('Edit Alert', [
            'class' => 'btn btn-default alert-field-btn',
            'data-target'=>'#alert-message-edit-modal',
            'data-toggle'=>'modal',
            'data-alert'=>$this->getFieldID(),
            'data-sub-id-btn' => (!is_null($this->dataId)) ? $this->dataId : null,
        ]);
        $input = Html::hiddenInput($this->getFieldName(), $this->value, [
            'id' => $this->getFieldID(),
            'class' => 'form-control form-control-grid sub-id',
            'data' => $options['data'],
        ]);

        return Html::tag('div', $button . $input, ['class'=>'alert-block']);
    }

    private function getInlineSearchField($options)
    {
        if (empty($this->config['custom_query_pk']) || empty($this->config['custom_query_param'])) {
            return $this->getTextField($options);
        }

        if (!empty($this->config['apply_input_mask']) && $this->config['apply_input_mask'] == self::PROPERTY_TRUE) {
            Html::addCssClass($options, 'apply-input-mask');
        }

        $id = str_replace(".", "", microtime(true));
        return Typeahead::widget([
            'id' => "inline-search-$id",
            'name' => $this->getFieldName(),
            'value' => $this->value,
            'options' => $options,
            'pluginOptions' => ['highlight' => true],
            'dataset' => [
                [
                    'datumTokenizer' => "Bloodhound.tokenizers.obj.whitespace('value')",
                    'display' => $this->config['custom_query_param'],
                    'limit' => CommandData::SEARCH_LIMIT,
                    'remote' => ['url' => '#'],
                    'source' => new JsExpression("
                        function (query, syncResults, asyncResults) {
                            setTimeout(function()  {
                                common.inlineSearchResults('{$this->config['custom_query_pk']}', [{name: '{$this->config['custom_query_param']}', value: query}], asyncResults);
                            }, 1000);
                        }
                    "),
                    'templates' => [
                        'notFound' => Html::tag('div', 'No search result', ['class' => 'text-danger', 'style' => ['padding' => '0 8px']])
                    ]
                ]
            ]
        ]);
    }

    private function getDatalistField($options)
    {
        $id = str_replace(".", "", microtime(true));
        return Typeahead::widget([
            'id' => "datalist-$id",
            'name' => $this->getFieldName(),
            'value' => $this->value,
            'options' => $options,
            'pluginOptions' => ['highlight' => true],
            'dataset' => [
                [
                    'local' => isset($this->config['dropdown_values']) ? explode(self::MULTI_SELECT_DELIMITER, $this->config['dropdown_values']) : [],
                    'limit' => 10
                ]
            ]
        ]);
    }

    private function getButtonField($options) {
        Html::addCssStyle($options, ['width' => '100%']);
        Html::addCssClass($options, 'btn btn-default screen-btn-custom-action');
        Html::removeCssClass($options, 'form-control');

        if (empty($this->config['identifier']) && $this->isGridField) {
            $this->config['identifier'] = 'grid-button-' . str_replace(".", "", microtime(true));
        }

        $options['id'] = $this->config['identifier'];

        if (in_array($this->mode, [RenderTabHelper::MODE_EDIT, RenderTabHelper::MODE_INSERT, RenderTabHelper::MODE_COPY]) && $this->mode == $this->config['button_action']) {
            Html::removeCssClass($options, 'screen-btn-custom-action');

            $buttons[] = Html::button(\yii\bootstrap\Html::icon('floppy-disk'), ['class' => 'btn btn-success', 'id' => "{$this->config['identifier']}_{$this->config['button_action']}_save"]);
            $buttons[] = Html::button(\yii\bootstrap\Html::icon('remove-circle'), ['class' => 'btn btn-danger', 'id' => "{$this->config['identifier']}_{$this->config['button_action']}_cancel"]);

            $this->view->registerJs("$('#{$this->config['identifier']}_{$this->config['button_action']}_save').on('click', function () {common.triggerSpecialAction('{$this->config['button_action']}', 'save')});");
            $this->view->registerJs("$('#{$this->config['identifier']}_{$this->config['button_action']}_cancel').on('click', function () {common.triggerSpecialAction('{$this->config['button_action']}', 'cancel')});");

            return Html::tag('div', implode('', $buttons), ['class' => 'btn-group']);
        }

        if ($this->config['button_action']) {
            if ($this->config['button_action'] == 'execute' && !empty($this->config['execute_function_custom'])) {
                if ($customFunctions = Json::decode($this->config['execute_function_custom'])) {
                    $customFunctions = explode(';', $customFunctions);
                    if (count($customFunctions) > 1) {
                        $preFunction = !empty($this->config['execute_function_pre']) ? $this->config['execute_function_pre'] : '{}';
                        $postFunction = !empty($this->config['execute_function_post']) ? $this->config['execute_function_post'] : '{}';
                        $subId = ($this->isGridField) ? "{id: '$this->dataId'}" : "null";

                        $this->view->registerJs("
                            $('#{$this->config['identifier']}').on('click', function () {
                                common.triggerExecute({$this->config['execute_function_get']}, ['$customFunctions[0]', '$customFunctions[1]'], $preFunction, $postFunction, $subId);
                            });
                        ");
                    }
                }
            } else {
                $this->view->registerJs("$('#{$this->config['identifier']}').on('click', function () {common.triggerAction('{$this->config['button_action']}')});");
            }
        }

        return Html::button($this->config['value'], $options);
    }

    /**
     * @return string - HTML code of field
     * @throws \Exception
     */
    private function getField()
    {
        $options = $this->getFieldOptions();
        if ($this->mode === RenderTabHelper::MODE_COPY &&
            $this->config['copyable_field'] === self::PROPERTY_FALSE &&
            !in_array($this->config['field_type'], [
                RenderTabHelper::SECTION_TYPE_CHART_BAR_HORIZONTAL,
                RenderTabHelper::SECTION_TYPE_CHART_BAR_VERTICAL,
                RenderTabHelper::SECTION_TYPE_CHART_DOUGHNUT,
                RenderTabHelper::SECTION_TYPE_CHART_LINE,
                RenderTabHelper::SECTION_TYPE_CHART_PIE
            ])
        ) {
            $this->value = '';
        }
        if ($this->config['field_type'] == self::TYPE_MULTI_SELECT || $this->config['field_type'] == self::TYPE_SELECT) {
            return $this->getSelectField($options, $this->config['field_type']);
        } elseif ($this->config['field_type'] == self::TYPE_CHECKBOX) {
            return $this->getCheckboxField($options);
        } elseif ($this->config['field_type'] == self::TYPE_RADIO) {
            return $this->getRadioField($options);
        } elseif ($this->config['field_type'] == self::TYPE_NUMERIC && $this->config['format_type'] == _FormattedHelper::CURRENCY_NUMERIC_FORMAT) {
            return $this->getCurrencyField($options);
        } elseif ($this->config['field_type'] == self::TYPE_TEXT && $this->config['format_type'] == _FormattedHelper::DATE_TEXT_FORMAT) {
            return $this->getDatePicker($options);
        } elseif ($this->config['field_type'] == self::TYPE_TEXT && $this->config['format_type'] == _FormattedHelper::DATE_TIME_TEXT_FORMAT) {
            return $this->getDateTimePicker($options);
        } elseif ($this->config['field_type'] == self::TYPE_HIDDEN) {
            return $this->getHiddenField($options);
        } elseif ($this->config['field_type'] == self::TYPE_DOCUMENT) {
            return $this->getDocumentField($options);
        } elseif ($this->config['field_type'] == self::TYPE_INLINE_SEARCH) {
            return $this->getInlineSearchField($options);
        } elseif ($this->config['field_type'] == self::TYPE_DATALIST) {
            return $this->getDatalistField($options);
        } elseif ($this->config['field_type'] == self::TYPE_ALERT) {
            return $this->getAlertField($options);
        } elseif ($this->config['field_type'] == self::TYPE_BUTTON) {
            return $this->getButtonField($options);
        } elseif ($this->config['field_type'] == self::TYPE_TEXTAREA) {
            return $this->getTextAreaField($options);
        } else {
            return $this->getTextField($options, $this->config['format_type']);
        }
    }

    private function getFileData()
    {
        if (empty($this->config['is_viewer'])) {
            if (empty($this->config['field_document_family']) || empty($this->config['field_document_category'])) {
                return Html::tag('div', 'Access denied', ['style' => 'color: red; padding: 7px 0 0 0;']);
            }

            $accessRight = DocumentGroup::getAccessPermission($this->config['field_document_family'], $this->config['field_document_category']);
            if ($accessRight != DocumentGroup::ACCESS_RIGHT_FULL && $accessRight != DocumentGroup::ACCESS_RIGHT_READ) {
                return Html::tag('div', 'Access denied', ['style' => 'color: red; padding: 7px 0 0 0;']);
            }
        }

        $link = null;
        $options = ['class' => 'btn btn-default download-file-link'];

        if ($fileContainer = FileModel::getFileContainer($this->value)) {
            $options['data-pk'] = $fileContainer['id'];
            if (!empty($this->config['related_frame_class'])) {
                $options['data-related-frame-class'] = $this->config['related_frame_class'];
            }

            $fileHashHex = bin2hex(base64_decode($fileContainer['original_file_hash']));
            $fileInfo = pathinfo($fileContainer['original_file_name']);
            $fileRoot = DIRECTORY_SEPARATOR . $fileHashHex . '.' . $fileInfo['extension'];
            $filePath = FileModel::getDirectory('@webroot') . $fileRoot;

            if (file_exists($filePath)) {
                $link = FileModel::getDirectory('@web') . $fileRoot;
                $options['download'] = $fileContainer['original_file_name'];
                Html::addCssClass($options, 'is-cached');
            }
        } else {
            return Html::tag('div', 'Can\'t find file container', ['style' => 'color: red; padding: 7px 0 0 0;']);
        }

        $btnText = ($link) ? 'Download file' : 'Download from API server';

        $progress = Html::tag('div', null, ['class' => 'progress-inner']);
        $icon = Html::tag('span', null, ['class' => 'glyphicon glyphicon-arrow-down']);
        $text = Html::tag('span', $btnText, ['class' => 'container-text-inner']);
        $iconContainer = Html::tag('div', $icon . ' ' . $text, ['class' => 'container-icon-inner']);

        return Html::a($iconContainer . $progress, $link, $options);
    }

    private function getAlertData() {
        if (($data = Json::decode($this->value)) && !empty($data['type'])) {
            $options = [];
            if (!empty($data['message'])) {
                $options['title'] = $data['message'];
            }

            switch ($data['type']) {
                case self::TYPE_ALERT_SUCCESS:
                    Html::addCssClass($options, ['glyphicon', 'glyphicon-ok-circle', 'grid-alert grid-alert-success']);
                    break;
                case self::TYPE_ALERT_WARNING:
                    Html::addCssClass($options, ['glyphicon', 'glyphicon-warning-sign', 'grid-alert grid-alert-warning']);
                    break;
                case self::TYPE_ALERT_ERROR:
                    Html::addCssClass($options, ['glyphicon', 'glyphicon-ban-circle', 'grid-alert grid-alert-danger']);
                    break;
            }

            return  Html::tag('span', null, $options);
        }

        return null;
    }

    /**
     * @return null|string - HTML code of formatted data
     */
    private function getFormattedData()
    {
        $data = '';
        if ($this->config['field_type'] == self::TYPE_SELECT) {
            if (strpos($this->value, '.')) {
                $entryData = explode('.', $this->value);
                $data = GetListList::getByListName($entryData[1], $entryData[0]);
                $data = $data['description'];
            } else {
                $data = $this->value;
            }
        } elseif ($this->config['field_type'] == self::TYPE_MULTI_SELECT) {
            $valuesList = explode(self::MULTI_SELECT_DELIMITER, $this->value);
            if (count($valuesList) > 1) {
                foreach ($valuesList as $value) {
                    if (strpos($value, '.')) {
                        $entryData = explode('.', $value);
                        $dataFromList = GetListList::getByListName($entryData[1], $entryData[0]);
                        $data .= empty($data) ? $dataFromList['description'] : ('<br/>' . $dataFromList['description']);
                    }
                }
            } else {
                $data = $this->value;
            }
        } elseif ($this->config['field_type'] == self::TYPE_CHECKBOX) {
            $data = Html::input('checkbox', null, (int)!empty($this->value), [
                'checked' => !empty($this->value),
                'disabled' => true
            ]);
        } elseif ($this->config['field_type'] == self::TYPE_RADIO) {
            $data = Html::input('radio', null, (int)!empty($this->value), [
                'checked' => !empty($this->value),
                'disabled' => true
            ]);
        } elseif ($this->config['field_type'] == self::TYPE_DOCUMENT) {
            $data = self::getFileData();
        } elseif ($this->config['field_type'] == self::TYPE_ALERT) {
            $data = self::getAlertData();
        } elseif ($this->config['field_type'] == self::TYPE_LINK) {
            if ($this->config['format_type'] == self::TYPE_FORMAT_LINK_LOCAL) {
                $url = ['/screen/index',
                    'menu' => $this->config['field_link_menu'],
                    'screen' => $this->config['field_group_screen_link'],
                    '#' => 'tab=' . $this->config['field_screen_link'] . '&search[' . $this->config['field_settings_link'] . ']=' . $this->value
                ];
                $data =  Html::a($this->value, $url, ['target' => $this->config['type-link'], 'class' => 'custom-link']);
            } else {
                $data =  Html::a($this->value, $this->value, ['target' => $this->config['type-link'], 'class' => 'custom-link']);
            }
        } elseif ($this->config['field_type'] == self::TYPE_BUTTON) {
            return $this->getButtonField($this->getFieldOptions());
        } elseif (!empty($this->config['format_type'])) {
            $data = $this->Formatted->run($this->value, $this->config['format_type']);
        } elseif ($this->config['field_type'] == self::TYPE_HIDDEN) {
            $data = '';
        } else {
            $data = $this->value;
        }

        return Html::tag('div', $data, $this->getBasicStyles());
    }

    /**
     * @param string $listName
     *
     * @return array - HTML code of input select type
     */
    public function getOptionsList($listName)
    {
        $result = [];
        $listData = [];
        $list = GetListList::getData(['list_name' => [$listName]]);

        if (!empty($list->list)) {
            $listData = ArrayHelper::map($list->list, 'entry_name', 'description');
        }

        foreach ($listData as $id => $name) {
            $result[$listName . '.' . $id] = $name;
        }

        return $result;
    }
}