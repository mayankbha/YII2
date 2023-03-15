<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 */

namespace app\modules\admin\models;

use app\models\BaseModel;
use app\modules\admin\models\forms\AliasForm;

class Alias extends BaseModel
{
    public static $dataLib = 'CodiacSDK.AliasScreens.dll';
    public static $dataAction = 'GetAliasTableDependencyList';

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
		//echo "<pre>"; print_r($post);

		//echo "<pre>"; print_r($post['AliasDependencyForm']);

		$dependentsOn = '';

		$existList = array();

		$i = 0;

		foreach($post['AliasDependencyForm']['RequestTable'] as $key => $val) {
			//echo 'dependentsOn :: '.$dependentsOn.'<br><br>';

			if($dependentsOn == '') { //echo 'DependentsOn is blank <br><br>';
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

		//echo "<pre>"; print_r($postData);

		$result = (new static())->processData($postData);

		return $result;
	}

	public static function createSingleAliasDependency($postData)
    {
		//echo "<pre>"; print_r($postData);

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

		//echo "<pre>"; print_r($postNewData);

		$result = (new static())->processData($postNewData);

		return $result;
	}

	public static function updateAliasDependency($id, $post)
    {
		//echo "<pre>"; print_r($post);

		//echo "<pre>"; print_r($post['AliasDependencyForm']);

		$id_explode = explode(';', $id);

		$existing_lists = Alias::getAliasTableDependency($id);
		$existingDependentsOn = explode(';', $existing_lists[0]['DependentsOn']);

		//echo "<pre>"; print_r($existing_lists);

		//echo "<pre>"; print_r($existingDependentsOn);

		$dependentsOn = '';

		$existList = array();

		$i = 0;

		foreach($post['AliasDependencyForm']['RequestTable'] as $key => $val) {
			//echo 'dependentsOn :: '.$dependentsOn.'<br><br>';

			if (array_key_exists($key, $existingDependentsOn) && array_key_exists($key, $post['AliasDependencyForm']['DependentsOn'])) { //echo 'key exist <br><br>';
				if($dependentsOn == '') { //echo 'DependentsOn is blank <br><br>';
					$existList[$i] = $post['AliasDependencyForm']['DependentsOn'][$key];

					$i++;
				} else if($dependentsOn != $post['AliasDependencyForm']['DependentsOn'][$key]) { //echo 'DependentsOn is different <br><br>';
					$existList[$i] = $post['AliasDependencyForm']['DependentsOn'][$key];

					$i++;
				}
			} else { //echo 'add DependentsOn <br><br>';
				if(!in_array($post['AliasDependencyForm']['DependentsOn'][$key], $existList)) {
					$existList[$i] = $post['AliasDependencyForm']['DependentsOn'][$key];

					$i++;
				}
			}

			$dependentsOn = $post['AliasDependencyForm']['DependentsOn'][$key];
		}

		/*echo "<pre>"; print_r($existList);

		foreach($existingDependentsOn as $existKey => $exist) {
			if (!in_array($exist, $existList)) {
				$postData = [
					"func_name" => "DeleteAliasTableDependency",
					"func_param" => [
						"PK" => $post['AliasDependencyForm']['AliasType'].';'.$post['AliasDependencyForm']['AliasDatabaseTable'].';'.$post['AliasDependencyForm']['RequestTable'][0]
					],
					"lib_name" => self::$dataLib
				];

				echo "<pre>"; print_r($postData);

				//$result = (new static())->processData($postData);
			}
		}*/

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

		//echo "<pre>"; print_r($postData);

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
                //"field_name_list" => ["AliasType", "AliasTable", "DependencyType"],
				"field_value_list" => [
					"AliasType" => ["$id_explode[0]"],
					"AliasTable" => ["$id_explode[1]"]
					//"DependencyType" => ["$id_explode[2]"]
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

		foreach($list as $row) { //echo $row['AliasType'];
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

	public static function getAliasRelationshipTableList()
    {
        $postData = [
            'func_name' => "GetAliasRelationshipTableList",
            'func_param' => [
                "field_name_list" => [],
				"field_value_list" => []
            ],
			'lib_name' => self::$dataLib,
        ];

		$result = (new static())->processData($postData);

        if (($result = (new static())->processData($postData)) && isset($result['record_list'])) {
            return $result['record_list'];
        }

        return null;
	}

	public static function createAliasRelationship($post)
    {
		//echo "<pre>"; print_r($post);

		$postData = [
			"func_name" => "CreateAliasRelationshipTable",
			"func_param" => [
				"patch_json" => [
					"ParentTable" => $post['AliasRelationshipForm']['ParentTable'],
					"ParentField" => $post['AliasRelationshipForm']['ParentField'],
					"ChildTable" => $post['AliasRelationshipForm']['ChildTable'],
					"ChildField" => $post['AliasRelationshipForm']['ChildField']
				]
			],
			"lib_name" => self::$dataLib
		];

		$result = (new static())->processData($postData);

		//echo "<pre>"; print_r($result); die;

		return $result;
	}

	public static function getAliasRelationship($id)
    {
		$id_explode = explode(';', $id);

        $postData = [
            'func_name' => "GetAliasRelationshipTableList",
            'func_param' => [
                "field_name_list" => ["ParentTable", "ParentField", "ChildTable" , "ChildField"],
				"field_value_list" => [
					"ParentTable" => [$id_explode[0]],
					"ParentField" => [$id_explode[1]],
					"ChildTable" => [$id_explode[2]],
					"ChildField" => [$id_explode[3]]
				]
            ],
			'lib_name' => self::$dataLib,
        ];

		$result = (new static())->processData($postData);

		return (!empty($result['record_list'])) ? $result['record_list'] : null;
	}

	public static function updateAliasRelationship($post, $id)
    {
		$id_explode = explode(';', $id);

		//echo "<pre>"; print_r($post);

		$postData = [
			"func_name" => "UpdateAliasRelationshipTable",
			"func_param" => [
				"PK" => $id_explode[2].';'.$id_explode[3],
				"patch_json" => [
					"ParentTable" => $post['AliasRelationshipForm']['ParentTable'],
					"ParentField" => $post['AliasRelationshipForm']['ParentField'],
					"ChildTable" => $post['AliasRelationshipForm']['ChildTable'],
					"ChildField" => $post['AliasRelationshipForm']['ChildField']
				]
			],
			"lib_name" => self::$dataLib
		];

		$result = (new static())->processData($postData);

		return $result;
	}

	public static function deleteAliasRelationship($id)
    {
        $postData = [
            "func_name" => "DeleteAliasRelationshipTable",
            "func_param" => [
				"PK" => $id
			],
            "lib_name" => self::$dataLib
        ];

        $result = (new static())->processData($postData);

		return $result;
	}

}