$(function() {
    $(document).on('click', '.js-style-switcher', function(){
        var panelBody = $(this).parents('.panel-body');
        var useImages = $(this).find('input[type="radio"]').val();
        switchBodySettings(panelBody, useImages);
    });

    $(document).on('change', '.js-security-filter', function () {
        var url = $(this).data('security-filter-url');
        loadSecuritySpec(url);
    });

    $(document).on('click', '.js-delete-image', function () {
        var url = $('#create-user-form').data('delImageUrl');
        var requestData =  {
            'id': $('#create-user-form').data('modelId'),
            'image': $(this).siblings('input[type="radio"]').val(),
            'attribute': $(this).data('modelAttr'),
        };

        deleteImage(url, requestData);
        $(this).parent('.img-thumbnail-wrapper').html('');
    });

    $(document).on('change', '.js-tenant-options', function () {
        var url = $(this).data('url');
        if (typeof tenantOptions[$(this).val()] != 'undefined') {
        var requestData =  tenantOptions[$(this).val()];
        $.ajax({
            type: 'POST',
            url: url,
            data: requestData
        }).done(function (data) {
                $.each(data, function(key, field) {
                    if (field) {
                        $('#userstyletemplateform-' + key).val(field);
                        if ($('#userstyletemplateform-' + key).hasClass('spectrum-input')) {
                            $('#userstyletemplateform-' + key + '-source').spectrum('set', field);
                        }
                    }
                });
            });
        }
    });

    $(document).on('change','.js-switch-security', function () {
        switchSecurity(this);
    });


    $(document).on('click', '.js-user-submit', function () {
        firstWithError();
    });

    function loadSecuritySpec(url,selected){
        var tenant = $( '#user_tenant' ).val();
        var accountType = $( '#user_account_type').val();
        if(tenant.length>0 && accountType.length>0){
            $('.js-disabled-input').prop('disabled',true);
            $('label[for="sec1"]').html('');
            $('label[for="sec2"]').html('');
            $.ajax({
                type: "POST",
                url: url,
                data: {'tenant':tenant,'account_type':accountType,'selected':selected},
                success: function( returnedData ) {
                    if(returnedData.length>0){
                        $('#ast').prop('disabled', false);
                        $('#ast').html( returnedData );
                        $('.js-switch-security').trigger('change');
                    }
                    else {
                        $('#ast').prop('disabled', true);
                        $('#ast').html( '' );
                        $('#sec1_len').val('');
                        $('#sec2_len').val('');
                        $('.js-disabled-input').val(null);
                        $('.js-disabled-input').prop('disabled',true);
                        $('label[for="sec1"]').html('');
                        $('label[for="sec2"]').html('');
                    }
                }
            });
        }
        else{
            $('#ast').prop('disabled', true);
            $('#ast').html( '' );
            $('#sec1_len').val('');
            $('#sec2_len').val('');
            $('.js-disabled-input').val(null);
            $('.js-disabled-input').prop('disabled',true);
            $('label[for="sec1"]').html('');
            $('label[for="sec2"]').html('');
        }
    }



    function switchSecurity(e){
        var option = $(e).find(":selected");
        if(option.val().length>0){
            var sec1len =option.attr('data-filter1_length');
            var sec2len =option.attr('data-filter2_length');
            $('label[for="sec1"]').html(option.attr('data-filter1') + ' (length:'+sec1len+')');
            $('label[for="sec2"]').html(option.attr('data-filter2') + ' (length:'+sec2len+')');
            $('#sec1_len').val(sec1len);
            $('#sec2_len').val(sec2len);
            $('.js-disabled-input').prop('disabled',false);
        }
    }

    function firstWithError()
    {
        var id = $('div.form-group.has-error:first').parents('.tab-pane').attr('id');
        if(id !== undefined)
        {
            $(".nav.nav-tabs").find("[aria-controls='"+id+"']").trigger('click');
        }
    }

    function deleteImage(url, requestData) {
        $.ajax({
            type: 'POST',
            url: url,
            data: requestData
        }).done(function (data) {
            console.log(data);
        });
    }

    function switchBodySettings(panelBody, useImages){
        var styleWrapper = panelBody.find('.style-case-wrapper');
        var imageWrapper = panelBody.find('.image-case-wrapper');
        if (useImages) {
            imageWrapper.fadeIn(400);
            styleWrapper.hide();
        } else {
            styleWrapper.fadeIn(400);
            imageWrapper.hide();
        }
    }

    $('label[for="sec1"]').html('');
    $('label[for="sec2"]').html('');
    var selected = $('.js-switch-security').data('value');
    loadSecuritySpec($('.js-security-filter').data('security-filter-url'),selected);
});