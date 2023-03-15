<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 */

namespace app\modules\admin\models;

use app\models\BaseModel;

use app\modules\admin\models\forms\AliasDependencyForm;

use yii\helpers\ArrayHelper;

class AliasDependency extends BaseModel
{
    public static $dataLib = 'CodiacSDK.AliasScreens.dll';
    public static $dataAction = 'GetAliasTableDependencyList';

    public static $formClass = AliasDependencyForm::class;

    public static function getAliasTableDependencyList()
    {
        $postData = [
            'func_name' => self::$dataAction,
            'func_param' => [
                "field_name_list" => [],
				"field_value_list" => []
            ],
			'lib_name' => self::$dataLib
        ];

        if (($result = (new static())->processData($postData)) && isset($result['record_list'])) {
            return $result['record_list'];
        }

        return null;
	}

	public static function createAliasDependency($post)
    {
		$dependentsOn = '';

		$existList = array();

		$i = 0;

		foreach($post['AliasDependencyForm']['RequestTable'] as $key => $val) {
			if($dependentsOn == '') {
				$existList[$i] = $post['AliasDependencyForm']['DependentsOn'][$key];

				$i++;
			} else {
				if(!in_array($post['AliasDependencyForm']['DependentsOn'][$key], $existList)) {
					$existList[$i] = $post['AliasDependencyForm']['DependentsOn'][$key];

					$i++;
				}
			}

			$dependentsOn = $post['AliasDependencyForm']['DependentsOn'][$key];
		}

		$postData = [
			"func_name" => "CreateAliasTableDependency",
			"func_param" => [
				"patch_json" => [
					"AliasType" => $post['AliasDependencyForm']['AliasType'],
					"AliasTable" => $post['AliasDependencyForm']['AliasDatabaseTable'],
					"DependencyType" => $post['AliasDependencyForm']['DependencyType'],
					"RequestTable" => $val,
					"DependentsOn" => implode(';', $existList)
				]
			],
			"lib_name" => self::$dataLib
		];

		$result = (new static())->processData($postData);

		return $result;
	}

	public static function createSingleAliasDependency($postData)
    {
		$postNewData = [
			"func_name" => "CreateAliasTableDependency",
			"func_param" => [
				"patch_json" => [
					"AliasType" => $postData['AliasType'],
					"AliasTable" => $postData['AliasTable'],
					"DependencyType" => $postData['DependencyType'],
					"RequestTable" => $postData['RequestTable'],
					"DependentsOn" => $postData['DependentsOn']
				]
			],
			"lib_name" => self::$dataLib
		];

		$result = (new static())->processData($postNewData);

		return $result;
	}

	public static function updateAliasDependency($id, $post)
    {
		$id_explode = explode(';', $id);

		$existing_lists = Alias::getAliasTableDependency($id);
		$existingDependentsOn = explode(';', $existing_lists[0]['DependentsOn']);

		$dependentsOn = '';

		$existList = array();

		$i = 0;

		foreach($post['AliasDependencyForm']['RequestTable'] as $key => $val) {

			if (array_key_exists($key, $existingDependentsOn) && array_key_exists($key, $post['AliasDependencyForm']['DependentsOn'])) {
				if($dependentsOn == '') {
					$existList[$i] = $post['AliasDependencyForm']['DependentsOn'][$key];

					$i++;
				} else if($dependentsOn != $post['AliasDependencyForm']['DependentsOn'][$key]) {
					$existList[$i] = $post['AliasDependencyForm']['DependentsOn'][$key];

					$i++;
				}
			} else {
				if(!in_array($post['AliasDependencyForm']['DependentsOn'][$key], $existList)) {
					$existList[$i] = $post['AliasDependencyForm']['DependentsOn'][$key];

					$i++;
				}
			}

			$dependentsOn = $post['AliasDependencyForm']['DependentsOn'][$key];
		}

		$postData = [
			"func_name" => "UpdateAliasTableDependency",
			"func_param" => [
				"PK" => $id_explode[0].';'.$id_explode[1].';'.$val,
				"account_type" => $_SESSION['screenData']["app\models\UserAccount"]->account_type,
				"alias_framework_info" => null,
				"lock_id" => 1,
				"patch_json" => [
					"AliasType" => $post['AliasDependencyForm']['AliasType'],
					"AliasTable" => $post['AliasDependencyForm']['AliasDatabaseTable'],
					"DependencyType" => $post['AliasDependencyForm']['DependencyType'],
					"RequestTable" => $val,
					"DependentsOn" => implode(';', $existList)
				]
			],
			"lib_name" => self::$dataLib
		];

		$result = (new static())->processData($postData);

		return $result;
	}

	public static function getAliasTableDependency($id)
    {
		$id_explode = explode(';', $id);

        $postData = [
            'func_name' => self::$dataAction,
            'func_param' => [
                "field_name_list" => ["AliasType", "AliasTable"],
				"field_value_list" => [
					"AliasType" => ["$id_explode[0]"],
					"AliasTable" => ["$id_explode[1]"]
				]
            ],
			'lib_name' => self::$dataLib,
        ];

        $result = (new static())->processData($postData);

		return (!empty($result['record_list'])) ? $result['record_list'] : null;
	}

	public static function deleteAliasDependency($id)
    {
		$list = self::getAliasTableDependency($id);

		foreach($list as $row) {
			$postData = [
				"func_name" => "DeleteAliasTableDependency",
				"func_param" => [
					"PK" => $row['AliasType'].';'.$row['AliasTable'].';'.$row['RequestTable']
				],
				"lib_name" => self::$dataLib
			];

			$result = (new static())->processData($postData);

			if ($result['requestresult'] == 'unsuccessfully') {
				return $result;
			}
		}
	}
}