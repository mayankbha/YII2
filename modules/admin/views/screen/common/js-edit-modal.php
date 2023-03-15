<?php

use yii\bootstrap\Tabs;
use yii\helpers\ArrayHelper;
use yii\bootstrap\Html;

?>
<div class="modal fade jsc-template-modal" id="<?= $modalID ?>" tabindex="-1" role="dialog" aria-labelledby="jsEditModal">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form class="<?= $formClass ?>" action="#" method="post">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title"><?= Yii::t('app', 'Custom Java script') ?></h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12 form-group">
                            <?= Tabs::widget([
                                'items' => [
                                    [
                                        'label' => Yii::t('app', 'custom javascript'),
                                        'content' => $this->render('_custom_js_form'),
                                        'active' => true,
                                        'options' => [
                                            'class' => 'custom-javascript-tab'
                                        ]
                                    ],
                                    [
                                        'label' => Yii::t('app', 'common javascript'),
                                        'content' => $this->render('_common_js_form'),
                                        'options' => [
                                            'class' => 'common-javascript-tab'
                                        ]
                                    ],
                                ],
                            ]); ?>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="row">
                        <div class="col-sm-6">
                            <?= Html::dropDownList(null, null, $jsTemplates, [
                                'prompt' => 'Select JS template',
                                'class' => 'form-control js-templates'
                            ]); ?>
                        </div>
                        <div class="col-sm-6">
                            <button type="button" class="btn btn-outline" data-dismiss="modal"><?= Yii::t('app', 'Close')?></button>
                            <button type="submit" class="btn btn-primary btn-save-table"><?= Yii::t('app','Save')?></button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>