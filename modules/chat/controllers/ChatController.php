<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 */

namespace app\modules\chat\controllers;

use app\controllers\ModuleController;
use app\modules\admin\models\Group;
use app\modules\admin\models\User;
use app\modules\chat\models\ChatRules;
use app\modules\chat\models\forms\MessageForm;
use app\modules\chat\models\forms\RoomForm;
use app\modules\chat\models\Message;
use app\modules\chat\models\Room;
use yii\data\ArrayDataProvider;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use Yii;

class ChatController extends ModuleController
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ]
        ];
    }

    public function actionIndex()
    {
        if ($roomList = Room::getData(['room_type' => [Room::SYSTEM_TYPE_ROOM]])) {
            $ownerList = ArrayHelper::getColumn($roomList->list, 'owner');
            $ownerList = array_values(array_unique($ownerList));

            if ($roomOwners = User::getData(['id' => $ownerList])) {
                $roomOwners = ArrayHelper::map($roomOwners->list, 'id', 'user_name');
            }
            $roomList = $roomList->list;
        } else {
            $roomList = [];
            $roomOwners = [];
        }

        $dataProvider = new ArrayDataProvider([
            'key' => 'pk',
            'allModels' => $roomList,
            'sort' => [
                'attributes' => ['room_type'],
            ],
            'pagination' => ['pageSize' => 10]
        ]);

        return $this->render('index', compact('dataProvider', 'roomOwners'));
    }

    public function actionUpdate($id)
    {
        $model = new RoomForm();
        $messageModel = new MessageForm();
        $model->getData($id);

        if ($model->load(\Yii::$app->request->post()) && $model->validate()) {
            Room::update($model);
            Yii::$app->session->setFlash('success', 'Rules has been update');
            return $this->redirect(['chat/index']);
        }


        $messages = Message::getList($model->id);

        return $this->render('update', [
            'model' => $model,
            'messageModel' => $messageModel,
            'messages' => !empty($messages['messages']) ? $messages['messages'] : [],
        ]);
    }

    public function actionCreate()
    {
        $model = new RoomForm();
        if ($model->load(\Yii::$app->request->post()) && $model->validate()) {
            $room = Room::create($model);
            Yii::$app->session->setFlash('success', 'Room has been created');
            return $this->redirect(['chat/index']);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionDelete($id)
    {
        if (Room::delete($id)) {
            Yii::$app->session->setFlash('success', 'Room has been deleted');
        } else {
            Yii::$app->session->setFlash('danger', 'Unsuccessfully delete');
        }

        return $this->redirect(['chat/index']);
    }

    public function actionSendMessage()
    {
        $model = new MessageForm();

        if ($model->load(\Yii::$app->request->post()) && $model->validate()) {
            Message::create($model->roomId, $model->message);
        } else {
            throw new BadRequestHttpException('Invalid Arguments');
        }

        return $this->redirect(['chat/update', 'id' => $model->roomId]);
    }
}