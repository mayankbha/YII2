<?php

use yii\helpers\Html;
use app\modules\admin\models\Group;

$listName = Group::getListName();

?>
<div class="modal fade" id="<?= $modalID ?>" tabindex="-1" role="dialog" aria-labelledby="accessRightsModal">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form class="<?= $formClass ?>" action="#" method="post">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title"><?= Yii::t('app', 'Access rights') ?></h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-6 form-group">
                            <label for="field-access-view"><?= Yii::t('app', 'View') ?></label>
                            <select id="field-access-view" class="form-control" name="access_view" rows="3" multiple>
                                <?= Html::renderSelectOptions('', $listName) ?>
                            </select>
                        </div>

                        <div class="col-sm-6 form-group">
                            <label for="field-access-update"><?= Yii::t('app', 'Update') ?></label>
                            <select id="field-access-update" class="form-control" name="access_update" rows="3" multiple>
                                <?= Html::renderSelectOptions('', $listName) ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline" data-dismiss="modal"><?= Yii::t('app', 'Close') ?></button>
                    <button type="submit" class="btn btn-primary btn-save-access"><?= Yii::t('app', 'Save') ?></button>
                </div>
            </form>
        </div>
    </div>
</div>