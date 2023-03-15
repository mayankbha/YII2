<?php

namespace app\modules\chat\models\forms;

use app\modules\admin\models\Group;
use app\modules\chat\models\ChatRules;
use app\modules\chat\models\Room;
use Yii;
use yii\base\Model;
use yii\web\NotFoundHttpException;


class RoomForm extends Model
{
    public $id;
    public $name;
    public $userGroups;
    public $groupRules;

    public function __construct(array $config = [])
    {
        $this->getAllUserGroups();
        parent::__construct($config);
    }

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['groupRules'], 'safe'],
        ];
    }

    /**
     * @return array customized attribute labels
     */
    public function attributeLabels()
    {
        return [
            'name' => 'name',
        ];
    }

    protected function getAllUserGroups()
    {
        if ($groups = Group::getData()) {
            $this->userGroups = $groups->list;
        } else {
            throw new NotFoundHttpException('No groups found for access');
        }
    }

    public function getData($id)
    {
        if ($room = Room::getData(['room' => [$id]])) {
            $room = $room->list[0];
            $this->id = $room['pk'];
            $this->name = $room['room_name'];
        } else {
            throw new NotFoundHttpException('The room is not found');
        }

        $this->getRoomGroups($room['pk']);
    }

    protected function getRoomGroups($roomId)
    {
        if ($chatRules = ChatRules::getData(['room' => [$roomId]])) {
            foreach ($this->userGroups as $key => $group) {
                foreach ($chatRules->list as $chatRule) {
                    if ($group['group_name'] == $chatRule['access_group']) {
                        $this->userGroups[$key]['value'] = $chatRule['rights'];
                    }
                }
            }
        }
    }
}