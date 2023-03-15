<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 */

/**
 * @var $pullUpList array
 */

use yii\grid\GridView;
?>
<div class="modal fade" id="pullup-modal" tabindex="-1" role="dialog" aria-labelledby="tableModal">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title"><?= Yii::t('app', 'List params') ?></h4>
            </div>
            <div class="modal-body">
                <?= GridView::widget([
                    'dataProvider' => $pullUpList,
                    'layout'=>"{items}\n{pager}",
                    'columns' => [
                        ['class' => 'yii\grid\RadioButtonColumn'],
                        'list_name',
                    ],
                    'tableOptions' => [
                        'class' => 'table table-hover table-pullup'
                    ],
                ]);
                ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" data-dismiss="modal"><?= Yii::t('app', 'Close')?></button>
                <button type="submit" class="btn btn-primary btn-save-pullup" data-dismiss="modal"><?= Yii::t('app','Save')?></button>
            </div>
        </div>
    </div>
</div>