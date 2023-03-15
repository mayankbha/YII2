<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

?>

<div class="modal fade" id="upload-csv-modal" tabindex="-1" role="dialog" aria-labelledby="tableModal">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <?php $form = ActiveForm::begin(['action' =>['table/upload-csv/'.$model->table_name],'options' => ['id' => 'csv-upload-modal-form', 'enctype' => 'multipart/form-data']]) ?>
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title"><?= Yii::t('app', 'Upload CSV') ?></h4>
                </div>

                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <?= Html::fileInput('TableForm[csv_file]', null, ['accept' => '.xls, .csv']) ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline" data-dismiss="modal"><?= Yii::t('app','Close') ?></button>

                    <button type="submit" class="btn btn-primary" ><?= Yii::t('app','Upload CSV') ?></button>
                </div>
            <?php ActiveForm::end() ?>
        </div>
    </div>
</div>