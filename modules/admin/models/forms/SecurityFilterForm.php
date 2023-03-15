<?php
namespace app\modules\admin\models\forms;
use Yii;
use yii\base\Model;

class SecurityFilterForm extends Model
{
    const VALUE_TRUE = 'Y';
    const VALUE_FALSE = 'N';

    public $tenant;
    public $user_type;
    public $description;
    public $account_type;
    public $filter1;
    public $filter1_length;
    public $filter2;
    public $filter2_length;

    public $allow_password_change;
    public $allow_settings_change;
    public $allow_self_registration;
    public $ldap;
    public $allow_chat;

    public $secret_questions;
    public $registration_screen_id;
    public $auth_types;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['tenant', 'user_type', 'account_type', 'filter1','filter1_length','filter2','filter2_length', 'secret_questions', 'auth_types', 'registration_screen_id'], 'required', 'message'=> Yii::t('app', 'Please fill out this field.')],
            [['description', 'filter1', 'filter2'], 'string', 'max' => 255],
            ['user_type', 'string', 'max' => 1],
            [['filter1_length','filter2_length', 'registration_screen_id'], 'integer'],
            [['allow_password_change', 'allow_settings_change', 'allow_self_registration', 'ldap', 'allow_chat'], 'boolean', 'trueValue' => self::VALUE_TRUE, 'falseValue' => self::VALUE_FALSE, 'strict' => false]
        ];
    }

    /**
     * @return array customized attribute labels
     */
    public function attributeLabels()
    {
        return [
            'tenant' => Yii::t('app', 'Tenant'),
            'user_type' => Yii::t('app', 'User type'),
            'description' => Yii::t('app', 'Description'),
            'account_type'=> Yii::t('app', 'Account type'),
            'filter1' => Yii::t('app', 'Filter 1 label'),
            'filter2' => Yii::t('app', 'Filter 2 label'),
            'filter1_length' => Yii::t('app', 'Filter 1 length'),
            'filter2_length' => Yii::t('app', 'Filter 2 length'),
            'allow_password_change' => Yii::t('app', 'Allow change password'),
            'allow_settings_change' => Yii::t('app', 'Allow change settings'),
            'allow_self_registration' => Yii::t('app', 'Allow self registration'),
            'ldap' => Yii::t('app', 'LDAP'),
            'secret_questions' => Yii::t('app', 'Secret questions'),
            'registration_screen_id' => Yii::t('app', 'Registration screen'),
            'auth_types' => Yii::t('app', 'Authorization type')
        ];
    }
}