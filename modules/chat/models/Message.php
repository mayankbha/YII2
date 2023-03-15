<?php
namespace app\modules\chat\models;

use app\models\UserAccount;
use Yii;

class Message extends BaseModel
{
    const FUNCTION_CREATE = 'CreateMessage';
    const FUNCTION_DELETE = 'DeleteMessage';
    const FUNCTION_UPDATE = 'UpdateMessage';
    const FUNCTION_GET_LIST = 'GetNewMessages'; //'GetMessageList';
    const FUNCTION_SEARCH = 'SearchMessage';

    public static function create($room, $message)
    {
        $userModel = Yii::$app->session['screenData'][UserAccount::class];

        if (empty($userModel->id)) {
            return false;
        }

        $model = new static();
        return $model->processData([
            'lib_name' => static::$dataLib,
            'func_name' => self::FUNCTION_CREATE,
            'func_param' => [
                'patch_json' => [
                    'message' => (string)$message,
                    'room' => (string)$room,
                    'sender' => (string)$userModel->id
                ]
            ]
        ]);
    }

    public static function delete($id)
    {
        $model = new static();
        return $model->processData([
            'lib_name' => static::$dataLib,
            'func_name' => self::FUNCTION_DELETE,
            'func_param' => [
                'PK' => (string)$id
            ]
        ]);
    }

    public static function getList($room)
    {
        $userModel = Yii::$app->session['screenData'][UserAccount::class];

        if (!empty($room) && !empty($userModel->id)) {
            $messages = (new static())->processData([
                'lib_name' => static::$dataLib,
                'func_name' => self::FUNCTION_GET_LIST,
                'func_param' => [
                    'user' => (string)$userModel->id,
                    'room' => $room
                ]
            ]);

            return isset($messages['record_list']) ? $messages['record_list'] : null;
        }

        return [];
    }
}
