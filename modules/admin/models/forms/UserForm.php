<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 */

namespace app\modules\admin\models\forms;

use kartik\password\StrengthValidator;
use Yii;
use yii\base\Model;

/**
 * Class UserForm
 * @property UserStyleTemplateForm $style_template
 */
class UserForm extends Model
{
    public $id;
    public $pk;
    public $account_name;
    public $phone;
    public $account_password;
    public $account_status;
    public $account_type;
    public $account_security_type;
    public $email;
    public $group_area;
    public $language;
    public $last_login;
    public $tenant_code;
    public $user_name;
    public $currencyformat_code;
    public $datetimeformat_code;
    public $timezone_code;
    public $dateformat_code;
    public $timeformat_code;
    public $currencytype_code;
    public $menutype_code;
    public $button_style_code;
    public $document_group;
    public $security1;
    public $security2;
    public $security1_length;
    public $security2_length;

    public $style_template;

    const BOOL_API_TRUE = 'Y';
    const BOOL_API_FALSE = 'N';

    const ACTIVE_ACCOUNT = 'active';
    const INACTIVE_ACCOUNT = 'inactive';

    public static $boolProperty = [
        self::BOOL_API_TRUE => 'Yes',
        self::BOOL_API_FALSE => 'No',
    ];

    public static $statusProperty = [
        self::ACTIVE_ACCOUNT => 'Active',
        self::INACTIVE_ACCOUNT => 'Inactive',
    ];

    public function init()
    {
        $this->style_template = new UserStyleTemplateForm();
        parent::init();
    }

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['user_name', 'phone'], 'required'],
            [['group_area', 'email'], 'required'],
            [
                [
                    'account_type',
                    'account_name',
                    'currencyformat_code',
                    'timezone_code',
                    'dateformat_code',
                    'timeformat_code',
                    'currencytype_code',
                    'menutype_code',
                    'button_style_code'
                ],
                'required', 'message'=> Yii::t('app', 'Please fill out this field.')
            ],
            [['id', 'tenant_code'], 'integer'],
            [
                [
                    'pk',
                    'last_login',
                    'style_template',
                    'datetimeformat_code',
                    'document_group'
                ],
                'safe'
            ],
            [['security1_length', 'security2_length','account_security_type'],'safe'],
            [['security1','security2'],'checkLength'],
            //[['account_password'], StrengthValidator::class, 'preset' => 'normal', 'userAttribute' => 'user_name'],
            [['account_status'], 'boolean', 'trueValue' => self::ACTIVE_ACCOUNT, 'falseValue' => self::INACTIVE_ACCOUNT, 'strict' => false],
        ];
    }

    public function checkLength($attribute, $params, $validator)
    {
        $n = substr($attribute, -1);
        $values = explode(',',$this->$attribute);
        $lengthProp = sprintf('security%s_length',$n);
        $length = $this->{$lengthProp};
        if(($length = (int)$length) && is_int($length)) {
            foreach($values as $e){
                if (mb_strlen($e) > $length){
                    $this->addError($attribute, "This field cannot contain an element of length > $length");
                }
            }
        }
    }

    /**
     * @return array customized attribute labels
     */
    public function attributeLabels()
    {
        return [
            'user_name' => Yii::t('app', 'User name'),
            'tenant_code' => Yii::t('app', 'Tenant code'),
            'last_login' => Yii::t('app', 'Last login'),
            'language' => Yii::t('app', 'Language'),
            'group_area' => Yii::t('app', 'Group area'),
            'email' => Yii::t('app', 'Email'),
            'background_color' => Yii::t('app', 'Background color'),
            'account_type' => Yii::t('app', 'Account type'),
            'account_status' => Yii::t('app', 'Active account'),
            'account_name' => Yii::t('app', 'Account name'),
            'currencyformat_code' => Yii::t('app', 'Currency format'),
            'datetimeformat_code' => Yii::t('app', 'Date/Time format'),
            'timezone_code' => Yii::t('app', 'Timezone'),
            'dateformat_code' => Yii::t('app', 'Date format'),
            'timeformat_code' => Yii::t('app', 'Time format'),
            'currencytype_code' => Yii::t('app', 'Currency type'),
            'menutype_code' => Yii::t('app', 'Menu format'),
            'button_style_code' => Yii::t('app', 'Button style'),
            'account_password' => Yii::t('app', 'Password'),
            'document_group' => Yii::t('app', 'Document group'),
            'account_security_type' => Yii::t('app', 'User type'),
            'phone'=> Yii::t('app', 'Phone'),
        ];
    }
}