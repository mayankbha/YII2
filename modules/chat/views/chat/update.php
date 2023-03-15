<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 *
 * @var $this yii\web\View
 * @var $room array
 * @var $messages array
 * @var $groups array
 * @var $chatRules array
 */

use yii\helpers\Html;
use yii\helpers\Url;
use app\modules\chat\models\Room;
use yii\bootstrap\ActiveForm;

$this->title = Yii::t('app', "Room '{room_name}'", ['room_name' => $model['name']]);
?>

<h1><?= $this->title ?></h1>
<?php \app\components\ThemeHelper::printFlashes(); ?>

<div class="border-form-block">
    <?php echo $this->render('_form', ['model' =>$model]); ?>
</div>
<div class="panel panel-default chat-room-messages-block">
    <div class="panel-heading">Messages</div>
    <ul class="list-group">
        <?php foreach ($messages as $item): ?>
            <li class="list-group-item <?= (Yii::$app->getUser()->getId() != $item['sender']) ? 'text-right' : '' ?>">
                <small class="text-info"><?= $item['user_name'] ?></small> <small class="text-muted">(<?= date('Y-m-d H:i:s', $item['message_time']) ?>)</small><br />
                <b><?= $item['message'] ?></b>
            </li>
        <?php endforeach ?>
    </ul>
    <div class="border-form-block">
        <?php $form = ActiveForm::begin(['action' =>['chat/send-message'], 'id' => 'forum_post', 'method' => 'post',]); ?>
<!--            <input name="message" type="text" placeholder="Write your message...">-->
            <?= $form->field($messageModel, 'roomId')->hiddenInput(['value'=> $model->id])->label(false) ?>
            <?= $form->field($messageModel, 'message')->textarea()->label(false) ?>
<!--            <button type="submit" class="submit"><i class="glyphicon glyphicon-send" aria-hidden="true"></i></button>-->
        <div class="form-submit text-right">
            <?= Html::submitButton(Yii::t('app', 'Send message'), ['class' => 'btn btn-primary']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
