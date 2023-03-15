<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 */

namespace app\models;

use Yii;

class UserAccount extends AccountModel implements \yii\web\IdentityInterface
{
    public $id;
    public $account_name;
    public $account_password;
    public $account_status;
    public $account_type;
    public $background_color;
    public $border_color;
    public $border_size;
    public $email;
    public $group_area;
    public $info_color;
    public $language;
    public $last_login;
    public $link_color;
    public $tenant_code;
    public $text_color;
    public $user_name;
    public $currencyformat_code;
    public $datetimeformat_code;
    public $timezone_code;
    public $header_border_size;
    public $header_color;
    public $header_border_color;
    public $search_border_color;
    public $tab_selected_color;
    public $tab_unselected_color;
    public $section_background_color;
    public $highlight_color_selection;
    public $dateformat_code;
    public $timeformat_code;
    public $currencytype_code;
    public $menutype_code;
    public $button_style_code;
    public $menu_background;
    public $message_line_color;
    public $section_header_color;

    public $style_template;

    public $authKey;
    public $accessToken;

    public static $dataAction = 'login';
    public static $patchAction = 'menu';
    public static $getDefaultAction = 'menu/DefaultColors';

    public static function getBorderSizeAllowed()
    {
        return array(
            '0px' => '0px',
            '1px' => '1px',
            '2px' => '2px',
            '3px' => '3px',
            '4px' => '4px',
        );
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        $session = Yii::$app->session;
        $existModel = (isset($session['screenData']) && isset($session['screenData']['app\models\UserAccount'])) ? $session['screenData']['app\models\UserAccount'] : null;
        return (!empty($existModel) && $existModel->id == $id) ? $existModel : null;
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return null;
    }

    public function validatePassword($password)
    {
        return !empty($this->user_name);
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @param string $password
     * @return static|null
     */
    public static function findByUsername($username, $password)
    {
        self::checkSourceLink();

        $password = self::encodePassword($password);
        return self::getModelInstance(null, ["func_param" => ['ulogin' => $username, 'upassword' => $password]]);
    }

    public static function encodePassword($password)
    {
        return function_exists('openssl_digest') ? openssl_digest($password, 'sha512') : hash('sha512', $password);
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->authKey;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->authKey === $authKey;
    }

    protected static function preparePostData($additionalPostData = array(), $funcName = null)
    {
        return $additionalPostData;
    }

    protected static function getData($subKey = null, $postData = array())
    {
        $model = new static();

        $postData = array_merge(array('func_name' => $model::$dataAction), $postData);

        $attributes = $model->processData($model::preparePostData($postData));

        if (!empty($attributes) && isset($attributes['user'])) {
            $attributes['user']['id'] = $attributes['user']['user_id'];
            unset($attributes['user']['user_id']);
            $attributes['user']['username'] = $attributes['user']['user_name'];
            unset($attributes['user']['username']);

            foreach ($attributes['user'] as $attribute => $value) {
                $model->$attribute = $value;
            }
            $model->account_password = isset($postData['func_param']['upassword']) ? $postData['func_param']['upassword'] : null;
        } else {
            $model = null;
        }

        return $model;
    }
}