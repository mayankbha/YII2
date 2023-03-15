<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 */

/* @var $this yii\web\View */

use yii\helpers\Html;
?>

<div class="modal fade" id="setting-library-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?= Yii::t('app', 'Simple search configuration') ?></h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label><?= Yii::t('app', 'Library name') ?></label>
                    <?= Html::input('text', 'screen_lib', null, ['disabled' => 'disabled', 'class' => 'form-control library-fs-modal-name']); ?>
                </div>
                <div class="form-group">
                    <label><?= Yii::t('app', 'Function name') ?></label>
                    <?= Html::input('text', 'screen_func', null, ['disabled' => 'disabled', 'class' => 'form-control library-fs-function-name']); ?>
                </div>
                <div class="form-group">
                    <label><?= Yii::t('app', 'Search field label') ?></label>
                    <?= Html::input('text', 'search_function_label', null, ['class' => 'form-control search-function-label']) ?>
                </div>
                <div class="render-configure">

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" data-dismiss="modal"><?= Yii::t('app','Close') ?></button>
                <button type="button" class="btn btn-primary btn-save-settings"><?= Yii::t('app','Save')?></button>
            </div>
        </div>
    </div>
</div>