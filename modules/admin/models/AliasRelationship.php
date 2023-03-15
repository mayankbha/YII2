<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 */

namespace app\modules\admin\models;

use app\models\BaseModel;

use app\modules\admin\models\forms\AliasRelationshipForm;

use yii\helpers\ArrayHelper;

class AliasRelationship extends BaseModel
{
    public static $dataLib = 'CodiacSDK.AliasScreens.dll';
    public static $dataAction = 'GetAliasRelationshipTableList';

    public static $formClass = AliasRelationshipForm::class;

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
                "field_name_list" => ["ChildTable", "ChildField", "ParentTable" , "ParentField"],
				"field_value_list" => [
					"ChildTable" => [$id_explode[0]],
					"ChildField" => [$id_explode[1]],
					"ParentTable" => [$id_explode[2]],
					"ParentField" => [$id_explode[3]]
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
				"PK" => $id_explode[0].';'.$id_explode[1].';'.$id_explode[2].';'.$id_explode[3],
				"patch_json" => [
					"ChildTable" => $post['AliasRelationshipForm']['ChildTable'],
					"ChildField" => $post['AliasRelationshipForm']['ChildField'],
					"ParentTable" => $post['AliasRelationshipForm']['ParentTable'],
					"ParentField" => $post['AliasRelationshipForm']['ParentField']
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