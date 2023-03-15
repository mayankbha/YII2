<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 */

namespace app\components;

use Yii;
use app\models\Screen;
use app\modules\admin\models\forms\JobSchedulerForm;
use app\modules\admin\models\JobScheduler;
use yii\base\Component;
use yii\helpers\Html;

/**
 * Class RenderTabHelper
 * @property _TemplateHelper|string $screen_tab_template
 */
class RenderTabHelper extends Component
{
    const DEFAULT_SECTION_LABEL_NAME = '*Default_label';

    const SECTION_TYPE_GRID = 'TABLE';
    const SECTION_TYPE_LIST = 'LIST';
    const SECTION_TYPE_CHART_PIE = 'CHART-PIE';
    const SECTION_TYPE_CHART_LINE = 'CHART-LINE';
    const SECTION_TYPE_CHART_BAR_HORIZONTAL = 'CHART-BAR-HORIZONTAL';
    const SECTION_TYPE_CHART_BAR_VERTICAL = 'CHART-BAR-VERTICAL';
    const SECTION_TYPE_CHART_DOUGHNUT = 'CHART-DOUGHNUT';
    const SECTION_TYPE_DOCUMENT = 'DOCUMENT';

    const MODE_EDIT = 'edit';
    const MODE_INSERT = 'insert';
    const MODE_EXECUTE = 'execute';
    const MODE_COPY = 'copy';

    const CHART_LINE_COLOR = '#3e95cd';

    public $_widget_class = array(
        self::SECTION_TYPE_LIST => RenderListWidget::class,
        self::SECTION_TYPE_GRID => RenderGridWidget::class,
        self::SECTION_TYPE_CHART_PIE => RenderChartWidget::class,
        self::SECTION_TYPE_CHART_LINE => RenderChartWidget::class,
        self::SECTION_TYPE_CHART_BAR_HORIZONTAL => RenderChartWidget::class,
        self::SECTION_TYPE_CHART_BAR_VERTICAL => RenderChartWidget::class,
        self::SECTION_TYPE_CHART_DOUGHNUT => RenderChartWidget::class,
        self::SECTION_TYPE_DOCUMENT => RenderDocumentWidget::class,
    );

    private $_mode;
    private $_data_id;
    private $_layout_type;
    private $_cache;
    private $_last_found_data;

    private $id = '';
    private $screen_desc = '*Default_description';
    private $screen_lib = '';
    private $screen_name = '';
    private $screen_tab_devices = 'D;M;W';
    private $screen_tab_name = '*Default_name';
    private $template_layout = '';
    private $screen_tab_text = '*Default_text';
    private $screen_tab_weight = '0';

    public function render($tabData, $mode='')
    {
		//echo '<pre>'; print_r($tabData);

		//die;

        if(!is_array($tabData)){
            return '';
        }

        foreach ($tabData as $property => $value) {
            if (isset($this->$property)) $this->$property = $value;
        }

        $this->_mode = $mode;

		//echo '<pre>'; print_r($this->template_layout);

        //$this->_data_id = $dataID;
        //$this->_cache = $cache;
        //$this->_last_found_data = $lastFoundData;

        //$this->template_layout = JobScheduler::decodeTemplate($this->template_layout, true);

		//echo '<pre>'; print_r($this->template_layout);

        if (!empty($this->template_layout)) { echo 'in if :: ';
            if ($this->_layout_type = JobSchedulerForm::$screen_types[1]) {
                return $this->initRender($this->template_layout);
            }
        }

        return '';
    }

    /**
     * @param $templateLayout
     * @return string - Tab HTML code
     */
    public function initRender($templateLayout)
    {
		echo '<pre>'; print_r($templateLayout);

        $result = '';
        $width = ($this->_layout_type['col_count'] == 1) ? 12 : 6;
        $isHeaderRender = false;

        for ($row = 1; $row <= $this->_layout_type['row_count']; $row++) {
            $rowResult = '';
            for ($col = 1; $col <= $this->_layout_type['col_count']; $col++) {
                foreach ($templateLayout as $item) {
                    if (!$isHeaderRender && $this->_layout_type['header'] && $item['row_num'] == 0 && $item['col_num'] == 0) {
                        $result = Html::tag('div', $this->renderSectionContent($item), [
                            'class' => 'alert alert-warning header-section',
                            'data-source-get' => $item['data_source_get'],
                            'data-type' => $item['layout_type'],
                            'style' => 'position: relative'
                        ]) . $result;
                        $isHeaderRender = true;
                    }
                    if ($item['row_num'] == $row && $item['col_num'] == $col) {
                        $rowResult .= $this->renderSection($item, $width);
                    }
                }
            }
            $result .= Html::tag('div', $rowResult, ['class' => 'row']);
        }

        return $result;
    }

    /**
     * @param $configuration - Template of section settings
     * @param integer $width - bootstrap col-sm-(1..12)
     * @return string
     */
    public function renderSection($configuration, $width)
    {
        if (!empty($configuration['layout_label_internationalization'][Yii::$app->language])) {
            $label = $configuration['layout_label_internationalization'][Yii::$app->language];
        } else if (!empty($configuration['layout_label'])) {
            $label = $configuration['layout_label'];
        }

        return Html::tag('div',
            Html::tag('div',
                Html::tag('div',
                    Html::tag('h3', $label, ['class' => "panel-title"]) .
                    Html::tag('span',
                        Html::tag('span', null, ['class' => "glyphicon glyphicon-new-window detach-icon", 'aria-hidden' => "true", 'title' => "Detach panel"]) .
                        Html::tag('span', null, ['class' => "glyphicon glyphicon-remove attach-icon", 'aria-hidden' => "true", 'title' => "Attach panel"]),
                        ['class' => "panel-controls"]
                    ),
                    ['class' => "panel-heading"]
                ) .
                Html::tag('div',
                    $this->renderSectionContent($configuration),
                    ['class' => "panel-body"]
                ),
                [
                    'class' => "panel panel-default panel-window",
                    'style' => "width: 100%;",
                   /*'data' => [
                        'type' => $configuration->layout_type,
                        'source-get' => $configuration->data_source_get,
                        'source-delete' => (empty($this->screen_tab_template->alias_framework->data_source_delete)) ? null : $this->screen_tab_template->alias_framework->data_source_delete
                    ]*/
                ]
            ),
            [
                'class' => 'col-sm-' . $width . ' stats-section',
                'data' => [
                    'row' => $configuration['row_num'],
                    'col' => $configuration['col_num']
                ]
            ]
        );
    }

    /**
     * Getting section HTML code
     * @param $configuration
     * @return string
     */
    public function renderSectionContent($configuration)
    {
        $widget = $this->getWidget($configuration['layout_type']);

        if (!empty($widget)) {
            //$cache = $this->_cache;
            //$this->_cache = false;

            /*if (!empty($this->screen_tab_template->search_custom_query)) {
                $searchConfiguration = $this->screen_tab_template->search_custom_query;
            } else {
                $searchConfiguration = $this->screen_tab_template->search_configuration;
            }*/

            return $widget::widget([
                //'lib_name' => $this->screen_lib,
                'configuration' => $configuration,
                '_mode' => $this->_mode,
                //'_data_id' => $this->_data_id,
                //'_cache' => $cache,
                //'_last_found_data' => $this->_last_found_data,
                //'_search_configuration' => $searchConfiguration,
                //'_alias_framework' => $this->screen_tab_template->alias_framework
            ]);
        }
        return '';
    }

    public function getWidget($layoutType)
    {
        return (!empty($layoutType) && array_key_exists($layoutType, $this->_widget_class)) ? $this->_widget_class[$layoutType] : null;
    }

    /**
     * Generate random color rgba
     * 
     * @param array $userColors
     * @param int $index
     *
     * @return string
     */
    public static function getColor($userColors = [], $index = 0)
    {
        $colors = [
            '#FF6384',
            '#36A2EB',
            '#FFCE56',
            '#4BC0C0',
            '#9966FF',
            '#FF9F40',
            '#6600CC',
            '#006633',
            '#66B2FF',
            '#CCFFE5'

        ];
        $userColors += $colors;
        return $userColors[$index] ? $userColors[$index] : $userColors[0];
    }
}