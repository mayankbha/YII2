<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 */

namespace app\models;

use Yii;
use app\modules\admin\models\forms\AliasForm;
use yii\helpers\ArrayHelper;
use yii\data\ArrayDataProvider;
//use yii\widgets\LinkPager;
use yii\widgets\LinkSorter;
use yii\helpers\Url;
use yii\helpers\Html;

class GetAliasList extends BaseModel
{
    public static $dataLib = 'CodiacSDK.CommonArea.dll';
    public static $dataAction = 'GetAliasList';
    public static $formClass = AliasForm::class;

    const DATA_LIB = 'CodiacSDK.CommonArea.dll';
    const API_DATA_LIB = 'CodiacSDK.AliasScreens.dll';

    //Change controller of API server
    protected static function getSourceLink()
    {
        if (!empty(Yii::$app->session['apiEndpointCustom'])) {
            return Yii::$app->session['apiEndpointCustom'];
        }
        return (YII_ENV == 'dev') ? Yii::$app->params['apiEndpointCustomDev'] : Yii::$app->params['apiEndpointCustom'];
    }

    public static function getAliasTypes()
    {
        return [
            "Database Field",
            "Array",
            "Custom Generated",
            "Custom Multi",
            "List Entry"
        ];
    }

    public static function getAliasTypesDropdown()
    {
        $dropdown = [];
        foreach (self::getAliasTypes() as $k => $v):
            $dropdown[$v] = $v;
        endforeach;
        return $dropdown;
    }

    public static function getFields()
    {
        $form = new AliasForm();
        return $form->attributeLabels();
    }

    public static function getByNames(array $names)
    {
        $lists = [];
        if (($model = self::getData(['list_name' => $names])) && !empty($model->list)) {
            $lists = ArrayHelper::index($model->list, null, 'list_name');
        }

        return array_merge(array_fill_keys($names, []), $lists);
    }

    public static function getArrayForSelectByNames(array $names, $concatListName = true, $concatDescription = true)
    {
        $lists = self::getByNames($names);
        foreach ($lists as $listName => $data) {
            $tmp = [];
            foreach ($data as $listData) {
                $key = ($concatListName) ? "$listName.{$listData['entry_name']}" : $listData['entry_name'];
                $value = ($concatDescription && !empty($listData['description'])) ? "{$listData['entry_name']}: {$listData['description']}" : $listData['entry_name'];

                $tmp[$key] = $value;
            }
            $lists[$listName] = $tmp;
        }
        return $lists;
    }

    public static function callAPI(
        $func_name = "GetAliasList",
        $filter_by_value,
        $page = 1,
        $limitnum = 10,
        $col = "",
        $filter_by = "AliasType",
        $debug = false
    ) {
        $model = new static();
        $offsetnum = ($page - 1) * $limitnum;
        $colstr = "";
        $filter_by_str = "";
        $filter_by_val_str = "";
        $filter_by_val_arr_str = "";

        if (is_array($col)) {
            for ($c = 0; $c < count($col); $c++) {
                if ($c > 0) {
                    $colstr .= ',';
                }
                $colstr .= '"' . $col[$c] . '"';
            }
        }
        if (is_array($filter_by_value)) {
            for ($fv = 0; $fv < count($filter_by_value); $fv++) {
                if ($fv > 0) {
                    $filter_by_val_str .= ',';
                }
                if (isset($filter_by_value[$fv])) {
                    if (is_array($filter_by_value[$fv])) {
                        for ($fvs = 0; $fvs < count($filter_by_value[$fv]); $fvs++) {
                            if ($fvs > 0) {
                                $filter_by_val_str .= ',';
                            }
                            $filter_by_val_str .= '"' . $filter_by[$fv] . '":["' . $filter_by_value[$fv][0] . '"]';
                        }
                    } else {
                        $filter_by_val_str .= '"' . $filter_by_value[$fv] . '"';
                    }
                }
            }
        }
        if (is_array($filter_by)) {
            for ($f = 0; $f < count($filter_by); $f++) {
                if ($f > 0) {
                    $filter_by_str .= ',';
                }
                $filter_by_str .= '"' . $filter_by[$f] . '"';
            }
        }

        //print_r($filter_by);
        //print_r($filter_by); exit;
        $request = Yii::$app->request;
        $sessionData = $model->getSessionData();

        switch ($func_name):
            case 'ReloadAliases':
                $requestbody = '{
                    "func_name": "' . $func_name . '"
                  }';
                break;

            case 'GetAliasList':
            case 'GetAliasDependencyList':
            case 'GetSpecialAccessRestrictionList':
            case 'GetAliasRestrictionList':
            case 'GetAliasSecuritySpecList':
                /*$requestbody = [
                    'func_name'     =>     $func_name,
                    'func_param'     =>    [
                        'field_out_list'    =>    $field_out_list,
                        'field_name_list'    =>    $field_name_list,
                        'field_value_list'    =>    $field_value_list,
                        'offsetnum'    =>    $offsetnum,
                        'limitnum'    =>    $limitnum
                    ],
                    'lib_name'        =>    self::API_DATA_LIB
                ];*/

                $requestbody = '{
                    "func_name": "' . $func_name . '",
                    "func_param": {
                      ' . ($col != "" ? '"field_out_list": [' . ($colstr != "" ? $colstr : '"' . $col . '"') . '],' : '') . '
                      "field_name_list": [' . ($filter_by_str != "" ? $filter_by_str : '"' . $filter_by . '"') . '],
                      "field_value_list": { ' . ($filter_by_val_str != "" ? $filter_by_val_str : ('"' . $filter_by . '": ["' . $filter_by_value . '"]')) . ' },
                      ' . ($limitnum == 99999999 ? '"offsetnum": ' . $offsetnum . ',
                      "limitnum": ' . $limitnum : '') . '
                    },
                    "lib_name": "' . self::API_DATA_LIB . '"
                  }';
                break;

            case 'SearchAliasById':
                $requestbody = '{
                    "func_name": "SearchAlias",
                    "func_param": {
                      "field_name_list": ["AliasCode"],
                      "search_mask_list": {"AliasCode":["' . $filter_by_value . '"]},
                      "offsetnum": 0,
                      "limitnum": 1
                    },
                    "lib_name": "' . self::API_DATA_LIB . '"
                  }';
                break;
            case 'SearchAlias':
            case 'SearchAliasDependency':
            case 'SearchSpecialAccessRestriction':
            case 'SearchAliasRestriction':
            case 'SearchAliasSecuritySpec':
                $requestbody = '{
                    "func_name": "' . $func_name . '",
                    "func_param": {
                      "field_name_list": [' . ($filter_by_str != "" ? $filter_by_str : '"' . $filter_by . '"') . '],
                      "search_mask_list": { ' . ($filter_by_val_str != "" ? $filter_by_val_str : ('"' . $filter_by . '": ["' . $filter_by_value . '"]')) . ' },
                      "offsetnum": ' . $offsetnum . ',
                      "limitnum": ' . $limitnum . '
                    },
                    "lib_name": "' . self::API_DATA_LIB . '"
                  }';
                break;

            case 'CreateAlias':
                $patch_json = '';
                //echo "<pre>".print_r($request->post('AliasForm'))."</pre>";exit;
                $p = 0;
                $f = false;
                foreach ($request->post('AliasForm') as $key => $value) {
                    if ($key == 'DefalutGroupUserIsNoAccess' || $key == 'DefaultValueIsNoAccess') {
                        $value = ($value == 1 ? 'T' : 'F');
                    } else if ($key == 'AliasModule') {
                        $f = true;
                        if (empty($value)) {
                            $patch_json .= ', "AliasModuleType": ""';
                            $value = 'F';
                        } else {
                            $patch_json .= ', "AliasModuleType": "' . $value . '"';
                            $value = 'T';
                        }
                    }

                    if ($p > 0) {
                        $patch_json .= ', ';
                    }
                    if ($key == "AliasType") {
                        foreach ($value as $k => $v) {
                            $value = $v;
                        }
                    }
                    if ($key == 'AliasSQLStatement') {
                        $value = base64_encode($value);
                    }
                    $patch_json .= '"' . $key . '": "' . $value . '"';
                    $p++;
                }
                if (!$f) {
                    $patch_json .= ', "AliasModule": "F", "AliasModuleType": ""';
                }

                $requestbody = '{
                    "func_name": "' . $func_name . '",
                    "func_param": {
                      "patch_json": {
                        ' . $patch_json . '
                      }
                    },
                    "lib_name": "' . self::API_DATA_LIB . '"
                  }';
                break;
            case 'CreateAliasDependency':
            case 'CreateAliasSecuritySpec':
            case 'CreateAliasRestriction':
            case 'CreateSpecialAccessRestriction':
                $patch_json = '';
                //echo "<pre>".print_r($request->post('AliasForm'))."</pre>";exit;
                $p = 0;
                foreach ($filter_by_value as $key => $value) {
                    if ($p > 0) {
                        $patch_json .= ', ';
                    }
                    $patch_json .= '"' . $key . '": "' . $value . '"';
                    $p++;
                }
                $requestbody = '{
                    "func_name": "' . $func_name . '",
                    "func_param": { 
                      "patch_json": {
                        ' . $patch_json . '
                      }
                    },
                    "lib_name": "' . self::API_DATA_LIB . '"
                  }';
                break;
            case 'DeleteAlias':
            case 'DeleteAliasDependency':
            case 'DeleteSpecialAccessRestriction':
            case 'DeleteAliasRestriction':
                $requestbody = '{
                    "func_name": "' . $func_name . '",
                    "func_param": {
                        "PK": "' . ($func_name == 'DeleteAlias' ? $filter_by_value :
                        ($func_name == 'DeleteSpecialAccessRestriction' || $func_name == 'DeleteAliasRestriction' ? $filter_by_value['Id']
                            .
                            ($func_name == 'DeleteAliasRestriction' ? ";" . $request->post('AliasForm')['AliasCode'] : "")
                            :
                            ($request->post('AliasForm')['AliasCode'] . (isset($filter_by_value['RequestTable']) ? ';' . $filter_by_value['RequestTable'] : "")
                            )
                        )
                    ) . '"
                    },
                    "lib_name": "' . self::API_DATA_LIB . '"
                  }';
                break;
            case 'DeleteAliasSecuritySpec':
                $requestbody = '{
                    "func_name": "DeleteAliasSecuritySpec",
                    "func_param": {
                      "PK": "' . $filter_by_value['AliasCode'] . ';' . $filter_by_value['Tenant'] . ';' . $filter_by_value['AccountType'] . ';' . $filter_by_value['UserType'] . '"
                    },
                    "lib_name": "' . self::API_DATA_LIB . '"
                  }';
                break;
            case 'UpdateAlias':
                //echo var_dump(base64_decode($request->post('AliasForm')['AliasSQLStatement']), TRUE);exit;
                //print_r($request->post());exit;
                $patch_json = '';
                $p = 0;
                $update_data = $request->post('AliasForm');
                $f = false;
                foreach ($update_data as $key => $value) {
                    if ($key == 'DefalutGroupUserIsNoAccess' || $key == 'DefaultValueIsNoAccess') {
                        $value = ($value == 1 ? 'T' : 'F');
                    } else if ($key == 'AliasModule') {
                        $f= true;
                        if (empty($value)) {
                            $patch_json .= ', "AliasModuleType": ""';
                            $value = 'F';
                        } else {
                            $patch_json .= ', "AliasModuleType": "' . $value . '"';
                            $value = 'T';
                        }
                    }

                    if ($p > 0) {
                        $patch_json .= ', ';
                    }
                    if ($key == "AliasType") {
                        foreach ($value as $k => $v) {
                            $value = $v;
                        }
                    }
                    if ($key == 'AliasSQLStatement') {
                        $value = base64_encode($value);
                    }
                    $patch_json .= '"' . $key . '": "' . $value . '"';
                    $p++;
                }
                if (!$f) {
                    $patch_json .= ', "AliasModule": "F", "AliasModuleType": ""';
                }

                $requestbody = '{
                    "func_name": "UpdateAlias",
                    "func_param": {
                      "PK": "' . $update_data['AliasCode'] . '",
                      "account_type": "' . $_SESSION['screenData']["app\models\UserAccount"]->account_type . '",
                      "alias_framework_info": null,
                      "lock_id": "1",
                      "patch_json": {
                           ' . $patch_json . '
                      }
                    },
                    "lib_name": "' . self::API_DATA_LIB . '",
                    "search_function_info": null,
                    "security1": "",
                    "security2": "",
                    "tenant_code": "' . $_SESSION['screenData']["app\models\UserAccount"]->tenant_code . '",
                    "user_document_groups": "' . $_SESSION['screenData']["app\models\UserAccount"]->document_group . '",
                    "user_email": "bill@bill.com",
                    "user_groups": "' . $_SESSION['screenData']["app\models\UserAccount"]->group_area . '",
                    "user_name": "' . $_SESSION['screenData']["app\models\UserAccount"]->account_name . '",
                    "user_phone_number": "7246401000"
                  }';
                break;
            case 'UpdateAliasDependency':
                $requestbody = '{
                    "func_name": "UpdateAliasDependency",
                    "func_param": {
                      "PK": "' . $filter_by_value['AliasCode'] . ';' . $filter_by_value['RequestTable'] . '",
                      "account_type": "' . $_SESSION['screenData']["app\models\UserAccount"]->account_type . '",
                      "alias_framework_info": null,
                      "lock_id": "1",
                      "patch_json": {
                        "DependentsOn": "' . $filter_by_value['DependentsOn'] . '"
                      }
                    },
                    "lib_name": "' . self::API_DATA_LIB . '",
                    "search_function_info": null,
                    "security1": "",
                    "security2": "",
                    "tenant_code": "' . $_SESSION['screenData']["app\models\UserAccount"]->tenant_code . '",
                    "user_document_groups": "' . $_SESSION['screenData']["app\models\UserAccount"]->document_group . '",
                    "user_email": "bill@bill.com",
                    "user_groups": "' . $_SESSION['screenData']["app\models\UserAccount"]->group_area . '",
                    "user_name": "' . $_SESSION['screenData']["app\models\UserAccount"]->account_name . '",
                    "user_phone_number": "7246401000"
                  }';
                break;
            case 'UpdateAliasRestriction':
                $patch_json = '';
                $p = 0;
                foreach ($filter_by_value as $key => $value) {
                    if ($p > 0) {
                        $patch_json .= ', ';
                    }
                    $patch_json .= '"' . $key . '": "' . $value . '"';
                    $p++;
                }
                $requestbody = '{
                    "func_name": "UpdateAliasRestriction",
                    "func_param": {
                      "PK": "' . $filter_by_value['Id'] . ';' . $filter_by_value['AliasCode'] . '",
                      "account_type": "' . $_SESSION['screenData']["app\models\UserAccount"]->account_type . '",
                      "alias_framework_info": null,
                      "lock_id": "1",
                      "patch_json": {' . $patch_json . '}
                    },
                    "lib_name": "' . self::API_DATA_LIB . '",
                    "search_function_info": null,
                    "security1": "",
                    "security2": "",
                    "tenant_code": "' . $_SESSION['screenData']["app\models\UserAccount"]->tenant_code . '",
                    "user_document_groups": "' . $_SESSION['screenData']["app\models\UserAccount"]->document_group . '",
                    "user_email": "bill@bill.com",
                    "user_groups": "' . $_SESSION['screenData']["app\models\UserAccount"]->group_area . '",
                    "user_name": "' . $_SESSION['screenData']["app\models\UserAccount"]->account_name . '",
                    "user_phone_number": "7246401000"
                  }';
                break;
            case 'UpdateSpecialAccessRestriction':
                $patch_json = '';
                $p = 0;
                foreach ($filter_by_value as $key => $value) {
                    if ($p > 0) {
                        $patch_json .= ', ';
                    }
                    $patch_json .= '"' . $key . '": "' . $value . '"';
                    $p++;
                }
                $requestbody = '{
                    "func_name": "UpdateSpecialAccessRestriction",
                    "func_param": {
                      "PK": "' . $filter_by_value['Id'] . '",
                      "account_type": "' . $_SESSION['screenData']["app\models\UserAccount"]->account_type . '",
                      "alias_framework_info": null,
                      "lock_id": "1",
                      "patch_json": {' . $patch_json . '}
                    },
                    "lib_name": "' . self::API_DATA_LIB . '",
                    "search_function_info": null,
                    "security1": "",
                    "security2": "",
                    "tenant_code": "' . $_SESSION['screenData']["app\models\UserAccount"]->tenant_code . '",
                    "user_document_groups": "' . $_SESSION['screenData']["app\models\UserAccount"]->document_group . '",
                    "user_email": "bill@bill.com",
                    "user_groups": "' . $_SESSION['screenData']["app\models\UserAccount"]->group_area . '",
                    "user_name": "' . $_SESSION['screenData']["app\models\UserAccount"]->account_name . '",
                    "user_phone_number": "7246401000"
                  }';
                break;
            case 'UpdateAliasSecuritySpec':
                $requestbody = '{
                    "func_name": "UpdateAliasSecuritySpec",
                    "func_param": {
                      "PK": "' . $filter_by_value['AliasCode'] . ';' . $filter_by_value['Tenant'] . ';' . $filter_by_value['AccountType'] . ';' . $filter_by_value['UserType'] . '",
                      "account_type": "' . $_SESSION['screenData']["app\models\UserAccount"]->account_type . '",
                      "alias_framework_info": null,
                      "lock_id": "1",
                      "patch_json": {
                        "SecurityField": "' . $filter_by_value['SecurityField'] . '"
                      }
                    },
                    "lib_name": "' . self::API_DATA_LIB . '",
                    "search_function_info": null,
                    "security1": "",
                    "security2": "",
                    "tenant_code": "' . $_SESSION['screenData']["app\models\UserAccount"]->tenant_code . '",
                    "user_document_groups": "' . $_SESSION['screenData']["app\models\UserAccount"]->document_group . '",
                    "user_email": "bill@bill.com",
                    "user_groups": "' . $_SESSION['screenData']["app\models\UserAccount"]->group_area . '",
                    "user_name": "' . $_SESSION['screenData']["app\models\UserAccount"]->account_name . '",
                    "user_phone_number": "7246401000"
                  }';
                break;
            case 'GetTablesInfo':
                $requestbody = '{
                    "func_name": "GetTablesInfo",
                    "func_param": {
                      "field_name_list": [],
                      "field_value_list": []
                    },
                    "lib_name": "' . self::DATA_LIB . '"
                  }';
                break;
        endswitch;

        //echo $requestbody;

        if ($sessionData['secretKey'] && $sessionData['secretIv']) {
            $requestbody = $model->AesEncrypt(json_decode($requestbody), $sessionData['secretKey'], $sessionData['secretIv']);
            $requestbody = '"' . $requestbody . '"';
        }
        $data_string = '{
          "requestbody": ' . $requestbody . ',
          "sessionhandle": "' . Yii::$app->session['screenData']['sessionData']['sessionhandle'] . '"
        }';

        //echo $data_string;

        /*   echo "<pre>test".print_r($data_string);
           exit;*/
        /*if(!is_array($requestbody)) $requestbody = json_decode($requestbody);
        $postData = [
            'requestbody'         => $requestbody,
            'sessionhandle'     => $sessionData['sessionhandle']
        ];
        $model = new static();
        if($func_name!="ReloadAliases") $requestUrl = Yii::$app->params['apiEndpointCustom'];
        else $requestUrl = Yii::$app->params['apiEndpoint'];
        $attributes =  $model->processData($postData, $requestUrl, true);
        exit;
        $model->list = $attributes['record_list'];

        if (!empty($model->list)) {
            if (!empty($pkList) && !empty($postData['lib_name']) && !empty($postData['func_name'])) {
                foreach ($model->list as $key => $value) {
                    $pk = [];
                    foreach ($pkList as $item) {
                        if (!empty($value[$item])) $pk[] = $value[$item];
                    }
                    if ($pk = implode(';', $pk)) {
                        $model->list[$key]['pk'] = $pk;
                    }
                }
            }
            return $model;
        }*/
        /*$results = $model->getData([], $postData, [], true);
        echo "<pre>ttsets".var_dump($results);
        exit;*/


        /*if($debug==true){ 
            echo $data_string; exit; 
        }*/
        if (!isset($data_string)) {
            echo print_r($_REQUEST);
            echo $func_name;
            exit;
        }
        //echo print_r($func_name)."::::";


        if ($func_name != "ReloadAliases") {
            $ch = (YII_ENV == 'dev') ? curl_init(Yii::$app->params['apiEndpointCustomDev']) : curl_init(Yii::$app->params['apiEndpointCustom']);
        } else {
            $ch = (YII_ENV == 'dev') ? curl_init(Yii::$app->params['apiEndpointDev'])  : curl_init(Yii::$app->params['apiEndpoint']);
        }

        //if($func_name!="ReloadAliases") $ch = curl_init('http://192.168.100.211:34560/osoc/api/customcontroller');  

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data_string),
            'User-Agent: Mozilla/5.0 (Windows NT 6.3; rv:36.0) Gecko/20100101 Firefox/36.0',
            'Accept-Language: en-US,en;q=0.5'
        ));
        $result = curl_exec($ch);
        //echo print_r(curl_getinfo($ch));
        if ($func_name == "ReloadAliases") {
            echo print_r($result);
            //return var_dump(json_decode($result));
        }

        //echo var_dump($result);exit; 

        if (strstr($result, "Correspond Function Not Found") != false) {
            echo $data_string . " " . $func_name . "CURL: " . var_dump($result);
            exit;
        }

        if ($result != false) {
            if (curl_errno($ch)) {
                $info = curl_getinfo($ch);
                echo 'Took ', $info['total_time'], ' seconds to send a request to ', $info['url'], "\n";
                exit;
            }
            curl_close($ch);
            if (strstr($result, 'unsuccessfully')) {
                return false;
            } else {
                return $result;
            }
        } else {
            return false;
        }
    }

    public static function handleAliasSubdetails($request, $method)
    {
        AliasForm::handleAliasSubdetails($request, $method);
    }

    public static function customPager($pagecount, $limitnum, $filters = [], $tab = 0)
    {
        $url = Url::current();
        $url = explode("?", $url);
        $url = str_replace('index', 'ajax', $url[0]);
        $url = str_replace('ajax/ajax', 'ajax', $url);
        $pager = '';
        $filter_str = '';
        $active = ' class="active"';
        $aliasTypes = GetAliasList::getAliasTypes();
        if (!empty($filters)) {
            $f = 0;
            foreach ($filters as $key => $value) {
                $filter_str .= "&";
                $filter_str .= htmlentities(urlencode($key)) . "=" . htmlentities(urlencode($value));
                $f++;
            }
        }
        if ($pagecount > 1) {
            $current_page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1;
            $pager = '<ul class="pagination" aliastype="' . $aliasTypes[$tab] . '" t="' . $tab . '">
                <li class="prev' . (($current_page - 1) < 1 ? " disabled" : "") . '">
                    <a ' . (isset($_REQUEST['afterAjax']) ? '' : 'onclick="return false;"') . ' href="' . $url . '?page=' . ($current_page - 1) . '&per-page=' . $limitnum . '&tab=' . $tab . $filter_str . '" data-page="' . ($current_page - 1) . '">«</a>
                </li>';
            $renderedCount = 10;
            $upper_limit = round($pagecount, -1, PHP_ROUND_HALF_DOWN);
            if ($current_page > $renderedCount && $current_page <= $upper_limit) {
                //echo "here if $current_page<=$upper_limit";exit;
                $offsetStart = $current_page - 4;
                $offsetEnd = $current_page + 5;
                for ($p = $offsetStart; $p <= $offsetEnd; $p++) {
                    $pager .= '<li' . ($current_page == $p ? $active : "") . '>
                        <a ' . (isset($_REQUEST['afterAjax']) ? '' : 'onclick="return false;"') . ' href="' . $url . '?page=' . $p . '&per-page=' . $limitnum . '&tab=' . $tab . $filter_str . '" data-page="' . $p . '">' . $p . '</a>
                    </li>';
                }
            } else {
                if ($current_page >= ($pagecount - 5) && $current_page > $renderedCount) {
                    //echo "here else if $current_page>=$upper_limit";exit;
                    for ($p = $current_page; $p <= $pagecount; $p++) {
                        $pager .= '<li' . ($current_page == $p ? $active : "") . '>
                        <a ' . (isset($_REQUEST['afterAjax']) ? '' : 'onclick="return false;"') . ' href="' . $url . '?page=' . $p . '&per-page=' . $limitnum . '&tab=' . $tab . $filter_str . '" data-page="' . $p . '">' . $p . '</a>
                    </li>';
                    }
                } else {
                    //echo "here else";
                    //if(isset($_REQUEST['ajax'])) {echo $pagecount;exit;}
                    for ($p = 1; $p <= ($pagecount < $renderedCount ? $pagecount : $renderedCount); $p++) {
                        $pager .= '<li' . ($current_page == $p ? $active : "") . '>
                        <a ' . (isset($_REQUEST['afterAjax']) ? '' : 'onclick="return false;"') . ' href="' . $url . '?page=' . $p . '&per-page=' . $limitnum . '&tab=' . $tab . $filter_str . '" data-page="' . $p . '">' . $p . '</a>
                    </li>';
                    }
                }
            }
            $pager .= '<li class="next ' . ($current_page == $pagecount ? " disabled" : "") . '">
                    <a ' . (isset($_REQUEST['afterAjax']) ? '' : 'onclick="return false;"') . ' href="' . $url . '?page=' . ($current_page + 1) . '&per-page=' . $limitnum . '&tab=' . $tab . $filter_str . '" data-page="' . ($current_page + 1) . '">»</a>
                </li>
            </ul>';
        }
        return $pager;
    }

    public static function generateGridArray($dataProvider, $searchModel, $fullData, $count_types, $json = "", $AliasType = "")
    {
        //echo print_r($dataProvider->allModels);exit;  
        $count_types_arr = [];
        foreach ($count_types as $key => $value) {
            $count_types_arr[$key] = $key;
        }
        $grid = [
            'dataProvider' => $dataProvider,

            'layout' => "<div class='table-responsive activity-table'>{items}</div>{pager}",
            'filterModel' => $searchModel,
            //'pager'=>'yii\widgets\LinkSorter',

            /* 'pager' => [
                 'firstPageLabel' => 'First Page',
                 'lastPageLabel' => 'Last Page',
                 'class' => '\yii\widgets\LinkPager',
                    'class' => 'app\widgets\DropdownPager', */
            'columns' => [
                'AliasCode',
                [
                    'attribute' => 'AliasDescription',
                    'label' => 'Description'
                ],
                [
                    'attribute' => 'AliasInfo',
                    'label' => 'Info'
                ],
                [
                    'attribute' => 'AliasFormatType',
                    'filter' => $count_types_arr,
                    'label' => 'Format Type'
                ],
                /*[
                    'attribute' => 'AliasEdits',
                    'label' => 'Edits'
                ],*/
                [
                    'attribute' => 'AliasDatabaseTable',
                    'label' => 'Database Table'
                ],
                [
                    'attribute' => 'AliasDatabaseField',
                    'label' => 'Database Field'
                ],
                [
                    'class' => 'yii\grid\ActionColumn',
                    'headerOptions' => ['width' => '70px'],
                    'template' => ($AliasType != 'Custom Multi') ? '{update} {delete}' : '{copy_alias} {update} {delete}',
                    'buttons' => [
						'copy_alias' => function ($url, $model, $key) use ($dataProvider) {
							$urlarr = explode("/", $url);
                            $offset = count($urlarr) - 1;
                            $offset = ($offset < 0 ? 0 : $offset);
                            $fullURL = $url;
							if (isset($dataProvider->allModels[$urlarr[$offset]]['AliasCode'])) {
                                $url = str_replace($urlarr[$offset], $dataProvider->allModels[$urlarr[$offset]]['AliasCode'], $fullURL);
                            }
                            return Html::a('', Url::toRoute(['alias/copy/'.$dataProvider->allModels[$urlarr[$offset]]['AliasCode'].'?customAPI=1&action=copy_alias']), ['class' => 'glyphicon glyphicon-copy', 'title' => Yii::t('app', 'Copy Alias'), 'style' => 'color: #fab01b !important']);
						},
                        'update' => function ($url, $model, $key) use ($dataProvider) {
                            $urlarr = explode("/", $url);
                            $offset = count($urlarr) - 1;
                            $offset = ($offset < 0 ? 0 : $offset);
                            $fullURL = $url;
                            if (isset($dataProvider->allModels[$urlarr[$offset]]['AliasCode'])) {
                                /*                        $data_arr = self::jsonToArray($json);
                                                    echo print_r($data_arr); exit;  
                                                    if(!empty($data_arr))
                                                        $url = str_replace($url[$offset], $data_arr[$url[$offset]]['AliasCode'], $fullURL);*/
                                $url = str_replace($urlarr[$offset],
                                    $dataProvider->allModels[$urlarr[$offset]]['AliasCode'], $fullURL);
                            }
                            return Html::a('', $url . "?customAPI=1", ['class' => 'glyphicon glyphicon-pencil']);
                        },
                        'delete' => function ($url, $model, $key) use ($dataProvider) {
                            $urlarr = explode("/", $url);
                            $offset = count($urlarr) - 1;
                            $offset = ($offset < 0 ? 0 : $offset);
                            $fullURL = $url;
                            if (isset($dataProvider->allModels[$urlarr[$offset]]['AliasCode'])) {
                                $id = $dataProvider->allModels[$urlarr[$offset]]['AliasCode'];
                                /*                        
                                                    $data_arr = self::jsonToArray($json);
                                                echo print_r($data_arr); exit;  
                                                    if(!empty($data_arr))
                                                        $url = str_replace($url[$offset], $data_arr[$url[$offset]]['AliasCode'], $fullURL);*/
                                $url = str_replace($urlarr[$offset], $id, $fullURL);
                            }
                            return Html::a('', $url . "?customAPI=1",
                                ['class' => 'glyphicon glyphicon-trash record_delete', 'data-id' => $id]);
                        }
                    ]
                ]
            ],
            'tableOptions' => [
                'class' => 'table table-hover'
            ],
        ];
        return $grid;
    }

    public static function minifyJavascript($javascript, $inQuote = false)
    {
        $buffer = '';
        if ($inQuote != false) {
            $idx_end = getNonEscapedQuoteIndex($javascript, $inQuote) + 1;
            if ($idx_end == 0) {
                return array($javascript, $inQuote);
            }
            $quote = substr($javascript, 0, $idx_end);
            $quote = str_replace("\\\n", ' ', $quote);
            $quote = preg_replace("/\s+/", ' ', $quote);
            $buffer = $quote;
            $javascript = substr($javascript, $idx_end);
            $inQuote = false;
        }
        while (list($idx_start, $keyElement) = getNextKeyElement($javascript)) {
            switch ($keyElement) {
                case '//':
                    $idx_end = strpos($javascript, PHP_EOL, $idx_start);
                    if ($idx_end !== false) {
                        $javascript = substr($javascript, 0, $idx_start) . substr($javascript, $idx_end);
                    } else {
                        $javascript = substr($javascript, 0, $idx_start);
                    }
                    break;
                case '/*':
                    $idx_end = strpos($javascript, '*/', $idx_start) + 2;
                    $javascript = substr($javascript, 0, $idx_start) . substr($javascript, $idx_end);
                    break;
                default: // string case
                    if ($keyElement == '\'' || $keyElement == '"') {
                        $idx_end = getNonEscapedQuoteIndex($javascript, $keyElement, $idx_start + 1) + 1;
                    } else {
                        $idx_end = $idx_start + strlen($keyElement);
                    }
                    // php is embedded in string in javascript
                    if ($idx_end == 0) {
                        $idx_end = strlen($javascript);
                        $inQuote = $keyElement;
                    }
                    $buffer .= minifyJavascriptCode(substr($javascript, 0, $idx_start));
                    $quote = substr($javascript, $idx_start, ($idx_end - $idx_start));
                    $quote = str_replace("\\\n", ' ', $quote);
                    $quote = preg_replace("/\s+/", ' ', $quote);
                    $buffer .= $quote;
                    $javascript = substr($javascript, $idx_end);
            }
        }
        if ($inQuote) {
            return array($buffer, $inQuote);
        }
        $buffer .= minifyJavascriptCode($javascript);
        return $buffer;
    }

    public static function minifyHTML($html)
    {
        return preg_replace('/\s+/', ' ', $html);
    }

    public static function getHTMLKeyControlElements($php)
    {
        $elements = array();
        $elements['<?'] = strpos($php, '<?');
        if (preg_match("/<\s*script(?:\s+type=\"text\/javascript\")?\s*>/i", $php,
            $matches, PREG_OFFSET_CAPTURE)) {
            if ($matches[0][1] > 0) {
                $elements['<script>'] = $matches[0][1];
            }
        }
        if (preg_match("/<\s*style(?:\s+type=\"text\/css\")?\s*>/i", $php,
            $matches, PREG_OFFSET_CAPTURE)) {
            if (count($matches) > 0) {
                $elements['<style>'] = $matches[0][1];
            }
        }
        if (preg_match("/<\s*div\s+class\s*=\s*\"phpcode\"\s*>/i", $php,
            $matches, PREG_OFFSET_CAPTURE)) {
            if (count($matches) > 0) {
                $elements['<div>'] = $matches[0][1];
            }
        }
        if (preg_match("/<\s*pre\s*>/i", $php, $matches, PREG_OFFSET_CAPTURE)) {
            if (count($matches) > 0) {
                $buffer = '';
                while (list($start_idx, $key) = getHTMLKeyControlElements($php)) {
                    switch ($key) {
                        case '<?':
                            $end_idx = strpos($php, '?>', $start_idx + 1);
                            $buffer .= minifyHTML(substr($php, 0, $start_idx))
                                . substr($php, $start_idx, $end_idx + 2 - $start_idx);
                            $php = substr($php, $end_idx + 2);
                            break;
                        case '<style>':
                            $buffer .= minifyHTML(substr($php, 0, $start_idx)) . '<style type="text/css">';
                            $php = substr($php, strpos($php, '>', $start_idx + 1) + 1);
                            $end_idx = strpos($php, '</style>');
                            while (strpos($php, '<?') < $end_idx) {
                                $tmp_idx = strpos($php, '<?');
                                $tmp_end_idx = strpos($php, '?>') + 2;
                                $buffer .= minifyCSS(substr($php, 0, $tmp_idx))
                                    . substr($php, $tmp_idx, $tmp_end_idx - $tmp_idx);
                                $php = substr($php, $tmp_end_idx);
                                $end_idx = strpos($php, '</style>');
                            }
                            $buffer .= minifyCSS(substr($php, 0, $end_idx)) . '</style>';
                            $php = substr($php, $end_idx + 8);
                            break;
                        case '<script>':
                            $buffer .= minifyHTML(substr($php, 0, $start_idx)) . '<script type="text/javascript">';
                            $php = substr($php, strpos($php, '>', $start_idx + 1) + 1);
                            $inQuote = false;
                            $end_idx = strpos($php, '</script>');
                            while (strpos($php, '<?') < $end_idx) {
                                $tmp_idx = strpos($php, '<?');
                                $tmp_end_idx = strpos($php, '?>') + 2;
                                $result = minifyJavascript(substr($php, 0, $tmp_idx), $inQuote);
                                if (is_array($result)) {
                                    $buffer .= $result[0];
                                    $inQuote = $result[1];
                                } else {
                                    $buffer .= $result;
                                    $inQuote = false;
                                }
                                $buffer .= substr($php, $tmp_idx, $tmp_end_idx - $tmp_idx);
                                $php = substr($php, $tmp_end_idx);
                                $end_idx = strpos($php, '</script>');
                            }
                            $result = minifyJavascript(substr($php, 0, $end_idx), $inQuote);
                            $buffer .= $result . '</script>';
                            $php = substr($php, $end_idx + 9);
                            break;
                        case '<div>':
                            $end_idx = strpos($php, '</div>', $start_idx + 1);
                            $buffer .= minifyHTML(substr($php, 0, $start_idx))
                                . substr($php, $start_idx, $end_idx + 6 - $start_idx);
                            $php = substr($php, $end_idx + 6);
                            break;
                    }
                }
                $buffer .= minifyHTML($php);
                return $buffer;
            }
        }
    }

    public static function jsonToArray($json)
    {
        $model = new static();
        if ($json == false) {
            return [];
        }
        $json_decoded = json_decode($json);
        //if(strstr($json, '"PK"')) {print_r($json_decoded);exit;}
        //print_r($json_decoded);exit;
        $data_arr = array();
        $data = is_object($json_decoded) ? get_object_vars($json_decoded) : $json_decoded;
        $resultbody = is_object($data['resultbody']) ? get_object_vars($data['resultbody']) : $data['resultbody'];
        $sessionData = $model->getSessionData();
        if ($sessionData['secretKey'] && $sessionData['secretIv']) {
            $resultbody = $model->AesDecrypt($resultbody, $sessionData['secretKey'], $sessionData['secretIv']);
            header('Content-Type: text/html; charset=utf-8');
            $resultbody = self::utf8ize($resultbody);
            $strEnd = strrpos($resultbody, '}');
            $resultbody = substr($resultbody, 0, $strEnd + 1);
            $resultbody = json_decode($resultbody, true);
            //echo var_dump(json_last_error());
            //echo var_dump($resultbody);exit; //
        }

        if (isset($resultbody['record_list'])) {
            foreach ($resultbody['record_list'] as $record) {
                array_push($data_arr, (is_object($record) ? get_object_vars($record) : $record));
            }
        }
        //print_r($data_arr);exit;//
        return $data_arr;
    }

    public static function utf8ize($d)
    {
        if (is_array($d)) {
            foreach ($d as $k => $v) {
                unset($d[$k]);
                $d[utf8ize($k)] = utf8ize($v);
            }
        } else {
            if (is_object($d)) {
                $objVars = get_object_vars($d);
                foreach ($objVars as $key => $value) {
                    $d->$key = utf8ize($value);
                }
            } else {
                if (is_string($d)) {
                    return iconv('UTF-8', 'UTF-8//IGNORE', utf8_encode($d));
                }
            }
        }
        return $d;
    }

    public static function generateProvider($json)
    {
        $data_arr = self::jsonToArray($json);
        $dataProvider = new ArrayDataProvider([
            'allModels' => $data_arr,
            'pagination' => [
                'pageSize' => 10,
            ],
            'sort' => [
                'attributes' => ['AliasCode', 'AliasType'],
            ],
        ]);
        return $dataProvider;
    }
}