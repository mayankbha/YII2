<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 */

namespace app\modules\admin\models\forms;

use Yii;
use yii\base\Model;

class ScreenForm extends Model
{
    const DEVICE_DESKTOP = 'D';
    const DEVICE_MOBILE = 'M';
    const DEVICE_WEB = 'W';

    const SIMPLE_SEARCH_TYPE = 'simple';
    const CUSTOM_SEARCH_TYPE = 'custom';

    public static $devices = [
        self::DEVICE_DESKTOP => 'Desktop',
        self::DEVICE_MOBILE => 'Mobile',
        self::DEVICE_WEB => 'Web',
    ];

    public static $defaultSort = [
        'screen_name' => SORT_ASC,
        'screen_tab_weight' => SORT_ASC,
        'screen_tab_name' => SORT_ASC,
    ];

    public static $typeLabels = [
        1 => 'Header 2x2',
        2 => 'Header 2x3',
        3 => '2x2',
        4 => '2x3',
        5 => '1x1',
        6 => 'Header 1x1',
        7 => 'Header 1x2',
        8 => '1x2',
        9 => 'Header 2x1',
        10 => '2x1',
    ];

    public static $types = [
        1 => [
            'header' => true,
            'row_count' => 2,
            'col_count' => 2
        ],
        2 => [
            'header' => true,
            'row_count' => 3,
            'col_count' => 2
        ],
        3 => [
            'header' => false,
            'row_count' => 2,
            'col_count' => 2
        ],
        4 => [
            'header' => false,
            'row_count' => 3,
            'col_count' => 2
        ],
        5 => [
            'header' => false,
            'row_count' => 1,
            'col_count' => 1
        ],
        6 => [
            'header' => true,
            'row_count' => 1,
            'col_count' => 1
        ],
        7 => [
            'header' => true,
            'row_count' => 1,
            'col_count' => 2
        ],
        8 => [
            'header' => false,
            'row_count' => 1,
            'col_count' => 2
        ],
        9 => [
            'header' => true,
            'row_count' => 2,
            'col_count' => 1
        ],
        10 => [
            'header' => false,
            'row_count' => 2,
            'col_count' => 1
        ],
    ];

    public static $dependentTypes = [
        1 => [2],
        2 => [],
        3 => [1, 2, 4],
        4 => [2],
        5 => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10],
        6 => [1, 2, 7, 9],
        7 => [1, 2],
        8 => [1, 2, 3, 4, 7],
        9 => [1, 2],
        10 => [1, 2, 3, 4, 9]
    ];


    public $pk;
    public $id;
    public $screen_desc;
    public $screen_lib;
    public $screen_name;
    public $screen_tab_devices;
    public $screen_tab_name;
    public $screen_tab_template;
    public $screen_tab_text;
    public $screen_tab_weight;

    public $menu_name;

    public function init()
    {
        parent::init();
        $this->screen_tab_template = self::getDefaultScreenTabTemplate();
    }

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['screen_lib', 'screen_name', 'screen_tab_devices', 'screen_tab_text', 'screen_tab_weight'], 'required', 'message'=> Yii::t('app', 'Please fill out this field.')],
            [['id', 'screen_tab_weight'], 'integer'],
            [['screen_desc', 'screen_tab_name'], 'string'],
            [['screen_tab_template'], 'safe'],
            [['screen_tab_weight'], 'default', 'value' => '0'],
            [['screen_tab_text'], 'string', 'max' => 15],

            ['menu_name', 'safe']
        ];
    }

    /**
     * @return array customized attribute labels
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'screen_desc' => Yii::t('app', 'Description'),
            'screen_lib' => Yii::t('app', 'Library'),
            'screen_name' => Yii::t('app', 'Screen name'),
            'screen_tab_devices' => Yii::t('app', 'Tab devices'),
            'screen_tab_name' => Yii::t('app', 'Tab name'),
            'screen_tab_text' => Yii::t('app', 'Tab text'),
            'screen_tab_weight' => Yii::t('app', 'Tab weight'),
            'screen_tab_template' => Yii::t('app', 'Template'),
            'menu_name' => Yii::t('app', 'Menu name'),
        ];
    }

    public static function getDefaultScreenTabTemplate()
    {
        return json_decode('{
            "layout_type": null,
            "search_configuration": null,
            "search_custom_query": null,
            "template_layout": null,
            "screen_extensions": {
                "add": {
                    "pre": null,
                    "post": null
                },
                "delete": {
                    "pre": null,
                    "post": null
                },
                "edit": {
                    "pre": null,
                    "post": null
                },
                "inquire": {
                    "pre": null,
                    "post": null
                },
                "execute": {
                    "pre": null,
                    "post": null
                },
                "executeFunction": {
                    "library": null,
                    "function": null,
                    "custom": null
                }
            },
            "step_screen": {
                "enable": false,
                "icon": null
            },
            "alias_framework": {
                "request_primary_table": null,
                "data_source_insert": null,
                "data_source_update": null,
                "data_source_delete": null
            }
        }');
    }
}