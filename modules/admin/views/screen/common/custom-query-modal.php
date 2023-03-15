<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 * @var $this yii\web\View
 */

?>

<div class="modal fade" id="search-configuration-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?= Yii::t('app', 'Multi-search with custom query') ?></h4>
            </div>
            <div class="modal-body"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" data-dismiss="modal"><?= Yii::t('app','Close') ?></button>
                <button type="button" id="btn-save-search-configuration" class="btn btn-primary"><?= Yii::t('app','Save') ?></button>
            </div>
        </div>
    </div>
</div>