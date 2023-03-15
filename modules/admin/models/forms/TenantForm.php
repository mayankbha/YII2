<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 */

namespace app\modules\admin\models\forms;

use Yii;
use yii\base\Model;

class TenantForm extends Model
{
    public $Tenant;
    public $Logos;
    public $Name;
    public $Contact;
    public $Email;
    public $Phone;
    public $Address1;
    public $Address2;
    public $Address3;
    public $City;
    public $StateRegion;
    public $Country;
    public $Postal;
    public $PostalExtend;
    public $DataLanguage;
    public $DefaultCurrency;
    public $DefaultCurrencyType;
    public $DefaultTimeFormat;
    public $DefaultTimeZone;
    public $DefaultDate;
    public $DefaultButtonStyle;
    public $DefaultMenuType;
    public $PinExpirationTime;
    public $Comments;

    public $EmailServer;
    public $EmailAccount;
    public $EmailPassword;

    public $TwilioSid;
    public $TwilioAuthToken;
    public $TwilioPhone;

	public $LDAPServer;
    public $LDAPSuperUserDN;
    public $LDAPPassword;

    /** @var $StyleTemplate UserStyleTemplateForm */
    public $StyleTemplate;

    /** @var $ChatSettings ChatSettingsForm */
    public $ChatSettings;

    public function init()
    {
        $this->StyleTemplate = new UserStyleTemplateForm();
        $this->ChatSettings = new ChatSettingsForm();
        parent::init();
    }

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['Tenant', 'Name', 'Contact', 'Email', 'Phone', 'Address1', 'City', 'Country'], 'required', 'message'=> Yii::t('app', 'Please fill out this field.')],
            [['Address2', 'Address3', 'StateRegion', 'Postal', 'PostalExtend', 'DataLanguage', 'Comments'], 'string'],
            [['DefaultCurrency', 'DefaultCurrencyType', 'DefaultTimeFormat', 'DefaultTimeZone', 'DefaultDate', 'DefaultButtonStyle', 'DefaultMenuType'], 'string'],
            [['EmailServer', 'EmailAccount', 'EmailPassword', 'TwilioSid', 'TwilioAuthToken', 'TwilioPhone', 'LDAPServer', 'LDAPSuperUserDN', 'LDAPPassword'], 'string'],
            [['Tenant', 'PinExpirationTime'], 'integer'],
            ['Email', 'email'],
            [['Logos', 'StyleTemplate', 'ChatSettings'],'safe']
        ];
    }

    /**
     * @return array customized attribute labels
     */
    public function attributeLabels()
    {
        return [
            'Tenant' => Yii::t('app', 'Tenant code'),
            'Logos' => Yii::t('app', 'Logo'),
            'Name' => Yii::t('app', 'Name'),
            'Contact' => Yii::t('app', 'Contact'),
            'Email' => Yii::t('app', 'Email'),
            'Phone' => Yii::t('app', 'Phone'),
            'Address1' => Yii::t('app', 'Address 1'),
            'Address2' => Yii::t('app', 'Address 2'),
            'Address3'  => Yii::t('app', 'Address 3'),
            'City' => Yii::t('app', 'City'),
            'StateRegion' => Yii::t('app', 'State/Region'),
            'Country' => Yii::t('app', 'Country'),
            'Postal' => Yii::t('app', 'Postal code'),
            'PostalExtend' => Yii::t('app', 'Postal extend'),
            'DataLanguage' => Yii::t('app', 'Language'),
            'DefaultButtonStyle' => Yii::t('app', 'Default Button Style'),
            'DefaultMenuType' => Yii::t('app', 'Default Menu Type'),
            'DefaultCurrency' => Yii::t('app', 'Currency'),
            'DefaultCurrencyType' => Yii::t('app', 'Currency type'),
            'DefaultTimeFormat' => Yii::t('app', 'Time format'),
            'DefaultTimeZone' => Yii::t('app', 'Timezone'),
            'DefaultDate' => Yii::t('app', 'Date'),
            'Comments' => Yii::t('app', 'Comments'),
            'PinExpirationTime' => Yii::t('app', 'Expiration time of password reset token (minutes)'),

            'EmailServer' => Yii::t('app', 'Email server'),
            'EmailAccount' => Yii::t('app', 'Email account'),
            'EmailPassword' => Yii::t('app', 'Email password'),

            'TwilioSid' => Yii::t('app', 'Twilio sid'),
            'TwilioAuthToken'  => Yii::t('app', 'Twilio auth token'),
            'TwilioPhone' => Yii::t('app', 'Twilio phone'),

			'LDAPServer' => Yii::t('app', 'LDAP Server'),
			'LDAPSuperUserDN' => Yii::t('app', 'LDAP Super User DN'),
			'LDAPPassword' => Yii::t('app', 'LDAP Password')
        ];
    }
}