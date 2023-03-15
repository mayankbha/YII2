<div class="modal fade" id="internationalization-modal" tabindex="-1" role="dialog" aria-labelledby="internationalizationModal">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form action="#" method="post">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title"><?= Yii::t('app', 'Internationalization') ?> <span class="heading"></span></h4>
                </div>
                <div class="modal-body">
                    <?php if (!empty($languages)): ?>
                        <div class="row">
                            <?php foreach($languages as $key => $language): ?>
                                <div class="col-sm-12 form-group">
                                    <label class="control-label"><?= $language ?></label>
                                    <input type="text" class="form-control" name="<?= $key ?>" />
                                </div>
                            <?php endforeach ?>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-danger"><?= Yii::t('app','Languages is not set') ?></div>
                    <?php endif ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline" data-dismiss="modal"><?= Yii::t('app', 'Close')?> </button>
                    <button type="button" class="btn btn-outline" onclick="$(this).parents('form')[0].reset();"><?= Yii::t('app','Reset') ?></button>
                    <button type="submit" class="btn btn-primary"><?= Yii::t('app','Apply') ?></button>
                </div>
            </form>
        </div>
    </div>
</div>