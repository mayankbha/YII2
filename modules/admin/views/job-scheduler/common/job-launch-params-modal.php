<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 */

/* @var $this yii\web\View */

?>

<div class="modal fade" id="job-launch-params-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>

                <h4 class="modal-title"><?= Yii::t('app', 'Launch Params') ?></h4>
            </div>

            <div class="modal-body">
                <div id="jstree"></div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-outline" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary btn-launch-params-done" data-dismiss="modal">Done</button>
            </div>
        </div>
    </div>
</div>