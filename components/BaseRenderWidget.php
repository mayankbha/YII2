<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 */

namespace app\components;

/**
 * @property string|array $data_source_get;
 * @property string $viewName;
 * @property array $data;
 * @property array $config;
 *
 */

use app\models\CommandData;
use app\models\CustomLibs;
use Yii;
use yii\base\Widget;
use yii\helpers\Json;

/**
 * Class BaseRenderWidget
 * @property \stdClass|array $data
 * @property \stdClass $_search_configuration
 */
class BaseRenderWidget extends Widget
{
    const FIELD_ACCESS_READ = 'R';
    const FIELD_ACCESS_UPDATE = 'E';
    const FIELD_ACCESS_FULL = 'U';
    const FIELD_ACCESS_NONE = 'N';

    public $viewName = '';

    public $lib_name;
    public $func_name;
    public $configuration;
    public $_mode = false;
    public $_data_id;
    public $_cache = false;
    public $_last_found_data = null;
    public $_search_configuration = null;
    public $_alias_framework = null;

    public $limit = false;
    public $offset = false;
    public $isAjax = false;

    protected $isGrid = false;
    protected $isChartLine = false;

    protected $data = [];
    protected $dataAccess = [];

    protected $dataCount = 0;

    /**
     * Js events lists, that don't connection with screen action (add, edit, delete and other)
     * @var array
     */
    static public $independentJsEvents = [
        'change'
    ];

    public function init()
    {
        parent::init();

        $this->fixedApiResult();

        $this->func_name = $this->configuration->data_source_get;
        $this->_data_id = (string)$this->_data_id;

        if ($this->_data_id) {
            $this->getData($this->isGrid || $this->isChartLine);
        }
    }

    protected function fixedApiResult()
    {
        $labels = [];
        $paramsType = [];
        $formatType = [];
        $config = $this->configuration->layout_configuration;

        $prepareFunction = function ($param) use (&$labels, &$paramsType, &$formatType, $config) {
            $newKey = CommandData::fixedApiResult($param, (!empty($this->_alias_framework) && $this->_alias_framework->enable));

            if (!empty($config->labels[$param])) {
                $labels[$newKey] = ($config->labels[$param]);
            }
            $paramsType[$newKey] = isset($config->params_type[$param]) ? $config->params_type[$param] : null;
            $formatType[$newKey] = isset($config->format_type[$param]) ? $config->format_type[$param] : null;

            return $newKey;
        };

        if ($this->isChartLine && !empty($config->params['x']) && !empty($config->params['y'])) {
            foreach ($config->params['x'] as $key => $param) {
                $config->params['x'][$key] = $prepareFunction($param);
            }
            foreach ($config->params['y'] as $key => $param) {
                $config->params['y'][$key] = $prepareFunction($param);
            }
        } else {
            foreach ($config->params as $key => $param) {
                $config->params[$key] = $prepareFunction($param);
            }
        }
        $config->labels = $labels;
        $config->params_type = $paramsType;
        $config->format_type = $formatType;
    }

    /**
     * Getting data from API server
     *
     * @param boolean $isGetParent - Set TRUE if is a parent data
     */
    protected function getData($isGetParent)
    {
        $subDataPK = [];
        $relatedField = CustomLibs::getRelated($this->lib_name, $this->func_name);

        if ($isGetParent && !empty($this->_data_id)) {
            if (CustomLibs::getTableName($this->lib_name, $this->func_name)) {
                $subDataPK = CustomLibs::getPK($this->lib_name, $this->func_name);
            } else if (!empty($this->_search_configuration->pk_configuration)) {
                $fieldParams = CommandData::getFieldListForQuery($this->_data_id);
            }
        }


        if (empty($fieldParams)) {
            $fieldParams = CommandData::getFieldListForQuery($this->_data_id, $relatedField);
        }

        $postData = [
            'lib_name' => $this->lib_name,
            'func_name' => $this->func_name,
            'alias_framework_info' => $this->_alias_framework,
            'search_function_info' => [
                'config' => $this->_search_configuration,
                'data' => Json::decode($this->_last_found_data, true)
            ]
        ];

        if ($this->_cache) unset(Yii::$app->session['cacheData']);

        $cacheData = Yii::$app->session['cacheData'];
        $currentCacheLib = (!empty($cacheData[$this->lib_name])) ? $cacheData[$this->lib_name] : null;
        $currentCacheLibFunction = (!empty($currentCacheLib[$postData['func_name']])) ? $currentCacheLib[$postData['func_name']] : null;

        if (empty($currentCacheLibFunction) || $this->isAjax || $isGetParent) {
            if ($isGetParent) {
                $additionalParam = CommandData::getGridFieldOutListForQuery($this->configuration, $subDataPK);
            } else {
                $additionalParam = CommandData::getFieldOutListForQuery($this->lib_name, $this->func_name);
            }

            if ($this->limit && $isGetParent) {
                $additionalParam = array_merge($additionalParam, [
                    'limitnum' => $this->limit,
                    'offsetnum' => $this->offset
                ]);
            }

            $this->data = CommandData::getData($fieldParams, $postData, $additionalParam);

            if (!$this->isAjax || !$isGetParent) {
                $currentCacheLib = ($currentCacheLib) ? array_merge($currentCacheLib, [$postData['func_name'] => $this->data]) : [$postData['func_name'] => $this->data];
                Yii::$app->session['cacheData'] = ($cacheData) ? array_merge($cacheData, [$this->lib_name => $currentCacheLib]) : [$this->lib_name => $currentCacheLib];
            }
        } else {
            $this->data = $currentCacheLibFunction;
        }

        if ($this->limit && !$this->isAjax && !empty($relatedField)) {
            $selectID = CommandData::getData($fieldParams, $postData, ['field_out_list' => [$relatedField]]);
            $this->dataCount = (!empty($selectID->list)) ? count($selectID->list) : 0;
        }

        if ($this->data) {
            $this->dataAccess = Yii::$app->session['tabData']->fieldsAccess[$postData['lib_name']][$postData['func_name']];

            $pkList = !empty($this->data->pkList) ? json_encode($this->data->pkList) : '[]';
            $this->view->registerJs("common.setLastGetDataPK($pkList, '$this->func_name')");

            if ($isGetParent) {
                $this->data = $this->data->list;
                foreach ($this->data as $key => $row) {
                    $newPK = [];
                    foreach($subDataPK as $item) {
                        if (!empty($row[$item])) $newPK[] = $row[$item];
                    }
                    $newPK = implode(';', $newPK);
                    $this->data[$key]['pk'] = $newPK;
                }
            } else {
                $this->data = $this->data->list[0];
            }
        }
    }

    public function run()
    {
        return $this->renderWidget();
    }

    protected function getRenderParams()
    {
        return ['data' => $this->data];
    }

    protected function renderWidget()
    {
        return empty($this->viewName) ? null : $this->render('@app/views/render/' . $this->viewName, $this->getRenderParams());
    }

    public function getSectionType()
    {
        return $this->configuration->layout_type;
    }

    public function executeLibFunction($fieldList, $postData, $additionalParam) {
        $postData['func_extensions'] = ($decodeJSON = json_decode($this->configuration->data_source_extension, true)) ? [$decodeJSON] : [$this->configuration->data_source_extension];
        return CommandData::getData($fieldList, $postData, $additionalParam);
    }

    public function generateJsTemplate($jsCode, $idBlock, $nameEvent)
    {
        if (!empty($idBlock)) {
            return /** @lang JavaScript */ '
                $("#' . $idBlock . '").on("' . $nameEvent . '", "input", function() {
                    try {
                        ' . base64_decode($jsCode) . '
                        $(this).removeClass("not-valid-data");
                    } catch(e) {
                        $(this).addClass("not-valid-data");
                        throw new Error(e.message);
                    }
                });
            ';
        }

        return null;
    }

    public function generateJsTemplateField($jsCode, $idBlock, $nameEvent)
    {
        if (!empty($idBlock)) {
            if (in_array($nameEvent, self::$independentJsEvents)) {
                $exceptionType = '
                    bootbox.confirm({
                        message: message,
                        buttons: {
                            confirm: {
                                label: "Continue",
                                className: "btn-success"
                            },
                            cancel: {
                                label: "Cancel",
                                className: "btn-danger"
                            }
                        },
                        callback: function (result) {
                            if (result) {
                                $( "#' . $idBlock . '").off("'.$nameEvent.'").removeClass("not-valid-data");
                            }
                        }
                    });
                ';
            } else {
                $exceptionType = 'throw new Error(message);';
            }

            return /** @lang JavaScript */ '            
                $("#' . $idBlock . '").on("' . $nameEvent . '", function () {
                    try {
                        ' . base64_decode($jsCode) . '
                        $(this).removeClass("not-valid-data");
                        
                    } catch (e) {
                        $(this).addClass("not-valid-data");
                        var message = common.getErrorMessageI18N(e.message);
                        
                        ' . $exceptionType . '
                    }
                });
            ';
        }

        return null;
    }

    public function generateEditJs($idBlock, $nameEvent, $index)
    {
        return /** @lang JavaScript */ '
            $("#' . $idBlock . ' input[data-sub-id!=\'-1\']").on("edit-left-table-custom-js", function(event) {
                if ($(event.target).closest("tr").data("key") == ' . $index . ') {
                    try {
                        $(event.target).trigger("' . $nameEvent . '");
                    } catch (e) {
                        common.customJsException(e.message);
                    }
                }
            });
        ';
    }

    public function generateInsertJs($idBlock, $index, $nameEvent)
    {
        return /** @lang JavaScript */ '
            $("#' . $idBlock . ' .add-sub-item").on("insert-left-table-custom-js", function() {
                try {
                    $("#' . $idBlock . ' tr:eq(' . $index . ') input").eq(0).trigger("' . $nameEvent . '");
                } catch (e) {
                    common.customJsException(e.message);
                }
            });
        ';
    }

    public function generateInsertJsTopTable($idBlock, $index, $nameEvent)
    {
        return /** @lang JavaScript */ '
            $("#' . $idBlock . ' .add-sub-item").on("insert-top-table-custom-js", function() {
                try {
                    $("#' . $idBlock . ' input").eq(' . $index . ').trigger("' . $nameEvent . '");
                } catch (e) {
                    common.customJsException(e.message);
                }
            });
        ';
    }

    public function generateEditJsTopTable($idBlock, $index, $nameEvent)
    {
        return /** @lang JavaScript */ '
            $("#' . $idBlock . ' input[data-sub-id!=\'-1\']").on("edit-top-table-custom-js", function(event) {
                if ($(event.target).closest("td")[0].cellIndex == ' . $index . ') {
                    try {
                        $(event.target).trigger("' . $nameEvent . '");
                    } catch (e) {
                        common.customJsException(e.message);
                    }
                }
            });
        ';
    }

    public function getFieldId($config)
    {
        if (!empty($config['identifier'])) {
            return $config['identifier'];
        }

        $filteredDataSource = str_replace([':', ',', '.'], '-', $this->configuration->data_source_get);
        return $filteredDataSource . '--' . CommandData::fixedApiResult($config['data_field']);
    }
}