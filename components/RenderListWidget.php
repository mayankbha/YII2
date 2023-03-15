<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 */

namespace app\components;

use Yii;
use app\models\CommandData;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

class RenderListWidget extends BaseRenderWidget
{
    const LABEL_ORIENTATION_LEFT = 'LEFT';
    const LABEL_ORIENTATION_TOP = 'TOP';

    public function run()
    {
        $result = null;
        if (!empty($this->configuration->layout_fields) && is_array($this->configuration->layout_fields)) {
            foreach ($this->configuration->layout_fields as $field) {
                $result = $result . $this->buildField($field);
            }
        }

        $this->view->registerJs("$('.grid-stack').gridstack({staticGrid: true, verticalMargin: 5, cellHeight: 20, float: true})");
        return Html::tag('div', $result, ['class' => 'grid-stack fields-row-wrapper']);
    }

    /**
     * Getting HTML code of field
     *
     * @param array $field
     *
     * @return string
     * @throws \Exception
     */
    private function buildField($field)
    {
        $config = ArrayHelper::map($field, 'name', 'value');
        $internationalization = ArrayHelper::map($field, 'name', 'internationalization');
        if (!empty($this->configuration->layout_formatting)) {
            $sectionFormatting = ArrayHelper::map($this->configuration->layout_formatting, 'name', 'value');
            $sectionFormatting = array_filter($sectionFormatting);

            $config = array_filter($config);
            $config = array_merge($sectionFormatting, $config);
        }

        if (!empty($config['field_type'])) {
            if (($config['field_type'] == _FieldsHelper::TYPE_HIDDEN) && !$this->_mode) {
                return null;
            } else if ($config['field_type'] == _FieldsHelper::TYPE_LABEL) {
                return $this->buildFieldHelper(null, $config);
            }
        }

        $template = null;
        if (!empty($config['js_event_edit'])) {
            $template = $this->generateJsTemplateField($config['js_event_edit'], $this->getFieldId($config), 'js_event_edit');
        }
        if (!empty($config['js_event_insert'])) {
            $template .= $this->generateJsTemplateField($config['js_event_insert'], $this->getFieldId($config), 'js_event_insert');
        }
        if (!empty($config['js_event_change'])) {
            $template .= $this->generateJsTemplateField($config['js_event_change'], $this->getFieldId($config), 'change');
        }
        if ($template) {
            $this->view->registerJs($template);
        }

        $dataField = !empty($config['data_field']) ? CommandData::fixedApiResult($config['data_field'], $this->_alias_framework->enable) : null;
        $value = isset($this->data[$dataField]) ? $this->data[$dataField] : null;

        $widgetConfig = [
            'mode' => $this->_mode,
            'libName' => $this->lib_name,
            'value' => $value,
            'dataField' => $dataField,
            'dataAccess' => $this->dataAccess,
            'config' => $config,
            'data_source_get' => $this->configuration->data_source_get
        ];
        if ($this->_alias_framework->enable) {
            $widgetConfig['data_source_update'] = $this->_alias_framework->data_source_update;
            $widgetConfig['data_source_delete'] = $this->_alias_framework->data_source_delete;
            $widgetConfig['data_source_create'] = $this->_alias_framework->data_source_insert;
        }

        $result = _FieldsHelper::widget($widgetConfig);

        if (!empty($config['field_type']) && ($config['field_type'] == _FieldsHelper::TYPE_HIDDEN) && $this->_mode) {
            return $result;
        }

        return $this->buildFieldHelper($result, $config, $internationalization);
    }

    /**
     * Getting HTML code of section fields
     * @param string $field - Field HTML code
     * @param array $config - Configuration for field
     * @param array $internationalization
     * @return string
     */
    private function buildFieldHelper($field, $config, $internationalization = [])
    {
        $label = null;
        $leftLabelFieldOptions  = [];
        $labelOptions = $this->getLabelOptions($config);

        if (isset($config['block_height']) && (int)$config['block_height'] > 0) {
            $blockHeight = (int)$config['block_height'];
        } elseif (!empty($config['label_orientation']) && $config['label_orientation'] == self::LABEL_ORIENTATION_TOP) {
            $blockHeight = 3;
        } else {
            $blockHeight = 2;
        }

        $divOptions = [
            'class' => 'grid-stack-item field-wrapper',
            'data' => [
                'gs-x' => (isset($config['block_col'])) ? (int)$config['block_col'] : 0,
                'gs-y' => (isset($config['block_row'])) ? (int)$config['block_row'] : 0,
                'gs-width' => (isset($config['block_width'])) ? (int)$config['block_width'] : 12,
                'gs-height' => $blockHeight
            ]
        ];
        Html::addCssClass($divOptions, (in_array($this->_mode, [RenderTabHelper::MODE_INSERT, RenderTabHelper::MODE_EDIT]))  ? 'is-edit' : '');

        $innerContentOption = ['class' => 'grid-stack-item-content'];
        if (!empty($config['label_orientation'])) {
            if ($config['label_orientation'] == self::LABEL_ORIENTATION_LEFT) {
                if (!empty($config['label_width'])) {
                    Html::addCssStyle($labelOptions, ['width' => $config['label_width'] . 'px' , 'flex' => ' 0 0 ' . $config['label_width'] . 'px']);
                }

                Html::addCssClass($labelOptions, ['field-left-label']);
                Html::addCssClass($innerContentOption, 'field-wrapper-left-label');
                $leftLabelFieldOptions = ['class' => 'left-label-wrapper'];
            } else {
                Html::addCssClass($labelOptions, 'top-label-wrapper');
            }
        }

        if ($field) {
            $field = Html::tag('div', $field, $leftLabelFieldOptions);
        }

        if (!empty($internationalization['field_label'][Yii::$app->language])) {
            $labelInternalization = $internationalization['field_label'][Yii::$app->language];
        } else if (!empty($config['field_label'])) {
            $labelInternalization = $config['field_label'];
        }

        if (!empty($labelInternalization)) {
            if (!empty($internationalization['field_tooltip'][Yii::$app->language])) {
                $tooltipInternalization = $internationalization['field_tooltip'][Yii::$app->language];
            } else if (!empty($config['field_tooltip'])) {
                $tooltipInternalization = $config['field_tooltip'];
            }

            $labelTextOptions = !empty($tooltipInternalization) ? $this->getTooltipOptions($tooltipInternalization) : [];
            $labelText = Html::tag('span',  $labelInternalization, $labelTextOptions);
            $label = Html::label($labelText, null, $labelOptions);
        }

        if (!empty($config['field_type'])) {
            if (in_array($config['field_type'], [_FieldsHelper::TYPE_INLINE_SEARCH, _FieldsHelper::TYPE_DATALIST])) {
                Html::addCssClass($innerContentOption, 'item-content-inline-search');
            } elseif ($config['field_type'] == _FieldsHelper::TYPE_LABEL && isset($config['label_text_align'])) {
                switch ($config['label_text_align']) {
                    case 'left':
                        $justifyContent = 'flex-start';
                        break;
                    case 'right':
                        $justifyContent = 'flex-end';
                        break;
                    default:
                        $justifyContent = 'center';
                        break;
                }

                Html::addCssClass($innerContentOption, 'field-label-block');
                Html::addCssStyle($innerContentOption, [
                    'text-align' => $config['label_text_align'],
                    'justify-content' => $justifyContent
                ]);
            }
        }

        $innerContent = Html::tag('div', $label . $field, $innerContentOption);
        return Html::tag('div', $innerContent, $divOptions);
    }

    private function getLabelOptions($config) {
        $option = ['style' => ''];

        $textDecoration = '';
        $textDecoration .= (!empty($config['label_strike'])) ? 'line-through' : '';
        $textDecoration .= (!empty($config['label_underline'])) ? ' underline' : '';

        if (!empty($textDecoration)) {
            Html::addCssStyle($option, ['text-decoration' => $textDecoration]);
        }
        if (!empty($config['label_bold'])) {
            Html::addCssStyle($option, ['font-weight' => 'bold']);
        }
        if (!empty($config['label_italic'])) {
            Html::addCssStyle($option, ['font-style' => 'italic']);
        }

        if (!empty($config['label_text_color'])) {
            Html::addCssStyle($option, ['color' => $config['label_text_color'] . '!important']);
        }
        if (!empty($config['label_bg_color'])) {
            Html::addCssStyle($option, ['background-color' => $config['label_bg_color']]);
        }

        if (!empty($config['label_font_family'])) {
            Html::addCssStyle($option, ['font-family' => $config['label_font_family']]);
        }
        if (!empty($config['label_font_size'])) {
            Html::addCssStyle($option, ['font-size' => $config['label_font_size'] . 'px']);
        }

        return $option;
    }

    protected function getTooltipOptions($title)
    {
        return ["data-toggle" => "tooltip", "data-placement" => "top", "title" => $title];
    }
}
