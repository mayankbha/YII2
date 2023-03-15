<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 */

namespace app\components;

use app\models\UserAccount;
use Yii;

/**
 * Class RenderChartWidget
 * @package app\components
 * @property _FormattedHelper $Formatted
 */
class RenderChartWidget extends BaseRenderWidget
{
    protected $Formatted ;

    public $viewNameByType = [
        RenderTabHelper::SECTION_TYPE_CHART_PIE => 'chart-pie',
        RenderTabHelper::SECTION_TYPE_CHART_LINE => 'chart-line',
        RenderTabHelper::SECTION_TYPE_CHART_BAR_HORIZONTAL => 'chart-bar-hor',
        RenderTabHelper::SECTION_TYPE_CHART_BAR_VERTICAL => 'chart-bar-ver',
        RenderTabHelper::SECTION_TYPE_CHART_DOUGHNUT => 'chart-doughnut'
    ];

    public function init()
    {
        $this->isChartLine = ($this->configuration->layout_type === RenderTabHelper::SECTION_TYPE_CHART_LINE) || ($this->configuration->layout_type === RenderTabHelper::SECTION_TYPE_CHART_BAR_HORIZONTAL) || ($this->configuration->layout_type === RenderTabHelper::SECTION_TYPE_CHART_BAR_VERTICAL);
        parent::init();
    }

    public function run()
    {
        $this->Formatted = new _FormattedHelper();
        if (!empty($this->viewNameByType[$this->configuration->layout_type])) $this->viewName = $this->viewNameByType[$this->configuration->layout_type];
        return $this->renderWidget();
    }

    public function getUserChartColors() {
        $userSettings = UserAccount::getSettings();
        if(!empty($userSettings->style_template)) {
            $userColor = [];
            if (!empty($userSettings->style_template['chart_color_first'])) {
                $userColor[] = $userSettings->style_template['chart_color_first'];
            }
            if (!empty($userSettings->style_template['chart_color_second'])) {
                $userColor[] = $userSettings->style_template['chart_color_second'];
            }
            if (!empty($userSettings->style_template['chart_color_third'])) {
                $userColor[] = $userSettings->style_template['chart_color_third'];
            }
            if (!empty($userSettings->style_template['chart_color_fourth'])) {
                $userColor[] = $userSettings->style_template['chart_color_fourth'];
            }
            return $userColor;
        }
        return [];
    }

    /**
     * @return array - Data for chart render
     */
    protected function getRenderParams()
    {
        $preparedData = [];
        $categories = [];

        $userColorChart = $this->getUserChartColors();

        if ($this->configuration->layout_type === RenderTabHelper::SECTION_TYPE_CHART_PIE) {
            $preparedData = $this->generatePieChart($userColorChart);
        } else if ($this->configuration->layout_type === RenderTabHelper::SECTION_TYPE_CHART_LINE) {
            $param = $this->configuration->layout_configuration->params['x'][0];

            //$dataAccess = (!empty($this->dataAccess[$param])) ? $this->dataAccess[$param] : []; //TODO: if permission cat be NONE READ
            //if (in_array(BaseRenderWidget::FIELD_ACCESS_READ, $dataAccess)) {
                if (!empty($this->data)) {
                    foreach ($this->data as $i => $data) {
                        $categories[] = isset($data[$param]) ? $this->Formatted->run($data[$param], $this->configuration->layout_configuration->format_type[$param]) : '';
                    }

                    foreach ($this->configuration->layout_configuration->params['y'] as $i => $param) {
                        //$dataAccess = (!empty($this->dataAccess[$param])) ? $this->dataAccess[$param] : self::FIELD_ACCESS_UPDATE; //TODO: if permission cat be NONE READ
                        //if (!in_array(BaseRenderWidget::FIELD_ACCESS_READ, $dataAccess)) continue;

                        if (!empty($this->configuration->layout_configuration->labels_internationalization[$param][Yii::$app->language])) {
                            $preparedData[$i]['label'] = $this->configuration->layout_configuration->labels_internationalization[$param][Yii::$app->language];
                        } else {
                            $preparedData[$i]['label'] = $this->configuration->layout_configuration->labels[$param];
                        }

                        $preparedData[$i]['backgroundColor'] = UserAccount::hex2rgba(RenderTabHelper::getColor($userColorChart, $i), 0.2);
                        $preparedData[$i]['borderColor'] = RenderTabHelper::getColor($userColorChart, $i);
                        $preparedData[$i]['lineTension'] = 0;
                        foreach ($this->data as $data) {
                            $preparedData[$i]['data'][] = isset($data[$param]) ? (int)$data[$param] : '';
                            $preparedData[$i]['formatData'][] = $this->Formatted->run(isset($data[$param]) ? (int)$data[$param] : '', $this->configuration->layout_configuration->format_type[$param]);
                        }
                    }
                }
            //}
        } else if ($this->configuration->layout_type === RenderTabHelper::SECTION_TYPE_CHART_BAR_HORIZONTAL) {
            $result = $this->generateBarChart($userColorChart);
            $preparedData = $result['preparedData'];
            $categories = $result['categories'];
        } else if ($this->configuration->layout_type === RenderTabHelper::SECTION_TYPE_CHART_BAR_VERTICAL) {
            $result = $this->generateBarChart($userColorChart);
            $preparedData = $result['preparedData'];
            $categories = $result['categories'];
        } else if ($this->configuration->layout_type === RenderTabHelper::SECTION_TYPE_CHART_DOUGHNUT) {
            $preparedData = $this->generatePieChart($userColorChart);
        }

        return array('dataProvider' => $preparedData, 'categories' => $categories);
    }

    protected function generatePieChart($userColorChart){
        $preparedData = [];
        foreach ($this->configuration->layout_configuration->params as $i => $param) {
            //$dataAccess = (!empty($this->dataAccess[$param])) ? $this->dataAccess[$param] : []; //TODO: if permission cat be NONE READ
            //if (!in_array(BaseRenderWidget::FIELD_ACCESS_READ, $dataAccess)) continue;

            $itemValue = isset($this->data[$param]) ? (int)$this->data[$param] : null;
            $preparedData['backgroundColor'][] = RenderTabHelper::getColor($userColorChart, $i);
            $preparedData['data'][] = $itemValue;
            $preparedData['label'][] = $this->Formatted->run($itemValue, $this->configuration->layout_configuration->format_type[$param]);

            if (!empty($this->configuration->layout_configuration->labels_internationalization[$param][Yii::$app->language])) {
                $preparedData['name'][] = $this->configuration->layout_configuration->labels_internationalization[$param][Yii::$app->language];
            } else {
                $preparedData['name'][] = $this->configuration->layout_configuration->labels[$param];
            }
        }
        return $preparedData;
    }

    protected  function generateBarChart($userColorChart) {
        $param = $this->configuration->layout_configuration->params['x'][0];

        //$dataAccess = (!empty($this->dataAccess[$param])) ? $this->dataAccess[$param] : []; //TODO: if permission cat be NONE READ
        //if (in_array(BaseRenderWidget::FIELD_ACCESS_READ, $dataAccess)) {
        $preparedData = [];
        $categories = [];
        if (!empty($this->data)) {
            foreach ($this->data as $i => $data) {
                $categories[] = isset($data[$param]) ? $this->Formatted->run($data[$param], $this->configuration->layout_configuration->format_type[$param]) : '';
            }

            foreach ($this->configuration->layout_configuration->params['y'] as $i => $param) {
                //$dataAccess = (!empty($this->dataAccess[$param])) ? $this->dataAccess[$param] : self::FIELD_ACCESS_UPDATE; //TODO: if permission cat be NONE READ
                //if (!in_array(BaseRenderWidget::FIELD_ACCESS_READ, $dataAccess)) continue;

                if (!empty($this->configuration->layout_configuration->labels_internationalization[$param][Yii::$app->language])) {
                    $preparedData[$i]['label'] = $this->configuration->layout_configuration->labels_internationalization[$param][Yii::$app->language];
                } else {
                    $preparedData[$i]['label'] = $this->configuration->layout_configuration->labels[$param];
                }

                $preparedData[$i]['fill'] = false;
                $preparedData[$i]['borderColor'] = RenderTabHelper::getColor($userColorChart, $i);
                foreach ($this->data as $j => $data) {
                    $preparedData[$i]['data'][] = isset($data[$param]) ? (int)$data[$param] : '';
                    $preparedData[$i]['backgroundColor'][] = RenderTabHelper::getColor($userColorChart, $i);
                    $preparedData[$i]['formatData'][] = $this->Formatted->run(isset($data[$param]) ? (int)$data[$param] : '', $this->configuration->layout_configuration->format_type[$param]);
                }
            }
        }
        //}
        return ['preparedData' => $preparedData, 'categories' => $categories];
    }

}
