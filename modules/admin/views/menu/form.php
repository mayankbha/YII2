<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 */

/* @var $this yii\web\View */
/* @var $model \app\modules\admin\models\forms\MenuForm */

use app\modules\admin\models\Group;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use insolita\iconpicker\Iconpicker;

$groupList = Group::getData()->list;
$groupList = ArrayHelper::map($groupList, 'group_name', 'group_name');
?>

<?php $form = ActiveForm::begin() ?>
    <?= $form->field($model, 'group_name')->dropDownList($groupList, ['prompt' => '-- Select --']); ?>
    <?= $form->field($model, 'menu_text')->input('text'); ?>
    <?= $form->field($model, 'menu_image')->widget(Iconpicker::class); ?>
    <?= $form->field($model, 'menu_description')->textarea(['class' => 'form-control']); ?>
    <?= $form->field($model, 'weight')->input('number', ['min' => '0', 'step' => '1']); ?>

    <div class="button-block">
        <?= Html::a(Yii::t('app', 'Back'), Url::toRoute('index'), ['class' => 'btn btn-link']); ?>
        <?= Html::submitButton(Yii::t('app', 'Submit'), ['class' => 'btn btn-primary']); ?>
    </div>
<?php ActiveForm::end() ?>
