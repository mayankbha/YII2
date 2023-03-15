<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 */

/**
 * @var $this yii\web\View
 * @var $model \app\modules\admin\models\forms\CustomQueryForm
 * @var $tablesInfo array|null
 */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

?>

<?php $form = ActiveForm::begin([
    'id' => 'query-builder-form',
    'action' => ($builder) ? Url::toRoute([$builder, 'id' => Yii::$app->request->get('id', null)]) : null
]) ?>
    <?= $form->field($model, 'query_name')->textInput(); ?>
    <?php if ($builder): ?>
        <?= $form->field($model, 'query_value')->hiddenInput(['id' => 'query-builder-result'])->label(false) ?>
        <?= $form->field($model, 'query_params')->hiddenInput(['id' => 'query-builder-params'])->label(false) ?>
        <?= $form->field($model, 'query_pks')->hiddenInput(['id' => 'query-builder-pks'])->label(false) ?>
    <?php else: ?>
        <?= $form->field($model, 'query_value')->textarea(['rows' => 7]); ?>
        <?= $form->field($model, 'query_params')->textInput(['placeholder' => 'PARAM1, PARAM2, ...', 'pattern' => '[\w|\-|\d]+(,\s*[\w|\-|\d]+)*', 'title' => Yii::t('app', "Only letters and numbers. Use the delimiter ',' for separete params")]); ?>
        <?= $form->field($model, 'query_pks')->textInput(['placeholder' => 'PK1, PK2, ...', 'pattern' => '[\w|\-|\d|.]+(,\s*[\w|\-|\d|.]+)*', 'title' => Yii::t('app', "Only letters and numbers. Use the delimiter ',' for separete params")]); ?>
    <?php endif ?>
    <?= $form->field($model, 'description')->textarea(); ?>

    <?php if ($builder): ?>
        <?php if ($tablesInfo): ?>
            <hr />
            <div class="form-group">
                <?= Html::label('Type', 'query-select-type', ['class' => 'control-label']) ?>
                <?= Html::dropDownList('query_select_type', 'SELECT', ['SELECT' => 'SELECT', 'SELECT DISTINCT' => 'SELECT DISTINCT'], [
                    'id' => 'query-select-type',
                    'class' => 'form-control',
                ]) ?>
            </div>
            <div class="form-group">
                <?= Html::label('Table name', 'query-table-name', ['class' => 'control-label']) ?>
                <?= Html::dropDownList('query_table_name', null, ArrayHelper::map($tablesInfo, 'table_name', 'table_name'), [
                    'id' => 'query-table-name',
                    'class' => 'form-control',
                    'prompt' => ''
                ]) ?>
            </div>
            <div class="form-group">
                <?= Html::label('Out params', 'query-out-params', ['class' => 'control-label']) ?>
                <?= Html::dropDownList('query_out_params', null, [], ['id' => 'query-out-params', 'class' => 'form-control', 'multiple' => true, 'required' => true, 'size' => 6]); ?>
            </div>
            <div class="form-group">
                <?= Html::label('PKS', 'query-pks', ['class' => 'control-label']) ?>
                <?= Html::dropDownList('query-pks', null, [], ['id' => 'query-pks', 'class' => 'form-control', 'multiple' => true, 'required' => true, 'size' => 6]); ?>
            </div>
            <?= Html::tag('div', null, ['id' => 'query-builder']) ?>
        <?php else: ?>
            <div class="alert alert-danger alert-dismissible" role="alert">
                <span class="alert-icon">
                    <span class="icon"></span>
                </span>
                Server tables not found
            </div>
        <?php endif ?>
    <?php endif ?>

    <br />
    <div class="button-block">
        <?= Html::a(Yii::t('app', 'Back'), Url::toRoute('index'), ['class' => 'btn btn-link']); ?>
        <?= Html::submitButton(Yii::t('app', 'Submit'), ['class' => 'btn btn-primary']); ?>
    </div>
<?php ActiveForm::end() ?>
