<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 */

/**
 * @var $this yii\web\View
 * @var $models array
 * @var $families array
 * @var $categories array
 */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\modules\admin\models\forms\DocumentGroupForm;

$this->registerJs("
    $(document).on('change', '.document-family-select', function () {
    console.log('work');
        $('.document-category-select:enabled').prop('disabled', true).hide();
        $('.document-category-select[data-category=\"' + $(this).val() + '\"]').prop('disabled', false).show();
    });
");

foreach($families as $name => $description) {
    $families[$name] = ($description) ? $name . ' - ' . $description : $name;
}

foreach($categories as $familyName => $categoryItems) {
    foreach($categoryItems as $name => $description) {
        $categories[$familyName][$name] = ($description) ? $name . ' - ' . $description : $name;
    }
}
?>

<?php $form = ActiveForm::begin() ?>
    <?= $form->field($models[0], 'group_name')->input('text'); ?>
    <?= $form->field($models[0], 'group_description')->textarea(); ?>

    <div class="row document-group-wrapper">
        <div class="col-sm-3">
            <?= $form->field($models[0], 'document_family')->dropDownList($families, ['class' => 'form-control document-family-select']); ?>
        </div>
        <div class="col-sm-4 col-sm-offset-1">
            <div class="form-group field-documentgroupform-document_category">
            <?= Html::label($models[0]->getAttributeLabel('document_category'), null, ['class' => 'control-label']) ?>
            <?php foreach($categories as $familyName => $categoryItems): ?>
                <?php
                    reset($families);
                    $disabledParam = array_key_exists($models[0]->document_family, $families) ? $models[0]->document_family : key($families);

                    echo Html::listBox("{$models[0]->formName()}[document_category]", ArrayHelper::getColumn($models, 'document_category'), $categoryItems, [
                        'class' => 'form-control document-category-select',
                        'data-category' => $familyName,
                        'multiple' => true,
                        'disabled' => $familyName != $disabledParam,
                        'style' => 'display: ' . (($familyName == $disabledParam) ? 'block' : 'none')
                    ]);
                ?>
            <?php endforeach ?>
            </div>
        </div>
        <div class="col-sm-3 col-sm-offset-1">
            <?= $form->field($models[0], 'access_right')->radioList(DocumentGroupForm::$access_list) ?>
        </div>
    </div>

    <div class="button-block">
        <?= Html::a(Yii::t('app', 'Back'), Url::toRoute('index'), ['class' => 'btn btn-link']); ?>
        <?= Html::submitButton(Yii::t('app', 'Submit'), ['class' => 'btn btn-primary']); ?>
    </div>
<?php ActiveForm::end() ?>
