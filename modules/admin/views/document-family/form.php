<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 */

/**
 * @var $this yii\web\View
 * @var $models array
 * @var $model \app\modules\admin\models\forms\DocumentFamilyForm
 */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$this->registerJs("
    $(document)
        .on('click', '.add-category-family', function () {
            var wrapper = $('.document-family-wrapper'),
                formName = wrapper.attr('data-form-name'),
                iteration = parseInt(wrapper.attr('data-iteration')) + 1,
                newWrapper = $('<div />', {class: 'row'}).html(wrapper.html());
                
            newWrapper.find('input, select, textarea').each(function () {
                var name = $(this).attr('name');
                $(this).attr('name', formName + '[' + iteration + '][' + name + ']');
            });
            
            wrapper.before(newWrapper);
            wrapper.attr('data-iteration', iteration);
            console.log('click');
        })
        .on('click', '.remove-family-icon', function () {
            if (confirm('A you\'re a sure want to delete this category?')) {
                $(this).parents('.row')[0].remove();
            }
        });
");
?>

<?php $form = ActiveForm::begin() ?>
    <?= $form->field($models[0], 'family_name')->input('text'); ?>
    <?= $form->field($models[0], 'family_description')->textarea(); ?>

    <div class="row">
        <div class="col-sm-2">
            <div class="form-group">
                <label>
                    <?= $models[0]->getAttributeLabel('category'); ?>
                </label>
            </div>
        </div>
        <div class="col-sm-2">
            <div class="form-group">
                <label>
                    <?= $models[0]->getAttributeLabel('category_description'); ?>
                </label>
            </div>
        </div>
        <div class="col-sm-1">
            <div class="form-group">
                <label>
                    <?= $models[0]->getAttributeLabel('key_part_1'); ?>
                </label>
            </div>
        </div>
        <div class="col-sm-1">
            <div class="form-group">
                <label>
                    <?= $models[0]->getAttributeLabel('key_part_2'); ?>
                </label>
            </div>
        </div>
        <div class="col-sm-1">
            <div class="form-group">
                <label>
                    <?= $models[0]->getAttributeLabel('key_part_3'); ?>
                </label>
            </div>
        </div>
        <div class="col-sm-1">
            <div class="form-group">
                <label>
                    <?= $models[0]->getAttributeLabel('key_part_4'); ?>
                </label>
            </div>
        </div>
        <div class="col-sm-1">
            <div class="form-group">
                <label>
                    <?= $models[0]->getAttributeLabel('key_part_5'); ?>
                </label>
            </div>
        </div>
        <div class="col-sm-2">
            <div class="form-group">
                <label>
                    <?= $models[0]->getAttributeLabel('search_text'); ?>
                </label>
            </div>
        </div>
    </div>
    <?php foreach($models as $i => $model): ?>
        <div class="row">
            <div class="col-sm-2">
                <?= $form->field($model, 'category')->textInput(['name' => "{$model->formName()}[$i][category]"])->label(false); ?>
            </div>
            <div class="col-sm-2">
                <?= $form->field($model, 'category_description')->textInput(['name' => "{$model->formName()}[$i][category_description]"])->label(false); ?>
            </div>
            <div class="col-sm-1">
                <?= $form->field($model, 'key_part_1')->textInput(['name' => "{$model->formName()}[$i][key_part_1]"])->label(false); ?>
            </div>
            <div class="col-sm-1">
                <?= $form->field($model, 'key_part_2')->textInput(['name' => "{$model->formName()}[$i][key_part_2]"])->label(false); ?>
            </div>
            <div class="col-sm-1">
                <?= $form->field($model, 'key_part_3')->textInput(['name' => "{$model->formName()}[$i][key_part_3]"])->label(false); ?>
            </div>
            <div class="col-sm-1">
                <?= $form->field($model, 'key_part_4')->textInput(['name' => "{$model->formName()}[$i][key_part_4]"])->label(false); ?>
            </div>
            <div class="col-sm-1">
                <?= $form->field($model, 'key_part_5')->textInput(['name' => "{$model->formName()}[$i][key_part_5]"])->label(false); ?>
            </div>
            <div class="col-sm-2">
                <?= $form->field($model, 'search_text')->textInput(['name' => "{$model->formName()}[$i][search_text]"])->label(false); ?>
            </div>
            <div class="col-sm-1">
                <?php if ($i > 0): ?><span class="glyphicon glyphicon-remove remove-family-icon"></span><?php endif ?>
            </div>
        </div>
    <?php endforeach ?>
    <div class="row document-family-wrapper" style="display: none;" data-form-name="<?= $model->formName() ?>" data-iteration="<?= $i ?>">
        <div class="col-sm-2">
            <div class="form-group">
                <?= Html::textInput("category", null, ['class' => 'form-control']) ?>
            </div>
        </div>
        <div class="col-sm-2">
            <div class="form-group">
                <?= Html::textInput("category_description", null, ['class' => 'form-control']) ?>
            </div>
        </div>
        <div class="col-sm-1">
            <div class="form-group">
                <?= Html::textInput("key_part_1", null, ['class' => 'form-control']) ?>
            </div>
        </div>
        <div class="col-sm-1">
            <div class="form-group">
                <?= Html::textInput("key_part_2", null, ['class' => 'form-control']) ?>
            </div>
        </div>
        <div class="col-sm-1">
            <div class="form-group">
                <?= Html::textInput("key_part_3", null, ['class' => 'form-control']) ?>
            </div>
        </div>
        <div class="col-sm-1">
            <div class="form-group">
                <?= Html::textInput("key_part_4", null, ['class' => 'form-control']) ?>
            </div>
        </div>
        <div class="col-sm-1">
            <div class="form-group">
                <?= Html::textInput("key_part_5", null, ['class' => 'form-control']) ?>
            </div>
        </div>
        <div class="col-sm-2">
            <div class="form-group">
                <?= Html::textInput("search_text", null, ['class' => 'form-control']) ?>
            </div>
        </div>
        <div class="col-sm-1">
            <span class="glyphicon glyphicon-remove remove-family-icon"></span>
        </div>
    </div>
    <div class="form-group pull-left">
        <?= Html::button(Yii::t('app', 'Add category'), ['class' => 'btn btn-default add-category-family']); ?>
    </div>

    <div class="button-block">
        <?= Html::a(Yii::t('app', 'Back'), Url::toRoute('index'), ['class' => 'btn btn-link']); ?>
        <?= Html::submitButton(Yii::t('app', 'Submit'), ['class' => 'btn btn-primary']); ?>
    </div>
<?php ActiveForm::end() ?>
