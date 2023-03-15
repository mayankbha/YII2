<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;

$this->title = Yii::t('app', 'Auto fill');
?>

<h1><?= $this->title ?></h1>
<?php \app\components\ThemeHelper::printFlashes(); ?>

<div class="alert alert-danger alert-dismissible" role="alert" style="display: none">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
    <span class="alert-icon">
        <span class="icon"></span>
    </span>
    <span class="error-message">
    </span>
</div>
<div class="border-form-block">
    <div class="form-group">
        <?= Html::label(Yii::t('app', 'Table List'), 'table-list', ['class' => 'control-label']) ?>
        <?= Html::dropDownList('table_list', null, ArrayHelper::map($tablesInfo, 'table_name', 'table_name'), [
            'id' => 'autofill-table-list',
            'class' => 'form-control',
            'multiple' => true,
            'required' => true,
            'size' => 10
        ]) ?>
    </div>
    <div class="button-block">
        <div class="repopulate-section">
            <?= Html::label(Yii::t('app', 'Repopulate tables'), 'repopulate-tables', ['class' => 'control-label']) ?>
            <?= Html::checkbox('repopulate-tables', false, ['id' => 'repopulate-tables']); ?>
        </div>
        <?= Html::submitButton(Yii::t('app', 'Fill'), ['class' => 'btn btn-primary js-auto-fill']); ?>
    </div>
</div>
<input type="hidden" class="wait-message" value="<?= Yii::t('app', 'Filling table. Please wait...') ?>">