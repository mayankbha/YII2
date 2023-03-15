<?php

use yii\bootstrap\Html;

?>
<div class="ui-editor">
    <div class="panel panel-default field-wrapper" data-id="cjss221" style="display: none">
        <div data-name="<?=$section?>" class="js-code-template" style="display: none;">
if (parseFloat(this.value) >= maxValue) {
  throw new Error(JSON.stringify(maxValueError));
}
        </div>
        <button class="btn btn-warning btn-xs cjs-section-remove-btn"><span class="glyphicon glyphicon-remove"></span></button>
        <div class="panel-heading">Check max size</div>
        <div class="panel-bod">
            <div class="form-group">
                <div class="col-sm-12 common-row-js">
                    <?= Html::label('Max value', $section .'_maxValue', ['class' => 'control-label']) ?>
                    <?= Html::input('text', $section .'_maxValue' ,null, [
                        'id' => 'max-value',
                        'data-name' => 'maxValue',
                        'class' => 'js-generator-data js-section-marker common-generator-input'
                    ]) ?>
                </div>
                <div class="input-group  common-row-js" data-target="internationalization-tooltip" data-original-title="" title="">
                    <?= Html::label('Error message', 'common_'.$section .'_maxValueError', ['class' => 'control-label']) ?>
                    <?= Html::input('text', 'common_'.$section .'_maxValueError' ,null, [
                        'id' => 'common_'.$section .'_maxValueError',
                        'data-name' => 'maxValueError',
                        'class' => 'js-generator-data i18n-data common-generator-input'
                    ]) ?>
                    <span class="input-group-btn">
                        <button class="btn btn-default common-btn-int" data-toggle="modal" data-target="#internationalization-modal" data-internationalization="#common_<?= $section ?>_maxValueError" type="button">
                            Internationalization
                        </button>
                    </span>
                </div>
            </div>
<?php if($section == 'js_event_change'): ?>
            <div class="form-group">
                <div class="col-sm-10">
                        <?= Html::label('Use confirm modal ',  'common_'.$section .'_maxValueError_modal') ?>

                        <?= Html::checkbox('common_'.$section .'_maxValueError_modal', null, [
                            'id' =>  'common_'.$section .'_maxValueError_modal',
                            'data-name' => 'maxValueError',
                            'class' => 'error-modal-type',
                        ]) ?>
                </div>
            </div>
<?php endif; ?>
        </div>
    </div>
    <div class="panel panel-default field-wrapper" data-id="cjss224" style="display: none">
        <div data-name="<?=$section?>" class="js-code-template" style="display: none;">
if (parseFloat(this.value) <= minValue) {
    throw new Error(JSON.stringify(minValueError));
}
        </div>
        <button class="btn btn-warning btn-xs cjs-section-remove-btn"><span class="glyphicon glyphicon-remove"></span></button>
        <div class="panel-heading">Check min size</div>
        <div class="panel-bod">
            <div class="form-group">
                <div class="col-sm-12 common-row-js">
                    <?= Html::label('Min value', $section .'_minValue', ['class' => 'control-label']) ?>
                    <?= Html::input('min_value', $section .'_minValue', null, [
                        'id' => 'min-value',
                        'data-name' => 'minValue',
                        'class' => 'js-generator-data js-section-marker common-generator-input'
                    ]) ?>
                </div>
                <div class="input-group common-row-js" data-target="internationalization-tooltip" data-original-title="" title="">
                    <?= Html::label('Error message', 'common_'.$section .'_minValueError', ['class' => 'control-label']) ?>
                    <?= Html::input('error_message', 'common_'.$section .'_minValueError' ,null, [
                        'id' => 'common_'.$section .'_minValueError',
                        'data-name' => 'minValueError',
                        'class' => 'js-generator-data i18n-data common-generator-input'
                    ]) ?>
                    <span class="input-group-btn">
                        <button class="btn btn-default common-btn-int" data-toggle="modal" data-target="#internationalization-modal" data-internationalization="#common_<?= $section ?>_minValueError" type="button">
                            Internationalization
                        </button>
                    </span>
                </div>
            </div>
<?php if($section == 'js_event_change'): ?>
            <div class="form-group">
                <div class="col-sm-10">
                    <?= Html::label('Use confirm modal ',  'common_'.$section .'_minValueError_modal') ?>

                    <?= Html::checkbox('common_'.$section .'_minValueError_modal', null, [
                        'id' =>  'common_'.$section .'_minValueError_modal',
                        'class' => 'error-modal-type',
                    ]) ?>
                </div>
            </div>
<?php endif; ?>
        </div>
    </div>
    <div class="panel panel-default field-wrapper" data-id="cjss222" style="display: none">
        <div data-name="<?=$section?>" class="js-code-template" style="display: none;">
var anotherInput = document.getElementById(inputId);
if (this.value != anotherInput.value) {
    throw new Error(JSON.stringify(compareFieldsError));
}
        </div>
        <button class="btn btn-warning btn-xs cjs-section-remove-btn"><span class="glyphicon glyphicon-remove"></span></button>
        <div class="panel-heading">Compare fields</div>
        <div class="panel-bod">
            <div class="form-group">
                <div class="col-sm-12 common-row-js">
                    <?= Html::label('Another input(id)', $section .'_inputId', ['class' => 'control-label']) ?>
                    <?= Html::input('input_id', $section .'_inputId', null,[
                        'data-name' => 'inputId',
                        'class' => 'js-generator-data js-section-marker common-generator-input'
                    ]) ?>
                </div>
                <div class="input-group common-row-js" data-target="internationalization-tooltip" data-original-title="" title="">
                    <?= Html::label('Error message', 'common_'.$section .'_compareError', ['class' => 'control-label']) ?>
                    <?= Html::input('error_message', 'common_'.$section .'_compareError', null, [
                        'id' =>  'common_'.$section .'_compareError',
                        'data-name' => 'compareFieldsError',
                        'class' => 'js-generator-data i18n-data common-generator-input'
                    ]) ?>
                    <span class="input-group-btn">
                        <button class="btn btn-default common-btn-int" data-toggle="modal" data-target="#internationalization-modal" data-internationalization="#common_<?= $section ?>_compareError" type="button">
                            Internationalization
                        </button>
                    </span>
                </div>
            </div>
<?php if($section == 'js_event_change'): ?>
            <div class="form-group">
                <div class="col-sm-10">
                    <?= Html::label('Use confirm modal ',  'common_'.$section .'_compareError_modal') ?>

                    <?= Html::checkbox('common_'.$section .'_compareError_modal', null, [
                        'id' =>  'common_'.$section .'_compareError_modal',
                        'class' => 'error-modal-type',
                    ]) ?>
                </div>
            </div>
<?php endif; ?>
        </div>
    </div>
    <div class="panel panel-default field-wrapper" data-id="cjss223" style="display: none">
        <div data-name="<?=$section?>" class="js-code-template" style="display: none;">
this.value = this.value.toUpperCase();
        </div>
        <button class="btn btn-warning btn-xs cjs-section-remove-btn"><span class="glyphicon glyphicon-remove"></span></button>
        <div class="panel-heading">To Upper case</div>
        <?=Html::hiddenInput($section .'_toUpperCase', null, ['class' => 'js-section-marker without-var-section']) ?>
    </div>
    <div class="panel panel-default field-wrapper" data-id="cjss225" style="display: none">
        <div data-name="<?=$section?>" class="js-code-template" style="display: none;">
this.value = this.value.toLowerCase();
        </div>
        <button class="btn btn-warning btn-xs cjs-section-remove-btn"><span class="glyphicon glyphicon-remove"></span></button>
        <div class="panel-heading">To Lower case</div>
        <?=Html::hiddenInput($section .'_toLowerCase', null, ['class' => 'js-section-marker without-var-section']) ?>
    </div>
</div>