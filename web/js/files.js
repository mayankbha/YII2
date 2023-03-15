$(function() {
    var uploadFragmentUrl = null,
        uploadFinishUrl = null,
        alert = $('.alert-danger'),
        alertMessageArea = alert.find('.error-message'),
        uploadToServerButton = null,
        executeButton = $('.execute-btn'),
        dropzone = $('.dropzone'),
        urlCheckExecute = null,
        urlRemove = null,
        executeResponse = null;

    $(document).on('click', '.upload-server-btn:not(.is-active)', function () {
        var t = $(this),
            family = t.attr('data-family'),
            category = t.attr('data-category'),
            fileName = t.attr('data-file-name');

        uploadToServerButton = t;
        uploadFragmentUrl = t.attr('data-url-fragment');
        uploadFinishUrl = t.attr('data-url-finish');
        urlRemove = t.attr('data-url-remove');

        $.ajax({
            type: 'POST',
            cache: false,
            url: t.attr('data-url'),
            data: {
                family: family,
                category: category,
                file_name: fileName
            },
            success: function (data) {
                alert.hide();

                if (data.status == 'success') {
                    t.addClass('is-active').prop('disabled', true);
                    uploadFileFragment(t, fileName, data.response, 0, 1);
                } else if (data.status == 'error') {
                    alert.show();
                    alertMessageArea.html(data.message);
                    t.removeClass('is-active').prop('disabled', false);
                }
            },
            error: function (data) {
                alert.show();
                alertMessageArea.html(data.responseJSON.message);
                t.removeClass('is-active').prop('disabled', false);
            }
        });
    });

    $(document).on('click', '.execute-btn', function () {
        var t = $(this),
            url = t.attr('data-url'),
            pk = t.attr('data-pk');

        urlCheckExecute = t.attr('data-check-status');
        $.ajax({
            type: 'POST',
            cache: false,
            url: url,
            data: {
                pk: pk
            },
            success: function (data) {
                alert.hide();

                if (data.status == 'success') {
                    $('<div />', {class: 'loader pull-right', style: 'margin: 6px 10px 0 0'}).insertAfter(t);
                    t.text(data.response['job_status'] + '..').prop('disabled', true).removeClass('btn-success').addClass('btn-primary');
                    executeResponse = data.response;

                    checkExecuteStatus(data.response['job_pk']);
                } else if (data.status == 'error') {
                    executeResponse = null;
                    alert.show();
                    alertMessageArea.html(data.message);
                }
            },
            error: function (data) {
                executeResponse = null;

                alert.show();
                alertMessageArea.html(data.responseJSON.message);

                t.text('In process..').prop('disabled', false).removeClass('btn-primary').addClass('btn-success');
            }
        });
    });
    function checkExecuteStatus (pk) {
        if (executeResponse) {
            $.ajax({
                type: 'POST',
                cache: false,
                url: urlCheckExecute,
                data: {
                    pk: pk
                },
                success: function (data) {
                    if (data.status == 'error') {
                        executeResponse = null;
                        alert.show();
                        alertMessageArea.html(data.message);

                        executeButton.text('Execute').prop('disabled', false);
                        executeButton.next('.loader').remove();
                    } else if (data.status == 'completed') {
                        executeButton.text('Completed').prop('disabled', true).addClass('btn-success').removeClass('btn-primary');
                        executeButton.next('.loader').remove();
                    } else {
                        executeButton.text(data.status + '..').prop('disabled', true).removeClass('btn-success').addClass('btn-primary');
                        checkExecuteStatus(pk);
                    }
                },
                error: function (data) {
                    executeResponse = null;

                    alert.show();
                    alertMessageArea.html(data.responseJSON.message);
                }
            });
        }
    }

    function uploadFileFragment (object, fileName, initResponse, offset, chunk) {
        var progressObject = dropzone.find('.dz-progress'),
            present;

        $.ajax({
            type: 'POST',
            cache: false,
            url: uploadFragmentUrl,
            data: {pk: initResponse['file_container_pk'], file_name: fileName, offset: offset, chunk: chunk},
            success: function (data) {
                alert.hide();

                if (data.status == 'completed') {
                    present = Math.round(data.response.offset * 100 / parseInt(data.response.size));
                    present = (present > 100) ? 100 : present;
                    progressObject.find('.dz-upload').addClass('is-uploading-to-server').css('width', present + '%');

                    uploadFileFragment(object, fileName, initResponse, data.response.offset, data.response.chunk);
                } else if (data.status == 'success') {
                    uploadFileFinish(object, initResponse, fileName);
                } else if (data.status == 'error') {
                    alert.show();
                    alertMessageArea.html(data.message);
                    t.removeClass('is-active').prop('disabled', false);
                }
            },
            error: function (data) {
                alert.show();
                alertMessageArea.html(data.responseJSON.message);
                uploadToServerButton.removeClass('is-active').prop('disabled', false);
            }
        });
    }

    function uploadFileFinish (object, initResponse, fileName) {
        $.ajax({
            type: 'POST',
            cache: false,
            url: uploadFinishUrl,
            data: {pk: initResponse['file_container_pk']},
            success: function (data) {
                alert.hide();

                if (data.status == 'success') {
                    uploadSuccessHelper(object, initResponse['file_container_pk']);
                    $.post(urlRemove, {file_name: fileName});
                } else if (data.status == 'error') {
                    alert.show();
                    alertMessageArea.html(data.message);
                    t.removeClass('is-active').prop('disabled', false);
                }
            },
            error: function (data) {
                alert.show();
                alertMessageArea.html(data.responseJSON.message);
                uploadToServerButton.removeClass('is-active').prop('disabled', false);
            }
        });
    }

    function uploadSuccessHelper (object, pk) {
        var progress = object.prev('.dropzone').find('.dz-progress');

        progress.css('opacity', 0);
        object.removeClass('is-active').removeClass('btn-success').addClass('btn-danger').prop('disabled', true);
        executeButton.addClass('btn-success').removeClass('btn-primary').prop('disabled', false).attr('data-pk', pk);

        alert.hide();
    }
});