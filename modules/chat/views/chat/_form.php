<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;
use app\modules\chat\models\Room;
use yii\helpers\Url;
?>

<?php $form = ActiveForm::begin(['id' => 'create-room-form']); ?>
<div class="form-group">
    <?= $form->field($model, 'name') ?>
</div>
<div class="panel panel-default chat-room-messages-block">
    <div class="panel-heading">Rules</div>
    <ul class="list-group">
        <?php foreach ($model->userGroups as $group): ?>
            <li class="list-group-item">
                <?= $group['group_name'] ?>
                <?php $value = isset($group['value']) ? $group['value'] : Room::RIGHT_N; ?>
                <?= Html::radioList("{$model->formName()}[groupRules][{$group['group_name']}]", $value, [Room::RIGHT_U => 'Full rights', Room::RIGHT_R => 'Read only', Room::RIGHT_N => 'No rights']) ?>
            </li>
        <?php endforeach ?>
    </ul>
</div>


<div class="form-submit text-right">
    <?php if($model->id): ?>
    <?= Html::a(Yii::t('app', 'Back'), Url::toRoute('index'), ['class' => 'btn btn-link']); ?>
    <?php endif; ?>
    <?= Html::submitButton( $model->id ? Yii::t('app', 'Update') : Yii::t('app', 'Create'), ['class' => 'btn btn-primary']) ?>
</div>
<?php ActiveForm::end(); ?>