<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 */

namespace app\components;

use Yii;
use app\models\CommandData;
use yii\data\ArrayDataProvider;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

class RenderGridWidget extends BaseRenderWidget
{
    const DEFAULT_PAGE_LIMIT = 5;

    public $viewName = 'grid';
    public $isAjax = false;
    public $page = 1;

    public $row = false;
    public $col = false;
    public $tid = false;

    protected $isGrid = true;

    public function init()
    {
        if ($this->isAjax) {
            if (!empty(Yii::$app->session['tabData']->tplData[$this->tid])) {
                $template_layout = Yii::$app->session['tabData']->tplData[$this->tid]['tpl']->template_layout;
                foreach($template_layout as $section) {
                    if ($section->row_num == $this->row && $section->col_num == $this->col) {
                        $this->configuration = $section;
                    }
                }
            }
        }

        if ($this->configuration->layout_table->show_type == 'SCROLL') $this->limit = 0;
        else if (!empty($this->configuration->layout_table->count)) $this->limit = (int) $this->configuration->layout_table->count;
        else $this->limit = self::DEFAULT_PAGE_LIMIT;

        $this->offset = ($this->page - 1) * $this->limit;

        parent::init();
    }

    /**
     * @return array|null|string
     * @throws \Exception
     */
    public function run()
    {
        if ($this->isAjax) return $this->getRenderParams();
        else return parent::run();
    }

    /**
     * @return array|string - Data for grid render
     * @throws \Exception
     */
    public function getRenderParams()
    {
        $columns = [];
        $data = [];
        $isTopOrientation = true;

        if (!empty($this->configuration->layout_table)) {
            $_layout_table = $this->configuration->layout_table;

            $isTopOrientation = $_layout_table->label_orientation == 'TOP';
            $tableConfig = $_layout_table->column_configuration;
            $config = $this->configuration->layout_configuration;
            $idTable = 'activity_grid' . str_replace(".","",microtime(true));

            foreach ($config->params as $paramKey => $param) {
                if (!empty($config->labels_internationalization[$param][Yii::$app->language])) {
                    $label = $config->labels_internationalization[$param][Yii::$app->language];
                } else {
                    $label = $config->labels[$param];
                }

                $currentConfiguration = [];
                foreach ($tableConfig as $tableParam => $configuration) {
                    $tableParam = CommandData::fixedApiResult($tableParam, (!empty($this->_alias_framework) && $this->_alias_framework->enable));
                    if ($tableParam == $param) {
                        $currentConfiguration = $configuration;
                        break;
                    }
                }
                $currentConfiguration = ArrayHelper::map($currentConfiguration, 'name', 'value');
                if (!empty($this->configuration->layout_formatting)) {
                    $sectionFormatting = ArrayHelper::map($this->configuration->layout_formatting, 'name', 'value');

                    $sectionFormatting = array_filter($sectionFormatting);
                    $currentConfiguration = array_filter($currentConfiguration);

                    $currentConfiguration = array_merge($sectionFormatting, $currentConfiguration);
                }

                $headerOptions = '';
                if (!empty($currentConfiguration['label_bold'])) $headerOptions .= 'font-weight: bold;';
                if (!empty($currentConfiguration['label_italic'])) $headerOptions .= 'font-style: italic;';

                $textDecoration = '';
                if (!empty($currentConfiguration['label_strike'])) $textDecoration = 'line-through';
                if (!empty($currentConfiguration['label_underline'])) $textDecoration .= ' underline';
                if ($textDecoration) $headerOptions .= 'text-decoration: ' . $textDecoration . ';';

                if (!empty($currentConfiguration['label_text_color'])) $headerOptions .= 'color: ' . $currentConfiguration['label_text_color'] . ';';
                if (!empty($currentConfiguration['label_bg_color'])) $headerOptions .= 'background-color: ' . $currentConfiguration['label_bg_color'] . ';';

                if (!empty($currentConfiguration['label_font_family'])) $headerOptions .= 'font-family: ' . $currentConfiguration['label_font_family'] . ';';
                if (!empty($currentConfiguration['label_font_size'])) $headerOptions .= 'font-size: ' . $currentConfiguration['label_font_size'] . 'px;';

                if ($isTopOrientation) {
                    $columns[] = [
                        'attribute' => $label,
                        'format' => 'raw',
                        'headerOptions' => ['style' => $headerOptions],
                    ];
                }

                if (($this->_mode === RenderTabHelper::MODE_EDIT || $this->_mode === RenderTabHelper::MODE_INSERT) && $isTopOrientation) {
                    $widgetConfig = [
                        'mode' => $this->_mode,
                        'libName' => $this->lib_name,
                        'value' => null,
                        'dataField' => $param,
                        'dataAccess' => $this->dataAccess,
                        'config' => $currentConfiguration,
                        'dataId' => -1,
                        'isGridField' => true,
                        'data_source_get' => $this->configuration->data_source_get
                    ];
                    if (!empty($this->_alias_framework) && $this->_alias_framework->enable) {
                        $widgetConfig['data_source_update'] = $this->_alias_framework->data_source_update;
                        $widgetConfig['data_source_delete'] = $this->_alias_framework->data_source_delete;
                        $widgetConfig['data_source_create'] = $this->_alias_framework->data_source_insert;

                        if (!empty($this->configuration->layout_table->alias_framework)) {
                            $widgetConfig['aliasFrameworkPKParts'] = $this->configuration->layout_table->alias_framework;
                        }
                    }

                    $data[-1][$label] = _FieldsHelper::widget($widgetConfig);
                }

                $rowIds = [];
                if (!empty($currentConfiguration['js_event_edit']) || !empty($currentConfiguration['js_event_insert'])) {
                    $id = 'activity_grid' . str_replace(".","",microtime(true));
                    $rowIds[$paramKey] = $id;
                }

                $template = '';
                if ($isTopOrientation) {
                    $nameEventEdit = 'js_table_edit' . rand();
                    $nameEventInsert = 'js_table_insert' . rand();
                    if (!empty($currentConfiguration['js_event_edit'])) {
                        $template = $this->generateJsTemplate($currentConfiguration['js_event_edit'], $idTable, $nameEventEdit);
                        $template .= $this->generateEditJsTopTable($idTable, $paramKey, $nameEventEdit);
                    } if (!empty($currentConfiguration['js_event_insert'])) {
                        $template .= $this->generateJsTemplate($currentConfiguration['js_event_insert'], $idTable, $nameEventInsert);
                        $template .= $this->generateInsertJsTopTable($idTable, $paramKey, $nameEventInsert);
                    }
                } else {
                    $nameEventEdit = 'js_table_edit' . rand();
                    $nameEventInsert = 'js_table_insert' . rand();
                    if (!empty($currentConfiguration['js_event_edit'])) {
                        $template = $this->generateJsTemplate($currentConfiguration['js_event_edit'], $idTable, $nameEventEdit);
                        $template .= $this->generateEditJs($idTable, $nameEventEdit, $paramKey);
                    } if (!empty($currentConfiguration['js_event_insert'])) {
                        $template .= $this->generateJsTemplate($currentConfiguration['js_event_insert'], $idTable, $nameEventInsert);
                        $template .= $this->generateInsertJs($idTable, $paramKey + 2, $nameEventInsert);
                    }
                }


                if ($template) {
                    $this->view->registerJs($template);
                }


                if (!$isTopOrientation && ($this->_mode === RenderTabHelper::MODE_EDIT || $this->_mode === RenderTabHelper::MODE_INSERT)) {
                    $data[-1]['header'] = '';
                    $data[-1]['new'] = Html::tag('span', null, ['class' => 'glyphicon glyphicon-plus sub-item-control add-sub-item']);
                }

                for ($i = 0; $i < count($this->data); $i++) {
                    $widgetConfig = [
                        'mode' => $this->_mode,
                        'libName' => $this->lib_name,
                        'value' => (!empty($this->data[$i][$param])) ? $this->data[$i][$param] : null,
                        'dataField' => $param,
                        'dataAccess' => $this->dataAccess,
                        'config' => $currentConfiguration,
                        'dataId' => $this->data[$i]['pk'],
                        'isGridField' => true,
                        'data_source_get' => $this->configuration->data_source_get
                    ];

                    if (!empty($this->_alias_framework) && $this->_alias_framework->enable) {
                        $widgetConfig['dataId'] = $this->configuration->row_num . $this->configuration->col_num . $this->page . $i;
                        $widgetConfig['data_source_update'] = $this->_alias_framework->data_source_update;
                        $widgetConfig['data_source_delete'] = $this->_alias_framework->data_source_delete;
                        $widgetConfig['data_source_create'] = $this->_alias_framework->data_source_insert;

                        if (!empty($this->configuration->layout_table->alias_framework)) {
                            $widgetConfig['aliasFrameworkPKParts'] = $this->configuration->layout_table->alias_framework;
                        }

                        if (isset($config->keys) && in_array($param, $config->keys)) {
                            $widgetConfig['config']['edit_type'] = _FieldsHelper::PROPERTY_READ_ONLY;
                        }
                    }

                    $field = _FieldsHelper::widget($widgetConfig);

                    if ($isTopOrientation) {
                        $data[$i][$label] = $field;
                        if ($this->_mode === RenderTabHelper::MODE_EDIT || $this->_mode === RenderTabHelper::MODE_INSERT) {
                            $data[$i]['control'] = Html::tag('span', null, [
                                'class' => 'glyphicon glyphicon-trash sub-item-control remove-sub-item',
                                'data-id' => $widgetConfig['dataId']
                            ]);
                        }
                    } else {
                        if ($this->_mode === RenderTabHelper::MODE_EDIT || $this->_mode === RenderTabHelper::MODE_INSERT) {
                            $data[-1][$i] = Html::tag('span', null, [
                                'class' => 'glyphicon glyphicon-trash sub-item-control remove-sub-item',
                                'data-id' => $widgetConfig['dataId']
                            ]);
                        }

                        $data[$paramKey][$i] = $field;
                        $columns[$i] = [
                            'attribute' => $i,
                            'format' => 'raw'
                        ];
                    }
                }


                if (!$isTopOrientation) {
                    $data[$paramKey]['header'] = Html::tag('span', $label, ['style' => $headerOptions]);
                    if ($this->_mode === RenderTabHelper::MODE_EDIT || $this->_mode === RenderTabHelper::MODE_INSERT) {
                        $data[-1]['header'] = '';
                        $data[-1]['new'] = Html::tag('span', null, ['class' => 'glyphicon glyphicon-plus sub-item-control add-sub-item']);
                    }

                    if ($this->_mode === RenderTabHelper::MODE_EDIT || $this->_mode === RenderTabHelper::MODE_INSERT) {
                        $widgetConfig = [
                            'mode' => $this->_mode,
                            'libName' => $this->lib_name,
                            'value' => null,
                            'dataField' => $param,
                            'dataAccess' => $this->dataAccess,
                            'config' => $currentConfiguration,
                            'dataId' => -1,
                            'isGridField' => true,
                            'data_source_get' => $this->configuration->data_source_get
                        ];

                        if (!empty($this->_alias_framework) && $this->_alias_framework->enable) {
                            $widgetConfig['data_source_update'] = $this->_alias_framework->data_source_update;
                            $widgetConfig['data_source_delete'] = $this->_alias_framework->data_source_delete;
                            $widgetConfig['data_source_create'] = $this->_alias_framework->data_source_insert;

                            if (!empty($this->configuration->layout_table->alias_framework)) {
                                $widgetConfig['aliasFrameworkPKParts'] = $this->configuration->layout_table->alias_framework;
                            }
                        }

                        $data[$paramKey]['new'] = _FieldsHelper::widget($widgetConfig);
                    }
                }
            }

            if (!$isTopOrientation) {
                if ($this->_mode === RenderTabHelper::MODE_EDIT || $this->_mode === RenderTabHelper::MODE_INSERT) {
                    array_unshift($columns, [
                        'attribute' => 'new',
                        'format' => 'raw'
                    ]);
                }
                array_unshift($columns, [
                    'attribute' => 'header',
                    'format' => 'html',
                    'contentOptions' => ['style' => '
                        border-right: 2px solid #ddd;
                        white-space: nowrap;
                    ']
                ]);
            }

            if (($this->_mode === RenderTabHelper::MODE_EDIT || $this->_mode === RenderTabHelper::MODE_INSERT) && $isTopOrientation) {
                $data[-1]['control'] = Html::tag('span', null, ['class' => 'glyphicon glyphicon-plus sub-item-control add-sub-item']);
                $columns[] = [
                    'attribute' => 'control',
                    'format' => 'raw',
                    'headerOptions' => ['style' => 'display: none;'],
                ];
            }
        }

        if ($this->isAjax) return json_encode($data);

        $dataProvider = new ArrayDataProvider([
            'allModels' => $data,
            'sort' => false,
        ]);

        if (isset($_layout_table)) {
            if ($_layout_table->show_type == 'SCROLL') {
                $this->dataCount = false;
            }
        }

        return [
            'idTable' => $idTable,
            'dataProvider' => $dataProvider,
            'columns' => $columns,
            'ids' => $rowIds,
            'isTopOrientation' => $isTopOrientation,
            'pageCount' => ($this->dataCount) ? ceil($this->dataCount / $this->limit) : false,
            'isScrollTable' => !empty($this->configuration->layout_table->show_type) && ($this->configuration->layout_table->show_type == 'SCROLL')
        ];
    }
}