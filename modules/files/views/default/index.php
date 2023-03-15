<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 *
 * @var $this yii\web\View
 * @var $documentGroupInfo array
 */

use yii\helpers\Url;
use kato\DropZone;

$this->title = Yii::t('app', 'Execute files');

$containerID = 'drop-zone-' . str_replace(".", "", microtime(true));
$this->registerJs("$('#$containerID')[0].dropzone.destroy()");
?>
<h1><?= $this->title ?></h1>
<div class="alert alert-danger alert-dismissible" role="alert" <?php if ($documentGroupInfo['family'] && $documentGroupInfo['category']): ?>style="display: none"<?php endif ?>>
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
    <span class="alert-icon">
        <span class="icon"></span>
    </span>
    <span class="error-message">
        <?php if (!$documentGroupInfo['family'] || !$documentGroupInfo['category']): ?>
            Has no permissions
        <?php endif ?>
    </span>
</div>

<?php if ($documentGroupInfo['family'] && $documentGroupInfo['category']): ?>
    <?= DropZone::widget([
        'dropzoneContainer' => $containerID,
        'uploadUrl' => Url::to(['/files/async/upload'], true),
        'options' => [
            'paramName' => "file",
            'uploadMultiple' => false,
            'maxFiles' => 1,
            'acceptedFiles' => '.sql, .xlsx,.xls,.csv, text/plain',
            'previewTemplate' => '
                <div class="dz-preview dz-file-preview">
                    <div class="dz-details">
                        <div class="dz-remove" data-dz-remove><span class="glyphicon glyphicon-remove"></span></div>
                        <div class="dz-size"><span data-dz-size></span></div>
                        <div class="dz-filename"><span data-dz-name></span></div>
                    </div>
                    <div class="dz-progress"><span class="dz-upload" data-dz-uploadprogress></span></div>
                    <div class="dz-success-mark"><span class="glyphicon glyphicon-ok"></span></div>
                    <div class="dz-error-mark"><span class="glyphicon glyphicon-exclamation-sign"></span></div>
                    <div class="dz-error-message"><span data-dz-errormessage></span></div>
                </div>
            '
        ],
        'clientEvents' => [
            'addedfile' => "function(file, xhr) {
                $(file.previewElement).parents('.dropzone').find('.dz-message').hide();
            }",
            'success' => "function(file, xhr) {
                var me = $(file.previewElement),
                    message = me.parents('.dropzone').find('.dz-message'),
                    uploadArrowButton = $('.upload-server-btn'),
                    progress = me.parents('.dropzone').find('.dz-progress'),
                    execute = $('.execute-btn');
                
                me.find('.dz-remove').click(function () {
                    message.show();
                    uploadArrowButton.prop('disabled', true).addClass('btn-danger').removeClass('btn-success');
                    uploadArrowButton.attr('data-file-name', '');
                    
                    uploadArrowButton.parent().find('input[type=\"hidden\"]').val('');
                    execute.text('Execute file').prop('disabled', true).removeClass('btn-success').addClass('btn-primary');
                    $('.alert-danger').hide();
                });
                
                progress.css('opacity', '1');
                uploadArrowButton.prop('disabled', false).removeClass('btn-danger').addClass('btn-success');
                uploadArrowButton.attr('data-file-name', xhr.name);
            }",
            'removedfile' => "function (file) {
                var response = JSON.parse(file.xhr.response);
                $.post('" . Url::to(['/files/async/delete'], true) . "', {file_name: response.name}).done(function(data) {
                    console.log('File \"' + response.name + '\" has been deleted');
                });
            }"
        ],
    ]) ?>
    <br />
    <?= \yii\helpers\Html::button('Upload file to API server', [
        'class' => 'btn btn-danger upload-server-btn',
        'disabled' => true,
        'data-family' => $documentGroupInfo['family'],
        'data-category' => $documentGroupInfo['category'],
        'data-url' => Url::to(['/files/async/init-upload'], true),
        'data-url-fragment' => Url::to(['/files/async/upload-fragment'], true),
        'data-url-finish' => Url::to(['/files/async/finish-upload'], true),
        'data-url-remove' => Url::to(['/files/async/delete'], true)
    ]) ?>
    <?= \yii\helpers\Html::button('Execute file', [
        'class' => 'btn btn-primary execute-btn pull-right',
        'disabled' => true,
        'data-url' => Url::to(['/files/async/init-execute'], true),
        'data-check-status' => Url::to(['/files/async/execute-check-status'], true),
    ]) ?>
<?php endif ?>