<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 */

/* @var $this yii\web\View */
?>

<div class="modal fade" id="execute-function-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?= Yii::t('app', 'Execute function') ?></h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <?= \yii\helpers\Html::label('Library', 'execute_library', ['label-control']) ?>
                    <?= \yii\helpers\Html::dropDownList('execute_library', null, [$model->screen_lib => $model->screen_lib], [
                        'class' => 'form-control',
                        'id' => 'execute_library',
                        'disabled' => true
                    ]) ?>
                </div>
                <div class="form-group">
                    <?= \yii\helpers\Html::label('Function', 'execute-function', ['label-control']) ?>
                    <?= \yii\helpers\Html::dropDownList('execute_function', null, [], [
                        'class' => 'form-control nt-save-form',
                        'id' => 'execute-function',
                        'disabled' => true
                    ]) ?>
                </div>
                <div class="form-group">
                    <?= \yii\helpers\Html::label('Custom function', 'execute-custom', ['label-control']) ?>
                    <?= \yii\helpers\Html::dropDownList('execute_custom', null, [], [
                        'class' => 'form-control nt-save-form',
                        'id' => 'execute-custom',
                        'disabled' => true
                    ]) ?>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline execute-close" data-dismiss="modal"><?= Yii::t('app','Close') ?></button>
                <button type="button" class="btn btn-primary execute-save"><?= Yii::t('app','Save') ?></button>
            </div>
        </div>
    </div>
</div>