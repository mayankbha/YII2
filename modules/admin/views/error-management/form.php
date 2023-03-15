<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 *
 * @var $this yii\web\View
 * @var $model \app\modules\admin\models\forms\ErrorManagementForm
 */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use app\models\CustomLibs;
use yii\helpers\ArrayHelper;
use app\models\GetListList;
use yii\web\View;

if (($libraryList = CustomLibs::getModelInstance()) && !empty($libraryList->lib_list)) {
    $libraryList = ArrayHelper::map($libraryList->lib_list, 'lib_name', function ($data) {
        return $data['lib_name'] . (!empty($data['lib_descr']) ? ' - ' . $data['lib_descr'] : '');
    });
}
$baseLists = GetListList::getArrayForSelectByNames([GetListList::BASE_NAME_LANGUAGE]);
asort($baseLists[GetListList::BASE_NAME_LANGUAGE]);

$this->registerJs(/** @lang JavaScript */ '
    function handleFunctions(e, funcValue) {
        var input = $("#' . Html::getInputId($model, 'func_name') . '");
                    
        input.html("");
        input.append($("<option />", {text: "Loading..."}));
                        
        $.post("' . Url::to(['/admin/screen/lib-functions'], true) . '", {
            library: $(e).val(),
            direction: "' . CustomLibs::DIRECTION_SETTER . '"
        }).done(function (data) {
            input.html("");
            if (data) {
                $.each(data, function (i, item) {
                    input.append($("<option />", {value: item["func_name"], text: item["func_name"]}));
                })
                if (funcValue) {
                    input.val(funcValue);
                }
            }
        });
    }
', View::POS_HEAD);
$this->registerJs('handleFunctions("#' . Html::getInputId($model, 'lib_name') . '", "' . $model->func_name . '")');
?>

<?php $form = ActiveForm::begin() ?>
    <div class="row">
        <div class="col-sm-6">
            <?= $form->field($model, 'lib_name')->dropDownList($libraryList, [
                'prompt' => '',
                'onchange' => 'handleFunctions(this)'
            ]) ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'func_name')->dropDownList([], ['prompt' => '']) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-4">
            <?= $form->field($model, 'language')->dropDownList($baseLists[GetListList::BASE_NAME_LANGUAGE], ['prompt' => '']) ?>
        </div>
        <div class="col-sm-4">
            <?= $form->field($model, 'err_code')->input('text') ?>
        </div>
        <div class="col-sm-4">
            <?= $form->field($model, 'params')->input('text') ?>
        </div>
    </div>
    <br />
    <?= $form->field($model, 'body')->textarea() ?>
    <?= $form->field($model, 'description')->textarea() ?>
    <?= $form->field($model, 'note')->textarea() ?>

    <div class="button-block">
        <?= Html::a(Yii::t('app', 'Back'), Url::toRoute('index'), ['class' => 'btn btn-link']); ?>
        <?= Html::submitButton(Yii::t('app', 'Submit'), ['class' => 'btn btn-primary']); ?>
    </div>
<?php ActiveForm::end() ?>
