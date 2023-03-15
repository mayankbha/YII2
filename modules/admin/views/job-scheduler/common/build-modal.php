<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 */

/* @var $this yii\web\View */
/* @var $model \app\modules\admin\models\forms\ScreenForm */

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use app\models\CustomLibs;

$jobs_params = json_decode($model->jobs_params);
?>

<div class="modal fade" id="setting-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?= Yii::t('app', 'Section settings') ?></h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label><?= Yii::t('app', 'Library name') ?></label>
					<?php echo Html::dropDownList('lib_name', $jobs_params->function_extensions_job_params->lib_name, [$jobs_params->function_extensions_job_params->lib_name => $jobs_params->function_extensions_job_params->lib_name], ['disabled' => 'disabled', 'class' => 'form-control screen-lib']); ?>
                </div>
                <div class="form-group layout-label-group" style="display: none">
                    <label class="control-label" for="form-layout_label"><?= Yii::t('app', 'Layout label') ?> <sup style="color: grey"><?= Yii::t('app','Default value')?></sup></label>
                    <div class="input-group" data-target="internationalization-tooltip">
                        <input type="text" id="form-layout_label" class="form-control layout_label" name="layout_label" value="" aria-required="true">
                        <span class="input-group-btn">
                            <button class="btn btn-default" data-toggle="modal"  data-target="#internationalization-modal" data-internationalization="#form-layout_label" type="button">
                                <?= Yii::t('app','Internationalization') ?>
                            </button>
                        </span>
                    </div>
                </div>
                <div class="form-group section-type">
                    <label class="radio-inline">
                        <input type="radio" name="section-type" value="LIST"> <?= Yii::t('app', 'Edit type') ?>
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="section-type" value="TABLE"> <?= Yii::t('app', 'Table type') ?>
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="section-type" value="CHART-PIE"> <?= Yii::t('app', 'Chart-pie type') ?>
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="section-type" value="CHART-LINE"> <?= Yii::t('app', 'Chart-line type') ?>
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="section-type" value="CHART-BAR-HORIZONTAL"> <?= Yii::t('app', 'CHART-BAR-HORIZONTAL type') ?>
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="section-type" value="CHART-BAR-VERTICAL"> <?= Yii::t('app', 'CHART-BAR-VERTICAL type') ?>
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="section-type" value="CHART-DOUGHNUT"> <?= Yii::t('app', 'CHART-DOUGHNUT type') ?>
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="section-type" value="DOCUMENT"> <?= Yii::t('app', 'Document type') ?>
                    </label>
                </div>
                <div class="form-group func-block-wrapper">
                    <div class="form-group">
                        <label><?= Yii::t('app','Function name for getting list')?></label>
                        <?= Html::dropDownList('screen_lib_func', null, [], ['prompt' => '', 'class' => 'form-control screen-lib-func']); ?>
                    </div>
                </div>
                <div class="render-configure"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" data-dismiss="modal"><?= Yii::t('app','Close') ?></button>
                <button type="button" data-toggle="modal" data-target="#formatting-modal-section" class="btn btn-success section-formatting-button"><?= Yii::t('app','Formatting section fields') ?></button>
                <button type="button" class="btn btn-primary btn-save-settings"><?= Yii::t('app','Save') ?></button>
            </div>
        </div>
    </div>
</div>