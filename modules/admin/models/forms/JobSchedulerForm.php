<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 */

namespace app\modules\admin\models\forms;

use Yii;
use yii\base\Model;

class JobSchedulerForm extends Model
{
	const TYPE_ACTIVE = 'Y';
    const TYPE_INACTIVE = 'N';

	const SIMPLE_SEARCH_TYPE = 'simple';
    const CUSTOM_SEARCH_TYPE = 'custom';

	public static $types = [
        self::TYPE_ACTIVE => 'Active',
        self::TYPE_INACTIVE => 'Inactive'
    ];

	public static $screen_types = [
        1 => [
            'header' => false,
            'row_count' => 1,
            'col_count' => 1
        ]
    ];

	public $id;
    public $job_name;
    public $job_description;
    public $is_active;
    public $launch_type;
    public $launch_condition;
    public $launch_params;
	public $jobs_params;
	public $template_layout;

	public function init()
    {
        parent::init();
        $this->jobs_params = self::getDefaultJobParams();
		$this->template_layout = array();

		//$this->template_layout = array('template_layout' => null);
    }

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['job_name', 'job_description', 'is_active', 'launch_type', 'launch_condition'], 'required'],
            [['launch_params'], 'string'],
			[['jobs_params', 'template_layout'], 'safe'],
        ];
    }

    /**
     * @return array customized attribute labels
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'job_name' => Yii::t('app', 'Job Name'),
            'job_description' => Yii::t('app', 'Job Description'),
            'is_active' => Yii::t('app', 'Status'),
            'launch_type' => Yii::t('app', 'Launch Type'),
            'launch_condition' => Yii::t('app', 'Launch Condition'),
            'launch_params' => Yii::t('app', 'Launch Params'),
            'jobs_params' => Yii::t('app', 'Jobs Params')
        ];
    }

	public static function getDefaultJobParams()
    {
       return json_decode('{
			"function_extensions_job_params": {
				"lib_name": null,
				"func_name": null,
				"alias_framework_info": {
					"request_primary_table": null,
					"data_source_insert": null,
					"data_source_update": null,
					"data_source_delete": null,
					"enable": true
				},
				"search_custom_query": null,
				"search_function_info": {
					"config": null
				}
			}
        }');
    }
}