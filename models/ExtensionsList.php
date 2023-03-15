<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 */

namespace app\models;

use Yii;

class ExtensionsList extends AccountModel
{
    public static $dataLib = 'CodiacSDK.CommonArea.dll';
    public static $dataAction = 'GetExtCallStack';

    //Change controller of API server
    protected static function getSourceLink()
    {
        if (!empty(Yii::$app->session['apiEndpointCustom'])) {
            return Yii::$app->session['apiEndpointCustom'];
        }
        return (YII_ENV == 'dev') ? Yii::$app->params['apiEndpointCustomDev'] : Yii::$app->params['apiEndpointCustom'];
    }

    /**
     * @param null|string $libName
     * @param array $functionName
     *
     * @return null|static
     */
    public static function getList($libName, $functionName)
    {
        $postData = [
            "func_name" => self::$dataAction,
            "func_param" => [
                "datasource_func" => $functionName,
                "datasource_lib" => $libName
            ],
            "lib_name" => self::$dataLib
        ];

        $result = (new static())->processData($postData);
        return (!empty($result['record_list'])) ? $result['record_list'] : null;
    }

    public static function getJobList()
    {
        $postData = [
            "func_name" => self::$dataAction,
            "func_param" => [
                "func_extensions_job" => null
            ],
            "lib_name" => self::$dataLib
        ];

        $result = (new static())->processData($postData);
        return (!empty($result['record_list'])) ? $result['record_list'] : null;
    }

	public static function getTableList()
    {
        $postData = [
            "func_name" => 'GetTablesNames',
            "func_param" => [
				"" => ""
			],
            "lib_name" => 'CodiacSDK.FileImport.dll'
        ];

        $result = (new static())->processData($postData);
        return (!empty($result['record_list'])) ? $result['record_list'] : null;
    }

	public static function getDataTypes()
    {
        $postData = [
            "func_name" => 'GetAvailableDatabaseDataTypes',
            "func_param" => [
				"" => ""
			],
            "lib_name" => 'CodiacSDK.CommonArea.dll'
        ];

        $result = (new static())->processData($postData);
        return (!empty($result['record_list'])) ? $result['record_list'] : null;
    }

	public static function getTableColumns($table_name)
    {
        $postData = [
            "func_name" => "GetExtendedTableInfo",
            "func_param" => [
				"table_names" => array($table_name)
			],
            "lib_name" => "CodiacSDK.CommonArea.dll"
        ];

        $result = (new static())->processData($postData);
		return (!empty($result['record_list'])) ? $result['record_list'] : null;
    }

	public static function getTableData($post)
    {
		//echo "<pre>"; print_r($post);

		$func_params = $post['func_param'];

		//echo "<pre>"; print_r($func_params);

		if($post['searchType'] == 'simple') {
			$field_name_list = $func_params['func_inparam_configuration'];
			$field_outlist = $func_params['pk_configuration'];
			$search_val = $post['query'];
			$func_name = $func_params['data_source_get'];

			$cols = array();

			foreach($field_name_list as $field_name) {
				$cols[$field_name][] = $search_val;
			}

			$postData = [
				"func_name" => $func_name,
				"func_param" => [
					"field_name_list" => $field_name_list,
					"field_out_list" => array_merge($field_name_list, $field_outlist),
					"search_mask_list" => $cols,
					"limitnum" => 15,
				],
				"lib_name" => $post['libName']
			];
		} else if($post['searchType'] == 'multi') {
			$pk = $func_params['query_pk'];
			$sql_params = $func_params['query_params'][0]['name'];
			$search_val = $post['query'];

			$postData = [
				"func_name" => "ExecuteCustomQuery",
				"func_param" => [
					"PK" => "$pk",
					"sql_params" => [
						":$sql_params" => "$search_val"
					]
				],
				"lib_name" => "CodiacSDK.CommonArea.dll"
			];
		}

		//echo "<pre>"; print_r(json_encode($postData)); die;

        $result = (new static())->processData($postData);

		//echo "<pre>"; print_r($result); die;

		return (!empty($result['record_list'])) ? $result['record_list'] : null;
    }

}
