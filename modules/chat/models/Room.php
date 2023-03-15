<?php
namespace app\modules\chat\models;

use app\models\UserAccount;
use yii\base\DynamicModel;
use app\modules\chat\models\forms\RoomForm;
use yii\filters\PageCache;

class Room extends BaseModel
{
    const RIGHT_U = 'U';
    const RIGHT_R = 'R';
    const RIGHT_N = 'N';

    const BOOL_API_TRUE = 'Y';
    const BOOL_API_FALSE = 'N';

    const DELETE_FUNC_TYPE = 'DeleteRoom';

    const SYSTEM_TYPE_ROOM = 'RoomType.System';

    public static $dataAction = 'GetRoomList';

    /**
     * @param $id
     * @param array $model
     *
     * @return bool|mixed|null|string
     */
    public static function updateModel($id, $model)
    {
        $model = new DynamicModel($model);
        return parent::updateModel($id, $model);
    }

    public static function create(RoomForm $form)
    {
        $userModel = \Yii::$app->session['screenData'][UserAccount::class];

        $model = new DynamicModel([
            'owner' => $userModel->id,
            'room_name' => $form->name,
            'room_type' => self::SYSTEM_TYPE_ROOM,
        ]);

        $systemRoom = self::setModel($model);

        if (!empty($systemRoom['record_list'])) {
            $room = $systemRoom['record_list']['PK'];
            ChatRules::createChatRules($room, $form->groupRules);
            return $room;
        }

        return false;
    }

    public static function delete($id)
    {
        $userModel = \Yii::$app->session['screenData'][UserAccount::class];

        $result = (new static())->processData([
            'lib_name' => static::$dataLib,
            'func_name' => self::DELETE_FUNC_TYPE,
            'func_param' => [
                'room' => (string)$id,
                'user' => $userModel->id,
            ]
        ]);

        return $result;
    }

    public static function update(RoomForm $form)
    {
        $model = new DynamicModel([
            'room_name' => $form->name,
        ]);

        $result = parent::updateModel($form->id, $model);

        foreach ($form->userGroups as $userGroup) {
            $chatRulePk = $form->id . ';' . $userGroup['group_name'];
            if (isset($userGroup['value']) && $form->groupRules[$userGroup['group_name']] == self::RIGHT_N) {
                ChatRules::deleteModel($chatRulePk);
            } else if(isset($userGroup['value']) && $form->groupRules[$userGroup['group_name']] != self::RIGHT_N) {
                $model = new DynamicModel([
                    'rights' => $form->groupRules[$userGroup['group_name']]
                ]);

                ChatRules::updateModel($chatRulePk, $model);
            } else if($form->groupRules[$userGroup['group_name']] != self::RIGHT_N) {
                $model = new DynamicModel([
                    'access_group' => $userGroup['group_name'],
                    'rights' => $form->groupRules[$userGroup['group_name']],
                    'room' => $form->id
                ]);

                ChatRules::setModel($model);
            }
        }

        return $result;
    }
}
