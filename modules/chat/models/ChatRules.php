<?php

namespace app\modules\chat\models;

use app\models\UserAccount;
use Yii;
use yii\base\DynamicModel;

class ChatRules extends BaseModel
{
    const CREATE_FUNC = 'CreateChatRule';
    const DELETE_FUNC = 'DeleteChatRule';
    const UPDATE_FUNC = 'UpdateChatRule';
    const GET_FUNC = 'GetChatRuleList';

    const BOOL_API_NO = 'N';

    public static $dataAction = self::GET_FUNC;

    public static function createChatRules($room, $rules)
    {
        $result = null;

        foreach ($rules as $accessGroup => $rule) {
            if ($rule == self::BOOL_API_NO) continue;

            $model = new DynamicModel([
                'room' => $room,
                'access_group' => $accessGroup,
                'rights' => $rule,
            ]);

            $result = parent::setModel($model);
        }

        return $result;
    }
}
