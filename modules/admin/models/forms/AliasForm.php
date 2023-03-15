<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 */

namespace app\modules\admin\models\forms;

use Yii;
use yii\base\Model;
use app\models\GetAliasList;

class AliasForm extends Model
{
    public $AliasCode;
    public $AliasType;
    public $AliasDescription;
    public $AliasInfo;
    public $AliasFormat = '';
    public $AliasFormatType = '';
    public $AliasEdits = '';
    public $AliasDatabaseTable = '';
    public $AliasDatabaseField = '';
    public $DefalutGroupUserIsNoAccess = '';
    public $DefaultValueIsNoAccess = '';
    public $AliasModule;
    public $AliasSQLStatement;
    public $pk;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['AliasCode', 'AliasType'], 'required'],
            [['AliasDescription', 'AliasInfo', 'AliasFormat', 'AliasModule', 'AliasSQLStatement'], 'string']
        ];
    }

    /**
     * @return array customized attribute labels
     */
    public function attributeLabels()
    {
        return [
            'AliasCode' => Yii::t('app', 'Alias Code'),
            'AliasType' => Yii::t('app', 'Type'),
            "AliasDescription" => Yii::t('app', 'Description'),
            "AliasInfo" => Yii::t('app', 'Info'),
            "AliasFormat" => Yii::t('app', 'Format'),
            "AliasFormatType" => Yii::t('app', 'Format Type'),
            "AliasEdits" => Yii::t('app', 'Edits'),
            "AliasDatabaseTable" => Yii::t('app', 'Database Table'),
            "AliasDatabaseField" => Yii::t('app', 'Database Field'),
            "DefalutGroupUserIsNoAccess" => Yii::t('app', 'Default Group/User is no access'),
            "DefaultValueIsNoAccess" => Yii::t('app', 'Default Value is no access'),
            "AliasModule" => Yii::t('app', 'Module Evaluation'),
            "AliasSQLStatement" => Yii::t('app', 'SQL Statement'),
        ];
    }

    /**
     * @return array customized tab labels
     */
    public function tabLabels()
    {
        return [
            'Alias' => Yii::t('app', 'Alias Details'),
            'AliasDependency' => Yii::t('app', 'Alias Dependency'),
            "AliasSecuritySpec" => Yii::t('app', 'Security Spec'),
            "SpecialAccessRestriction" => Yii::t('app', 'Special Access Restrictions'),
            "AliasRestriction" => Yii::t('app', 'Alias Restriction'),
        ];
    }

    public function handleAliasSubdetails($request, $method, $pk = null)
    {
        $sd = [
            "AliasDependency" => "DependentsOn",
            "AliasSecuritySpec" => "SecurityField",
            "SpecialAccessRestriction" => "UserGroupValue",
            "AliasRestriction" => "UserGroup"
        ];
        $sd_save_status = [];
        foreach ($sd as $d => $dv) {
            if ($method != 'delete') {
                if ($request->post($d . 'Form') != null) {
                    $post_data = $request->post($d . 'Form');
                    $func_prefix = ($method == "mixed" ? $post_data['method'] : $method);
                    unset($post_data['method']);
                    foreach ($post_data[$dv] as $k => $v) {
                        $func_pref = ucwords(is_array($func_prefix) ? $func_prefix[$k] : $func_prefix);
                        switch ($d) {
                            case 'AliasDependency':
                                $sd_fields = [
                                    'AliasCode' => $request->post('AliasForm')["AliasCode"],
                                    'RequestTable' => $post_data['RequestTable'][$k],
                                    'DependentsOn' => $post_data['DependentsOn'][$k]
                                ];
                                break;
                            case 'AliasSecuritySpec':
                                $sd_fields = [
                                    'AliasCode' => $request->post('AliasForm')["AliasCode"],
                                    'Tenant' => $post_data['Tenant'][$k],
                                    'AccountType' => $post_data['AccountType'][$k],
                                    'UserType' => $post_data['UserType'][$k],
                                    'SecurityField' => $post_data['SecurityField'][$k]
                                ];
                                break;
                            case 'SpecialAccessRestriction':
                                $sd_fields = [
                                    'AliasCode' => $request->post('AliasForm')["AliasCode"],
                                    'Entity' => $post_data['Entity'][$k],
                                    'UserGroupValue' => $post_data['UserGroupValue'][$k],
                                    'Rights' => $post_data['Rights'][$k]
                                ];
                                if ($func_pref == 'Delete') {
                                    $sd_e_fields = ['Id' => $post_data['Id'][$k]];
                                    $sd_fields = array_merge($sd_fields, $sd_e_fields);
                                }
                                break;
                            case 'AliasRestriction':
                                $sd_fields = [
                                    'AliasCode' => $request->post('AliasForm')["AliasCode"],
                                    'Entity' => $post_data['Entity'][$k],
                                    'Rights' => $post_data['Rights'][$k],
                                    'UserGroup' => $post_data['UserGroup'][$k],
                                    'Value' => $post_data['Value'][$k]
                                ];
                                if ($func_pref == 'Delete') {
                                    $sd_e_fields = ['Id' => $post_data['Id'][$k]];
                                    $sd_fields = array_merge($sd_fields, $sd_e_fields);
                                }
                                break;
                        }
                        $sd_save_status[$d] = GetAliasList::callAPI($func_pref . $d, $sd_fields);
                        /*echo print_r($sd_fields)."<br />";
                        echo var_dump($func_pref.$d)." --> ".var_dump($sd_save_status)."<br />";*/
                        //if(GetAliasList::callAPI($func_pref.$d, $sd_fields) == false){echo var_dump($func_pref.$d);exit;} 
                    }
                }
            } else {
                if ($pk != null) {
                    $del_data = ['AliasCode' => $pk];
                    $post_data = $request->post($d . 'Form');
                    //$del_data = array_merge($del_data, $post_data);
                    if ($d == "AliasSecuritySpec") {
                        echo print_r($del_data);
                        exit;
                    }
                    /*$del_obj = GetAliasList::callAPI(ucwords("get").$d."List", $reqFields, $current_page, $limit, "", $reqFieldCol);
                    $del_arr = GetAliasList::jsonToArray($del_obj);
                    //echo print_r($del_arr);exit;
                    foreach ($del_obj as $key => $value) {
                        $del_data[$key] = $value;
                    }*/
                    $sd_save_status[$d] = GetAliasList::callAPI(ucwords($method) . $d, $del_data);
                }
            }
        } // end for
        //exit;
        return $sd_save_status;
    }

}