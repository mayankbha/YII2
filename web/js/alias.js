var BASE_SERVER = null;
var BASE_URL = null;

function setAliasBaseUrl(url) {
    BASE_SERVER = url;
    BASE_URL = BASE_SERVER + "admin/alias/";
}

$(document).ready(function () {
	$('#aliasdependencyform-dependencytype').change(function () {
		if($(this).val() == 'REL') {
			var con = confirm('Are you sure you want to change as it will delete all dependents and clear the list?');

			if(con) {
				$('.add-to-list').attr('disabled', true);
				$("#w0 .alias-subvalues-list table>tbody").html('');
			} else {
				$('.add-to-list').attr('disabled', false);

				$(this).val('PKS');
			}

			//$('.group-list').attr('disabled', true);
			//$('.btn-outline').attr('disabled', true);
		} else {
			$('.add-to-list').removeAttr('disabled');
			//$('.group-list').removeAttr('disabled');
			//$('.btn-outline').removeAttr('disabled');
		}
	});

    if ($(".update_form").length > 0) {
        $('#aliasform-aliasmodule input').prop('checked', false);
        $('#aliasform-aliasmodule input[value="' + $("#AliasForm_AliasModule").val() + '"]').prop('checked', true);
    }

    $(".record_delete").click(function () {
        if (confirm("Are you sure you want to delete this item?")) {

        }
        else {
            return false;
        }
    });

    $("#reloadAliases").click(function () {
        console.log("reloading aliases...");
        if (!$(".notice").parent().hasClass('alert-warning')) $(".notice").parent().removeClass('alert-danger').addClass('alert-warning').removeClass('alert-success');
        $(".notice").html("<b>Processing...</b>"); 
        $.ajax({
            method: "POST",
            url: BASE_URL + "api/reloadAliases",
            data: {ajax: 'true'}
        })
            .done(function (data) {
                console.log(data);
                if (data.length > 2 && data.search("unsuccessfully") == -1 && data.search("false") == -1) {
                    if (!$(".notice").parent().hasClass('alert-success')) $(".notice").parent().removeClass('alert-danger').removeClass('alert-warning').addClass('alert-success');
                    $(".notice").html("<b>Done. Alias records reloaded successfully.</b>");
                }
                else {
                    if (!$(".notice").parent().hasClass('alert-danger')) $(".notice").parent().removeClass('alert-warning').removeClass('alert-success').addClass('alert-danger');
                    $(".notice").html("<b>Failed. Operation execution failure. Please try again.</b>");
                }
            })
            .fail(function (err) {
                if (!$(".notice").parent().hasClass('alert-danger')) $(".notice").parent().removeClass('alert-warning').removeClass('alert-success').addClass('alert-danger');
                $(".notice").html("<b>Failed. Cannot connect to specified URL. Please try again.</b>");
                console.log(err);
            });
    });

    if ($("form#w0").length > 0) {
		if($('#customAPI').val() == 0) {
			var removeButton = $(".remove");
			removeButton.click(function () {
				var con = confirm('Are you sure you want to delete?');

				if(con) {
					$(this).parent().parent().remove();
				}
			});
		} else {
			$("[class*='alias-subvalues-list'] table input").attr('readonly', true);
		}

        $('#aliasCode').focus(function () {
            if ($('.update_form').length > 0 && $('#aliasCode').val() != '')
                $(this).attr('readonly', true);
        });

        $("#group_dependencies").click(function () {
            var tab = $(this).attr('tab');
            tab = parseInt(tab) - 1;

			if($('#customAPI').val() == 0) {
				addListGroup(tab, "#w0 .alias-subvalues-list-group table>tbody", "AliasDependencyForm[DependentsOn][]", $('#aliasdependencyform-dependentson').val(), "preview");

				$("[class*='alias-subvalues-list'] table input").removeAttr('readonly');
			} else {
				addListGroup(tab, "#w" + tab + " .alias-subvalues-list-group table>tbody", "AliasDependencyForm[DependentsOn][]", $('#aliasdependencyform-dependentson').val(), "preview");
			}

            $("#group_dependencies_clear").trigger('click');
        });

        $("#group_dependencies_clear").click(function () {
            $('.alias-subvalues-list table > tbody').html('');
            $('#aliasdependencyform-requesttable').prop('disabled', false);
        });

        $("button.add-to-list").click(function () {
            updatePK();
            var tab = $(this).attr('tab');
            tab = parseInt(tab) - 1;
            //console.log(tab);

            assessTab(tab, "create");

			if($('#customAPI').val() != 0) {
				$('#aliasdependencyform-requesttable').prop('disabled', true);
			}
        });

		if($('#customAPI').val() != 0) {
			$('.field-aliasform-aliasdatabasetable').prop('disabled', true).hide();
		}

        $('.field-aliasform-aliasdatabasefield').prop('disabled', true).hide();

        formLogic();
        populateDatabaseTables();

        /*if($('.update_form').length>0){
            $.ajax({
              method: "POST",
              url: BASE_URL + "api/getDependents",
              data: { ajax: 'true', aliasCode: $('#aliasCode').val() }
            }).done(function( data ) {
                let tab = "2";
                var d = 0;
                console.log(data);
                $.each(JSON.parse(data), function (key, entry) {
                    console.log(entry.DependentsOn);
                    $("#group_dependencies").trigger("click");
                    addListGroup(tab, "#w" + tab + " .alias-subvalues-list-group table>tbody", "AliasDependencyForm[DependentsOn][]", entry.DependentsOn, "preview");
                    d++;
                 });


          });
        }*/

		if($('#customAPI').val() != 'arm' || $('#customAPI').val() == 0) {
			getAliasDependentsOn();
		}

		function getAliasDependentsOn() {
			$.ajax({
				method: "POST",
				url: BASE_URL + "api/getAliasCodes",
				data: {ajax: 'true', aliasCode: $('#aliasCode').val()}
			})
			.done(function (data) {
				let dropdown = $('#aliasdependencyform-dependentson');
				dropdown.empty();
				var d = 0;
				$.each(JSON.parse(data), function (key, entry) {
					//console.log(key);

					var aliasType = $('#aliasform-aliastype').val();
					var keyArr = key.split('.');

					//console.log('aliasType :: ' + aliasType);
					//console.log('keyArr :: ' + keyArr[0]);

					//dropdown.append($('<option></option>').attr('formattype', entry).attr('value', key).text(key));

					//alert(aliasType);

					/*if(aliasType != undefined && aliasType != '') {
						if((aliasType == 'Custom Generated' || aliasType == 'Custom Multi') && (keyArr[0] == 'Custom' || keyArr[0] == 'Alias')) {
							dropdown.append($('<option></option>').attr('formattype', entry).attr('value', key).text(key));
						}
					}*/

					if(keyArr[0] == 'Custom' || keyArr[0] == 'Alias') {
						if($('#aliasform-aliasdependentson_stored').val() != '') {
							dropdown.append($('<option' + (key == $('#aliasform-aliasdependentson_stored').val() ? ' selected' : '') + '></option>').attr('value', key).text(key));
						} else {
							dropdown.append($('<option></option>').attr('formattype', entry).attr('value', key).text(key));
						}
					}

					//if(d==0) dropdown.attr('selected', 'selected');

					d++;
				});
				dropdown.prepend("<option value=''>Please select field</option>");
				$(".aliasdependencyform.loading").remove();
				$("[class*=alias-subvalues-list]").show();
				dropdown.selectpicker('refresh');
			});
		}

		$('#aliasform-aliasdatabasetable').change(function () {
            $('.field-aliasform-aliasdatabasefield').hide();
            populateDatabaseFields();
        });

		$('#aliasrelationshipform-aliasparenttable').change(function () {
			var id = $(this).attr('id');
            populateDatabaseFields(id);
        });

		$('#aliasrelationshipform-aliaschildtable').change(function () {
			var id = $(this).attr('id');
            populateDatabaseFields(id);
        });

        $("#aliasform-aliastype").change(function () {
			if($('#customAPI').val() != 0) {
				$('.field-aliasform-aliasdatabasetable').prop('disabled', true).hide();
			}
            $('.field-aliasform-aliasdatabasefield').prop('disabled', true).hide();
            formLogic();

			getAliasDependentsOn();
        });

        $("#aliasform-aliasdatabasefield").change(function () {
            formLogic();
        });

        function compareStrings(a, b) {
            // Assuming you want case-insensitive comparison
            a = a.toLowerCase();
            b = b.toLowerCase();
            return (a < b) ? -1 : (a > b) ? 1 : 0;
        }

        function formLogic() {
            let dropdown = $('#aliasform-aliasdatabasetable');
            let dropdown2 = $('#aliasform-aliasdatabasefield');
            let aliasType = $("#aliasform-aliastype").val(),
                aliasdatabasetable = $('#aliasform-aliasdatabasetable').val(),
                aliasdatabasefield = $('#aliasform-aliasdatabasefield').val();
            switch (aliasType) {
                case 'Database Field':
                    $('#aliasform-aliasdatabasetable option.blank').remove();
                    $('#aliasform-aliasdatabasefield option.blank').remove();
                    $("#aliasform-aliasmodule [type=radio]").attr('disabled', 'disabled');
                    $("#aliasform-aliassqlstatement").attr('disabled', 'disabled');
                    $("#aliasform-aliassqlstatement").attr('readonly', 'readonly');
                    if ($(".update_form").length == 0) $('#aliasCode').val('Alias.' + (aliasdatabasetable == null ? '' : aliasdatabasetable) + '.' + (aliasdatabasefield == null ? '' : aliasdatabasefield));
                    $('.field-aliasform-aliasdatabasetable').prop('disabled', false).show();
                    $('.field-aliasform-aliasdatabasefield').prop('disabled', false).show();
					$('.nav-tabs li:nth-child(2)').hide();
                    $('.nav-tabs li:nth-child(3)').show();
                    $('.nav-tabs li:nth-child(4)').show();
                    $('.nav-tabs li:nth-child(5)').show();
                    break;
                case 'Array':
                    $('#aliasform-aliasdatabasetable option.blank').remove();
                    $('#aliasform-aliasdatabasefield option.blank').remove();
                    $("#aliasform-aliasmodule [type=radio]").attr('disabled', 'disabled');
                    $("#aliasform-aliassqlstatement").attr('disabled', 'disabled');
                    $("#aliasform-aliassqlstatement").attr('readonly', 'readonly');
                    if ($(".update_form").length == 0) $('#aliasCode').val('Array.' + (aliasdatabasetable == null ? '' : aliasdatabasetable) + '.' + (aliasdatabasefield == null ? '' : aliasdatabasefield));
                    $('.field-aliasform-aliasdatabasetable').prop('disabled', false).show();
                    $('.field-aliasform-aliasdatabasefield').prop('disabled', false).show();
					$('.nav-tabs li:nth-child(2)').hide();
                    $('.nav-tabs li:nth-child(3)').hide();
                    $('.nav-tabs li:nth-child(4)').hide();
                    $('.nav-tabs li:nth-child(5)').hide();
                    break;
                case 'Custom Generated':
                    $("#aliasform-aliasmodule [type=radio]").removeAttr('disabled');
                    $("#aliasform-aliassqlstatement").removeAttr('disabled');
                    $("#aliasform-aliassqlstatement").removeAttr('readonly');
                    $("#aliasform-aliasformattype").removeAttr('readonly');
                    if ($(".update_form").length == 0) {
                        $("#aliasCode").removeAttr('readonly');
                        $('#aliasCode').val('Custom.');
                    }

                    $('.field-aliasform-aliasdatabasetable').prop('disabled', true).hide();
                    $('.field-aliasform-aliasdatabasefield').prop('disabled', true).hide();

                    /*if($("#aliasform-aliasdatabasetable option.blank").length==0) dropdown.prepend('<option class="blank"></option>');
                    dropdown.prop('selectedIndex', 0);
                    if($("#aliasform-aliasdatabasefield option.blank").length==0) dropdown2.prepend('<option class="blank"></option>');
                    dropdown2.prop('selectedIndex', 0);*/
					$('.nav-tabs li:nth-child(2)').show();
                    $('.nav-tabs li:nth-child(3)').show();
                    $('.nav-tabs li:nth-child(4)').show();
                    $('.nav-tabs li:nth-child(5)').show();
                    break;
                case 'Custom Multi':
                    $("#aliasform-aliasmodule [type=radio]").removeAttr('disabled');
                    $("#aliasform-aliassqlstatement").removeAttr('disabled');
                    $("#aliasform-aliassqlstatement").removeAttr('readonly');
                    $("#aliasform-aliasformattype").removeAttr('readonly');
                    if ($(".update_form").length == 0) {
                        $("#aliasCode").removeAttr('readonly');
                        $('#aliasCode').val('Multi.' + (aliasdatabasetable == null ? '' : aliasdatabasetable) + '.' + (aliasdatabasefield == null ? '' : aliasdatabasefield));
                    }
                    $('.field-aliasform-aliasdatabasetable').prop('disabled', false).show();
                    $('.field-aliasform-aliasdatabasefield').prop('disabled', false).show();
                    /*if($("#aliasform-aliasdatabasetable option.blank").length==0) dropdown.prepend('<option class="blank"></option>');
                    if($("#aliasform-aliasdatabasefield option.blank").length==0) dropdown2.prepend('<option class="blank"></option>');*/
					$('.nav-tabs li:nth-child(2)').show();
                    $('.nav-tabs li:nth-child(3)').show();
                    $('.nav-tabs li:nth-child(4)').show();
                    $('.nav-tabs li:nth-child(5)').show();
                    break;
                case 'List Entry':
                    $("#aliasform-aliasmodule [type=radio]").removeAttr('disabled');
                    $("#aliasform-aliassqlstatement").removeAttr('disabled');
                    $("#aliasform-aliassqlstatement").removeAttr('readonly');
                    $("#aliasform-aliasformattype").removeAttr('readonly');
                    if ($(".update_form").length == 0) {
                        $("#aliasCode").removeAttr('readonly');
                        $('#aliasCode').val('List.');
                    }
                    $('.field-aliasform-aliasdatabasetable').prop('disabled', false).hide();
                    $('.field-aliasform-aliasdatabasefield').prop('disabled', false).hide();
                    if ($("#aliasform-aliasdatabasetable option.blank").length == 0) dropdown.prepend('<option class="blank"></option>');
                    dropdown.prop('selectedIndex', 0);
                    if ($("#aliasform-aliasdatabasefield option.blank").length == 0) dropdown2.prepend('<option class="blank"></option>');
                    dropdown2.prop('selectedIndex', 0);
                    $('.nav-tabs li:nth-child(2)').show();
                    $('.nav-tabs li:nth-child(3)').show();
                    $('.nav-tabs li:nth-child(4)').show();
                    $('.nav-tabs li:nth-child(5)').show();
                    break;
            }
            $('#aliasform-aliasformattype').val(aliasdatabasetable != null && aliasdatabasefield != null ? $('#aliasform-aliasdatabasefield option:selected').attr('formattype') : '');
            updatePK();
        };
    }

    function updatePK() {
        $(".pkValue").val($("#aliasCode").val());
    }

    function populateDatabaseTables() {
        $.ajax({
            method: "POST",
            url: BASE_URL + "api/getTables",
            data: {ajax: 'true'}
        })
            .done(function (data) {
                $('.field-aliasform-aliasdatabasefield').prop('disabled', true).hide();

                let dropdown = $('#aliasform-aliasdatabasetable');
                dropdown.empty();

				let relationship_parent_table_dropdown = $('#aliasrelationshipform-aliasparenttable');
				relationship_parent_table_dropdown.empty();

				let relationship_child_table_dropdown = $('#aliasrelationshipform-aliaschildtable');
				relationship_child_table_dropdown.empty();

                $.each(JSON.parse(data), function (key, entry) {
                    if ($('.update_form').length == 0) {
                        dropdown.append($('<option></option>').attr('value', entry).text(entry));
                    }
                    else {
                        /*console.log(entry);*/
                        dropdown.append($('<option' + (entry == $('#aliasform-aliasdatabasetable_stored').val() ? ' selected' : '') + '></option>').attr('value', entry).text(entry));
                    }

					if($('#aliasform-aliasdatabasetable_stored').val() != '') {
						dropdown.append($('<option' + (entry == $('#aliasform-aliasdatabasetable_stored').val() ? ' selected' : '') + '></option>').attr('value', entry).text(entry));
					}

					relationship_parent_table_dropdown.append($('<option' + (entry == $('#aliasrelationshipform-aliasparenttable_stored').val() ? ' selected' : '') + '></option>').attr('value', entry).text(entry));

					relationship_child_table_dropdown.append($('<option' + (entry == $('#aliasrelationshipform-aliaschildtable_stored').val() ? ' selected' : '') + '></option>').attr('value', entry).text(entry));
                });

				$(".common-relationship-parent-table-class").each(function(index) {
					var id = $(this).attr('id');
					populateDatabaseFields(id);
				});

                populateDatabaseFields();
            });
    }

    function populateDatabaseFields(id = '') {
		//alert(id);

		if(id != '') {
			var table = $('#'+id).val();
		} else {
			var table  = $('#aliasform-aliasdatabasetable').val();
		}

		//alert(table);

        $.ajax({
            method: "POST",
            url: BASE_URL + "api/getFields",
            data: {ajax: 'true', table: table}
        })
            .done(function (data) {
                let dropdown = $('#aliasform-aliasdatabasefield');
                dropdown.empty();

				//alert('on success id' + id);

				if(id != '' && id == 'aliasrelationshipform-aliasparenttable') {
					let relationship_parent_field_dropdown = $('#aliasrelationshipform-aliasparentfield');
					relationship_parent_field_dropdown.empty();

					$.each(JSON.parse(data), function (key, entry) {
						console.log(key);

						relationship_parent_field_dropdown.append($('<option' + (key == $('#aliasrelationshipform-aliasparentfield_stored').val() ? ' selected' : '') + '></option>').attr('formattype', entry).attr('value', key).text(key));
					});
				} else if(id != '' && id == 'aliasrelationshipform-aliaschildtable') {
					let relationship_child_field_dropdown = $('#aliasrelationshipform-aliaschildfield');
					relationship_child_field_dropdown.empty();

					$.each(JSON.parse(data), function (key, entry) {
						console.log(key);

						relationship_child_field_dropdown.append($('<option' + (key == $('#aliasrelationshipform-aliaschildfield_stored').val() ? ' selected' : '') + '></option>').attr('formattype', entry).attr('value', key).text(key));
					});
				}

                var d = 0;

                $.each(JSON.parse(data), function (key, entry) {
                    console.log(key);

                    if ($('.update_form').length > 0)
                        dropdown.append($('<option' + (key == $('#aliasform-aliasdatabasefield_stored').val() ? ' selected' : '') + '></option>').attr('formattype', entry).attr('value', key).text(key));
                    else
                        dropdown.append($('<option></option>').attr('formattype', entry).attr('value', key).text(key));
                    if ($('.update_form').length == 0) {
                        if (d == 0) dropdown.attr('selected', 'selected');
                    }

                    d++;
                });

                $('.field-aliasform-aliasdatabasefield .help-block').html('');
                $('.field-aliasform-aliasdatabasefield').removeClass('has-error').prop('disabled', false).show();

                formLogic();
            });
    }

    setInterval(function () {
        $.ajax({
            method: "POST",
            url: BASE_URL + "api/checkLogin",
            data: {ajax: 'true'}
        })
            .done(function (data) {
                console.log(data);
                if (data == "false")
                    window.location.href = BASE_SERVER + 'login';
            });
    }, 1800000);

});

function checkIfExist(pk) {

}


function addListItem(source, destination, field, fieldsource, type) {
    if (type != 'novalidate') {
        switch (source) {
            case 3:
                if ($('#specialaccessrestrictionform-entity').val().trim() == "") {
                    alert('Please fill up Entity field.');
                    return false;
                }
                break;
            case 4:
                if ($('#aliasrestrictionform-entity').val().trim() == "") {
                    alert('Please fill up Entity field.');
                    return false;
                }
                if ($('#aliasrestrictionform-value').val().trim() == "") {
                    alert('Please fill up Value field.');
                    return false;
                }
                break;
        }
    }
    console.log(destination);
    var lastField = $(destination + " tr:last-child");
    var intId = (lastField && lastField.length && lastField.data("idx") + 1) || 1;
    if (lastField.attr("elno") != undefined) intId = parseInt(lastField.attr("elno")) + 1;
    else intId = 1;
    var fieldWrapper = $("<tr class=\" fieldwrapper button-block\" id=\"field" + intId + "\"  elno=\"" + intId + "\" tab=\"" + source + "\" />");
    /*fieldWrapper.data("idx", source + "_" + intId);*/
    switch (type) {
        case 'preview':
            switch (source) {
                case 1:
					if($('#customAPI').val() == 0) {
						var fName = $("<td class='col-xs-11'><input type=\"text\" class=\"form-control list-item\" value=\"" + fieldsource + "\" name=\"" + field + "\" readonly /><input type=\"hidden\" class=\"form-control\" value=\"" + $("#aliasdependencyform-requesttable").val() + "\" name=\"AliasDependencyForm[RequestTable][]\" readonly /></td>");
					} else {
						var fName = $("<td class='col-xs-11'><input type=\"text\" class=\"form-control list-item\" value=\"" + fieldsource + "\" name=\"" + field + "\" readonly /></td>");
					}
                    break;
                case 2:
                    var fName = $("<td class='col-xs-3'><input type=\"text\" class=\"form-control\" value=\"" + fieldsource + "\" name=\"" + field + "\" readonly /></td><td class='col-xs-3'><input type=\"text\" class=\"form-control\" value=\"" + $("#aliassecurityspec-usertype").val() + "\" name=\"AliasSecuritySpecForm[UserType]\" readonly /></td><td class='col-xs-3'><input type=\"text\" class=\"form-control\" value=\"" + $("#aliassecurityspec-tenant").val() + "\" name=\"AliasSecuritySpecForm[Tenant]\" readonly /></td><td class='col-xs-2'><input type=\"text\" class=\"form-control\" value=\"" + $("#aliassecurityspec-securityfield").val() + "\" name=\"AliasSecuritySpecForm[SecurityField]\" readonly /></td>");
                    break;
                case 3:
                    var fName = $("<td class='col-xs-4'><input type=\"text\" class=\"form-control\" value=\"" + fieldsource + "\" name=\"" + field + "\" readonly /></td><td class='col-xs-4'><input type=\"text\" class=\"form-control\" value=\"" + $("#specialaccessrestrictionform-usergroupvalue").val() + "\" name=\"SpecialAccessRestrictionForm[UserGroupValue]\" readonly /></td><td class='col-xs-3'><input type=\"text\" class=\"form-control\" value=\"" + $("#specialaccessrestrictionform-rights").val() + "\" name=\"SpecialAccessRestrictionForm[Rights]\" readonly /></td>");
                    break;
                case 4:
                    var fName = $("<td class='col-xs-3'><input type=\"text\" class=\"form-control\" value=\"" + fieldsource + "\" name=\"" + field + "\" readonly /></td><td class='col-xs-3'><input type=\"text\" class=\"form-control\" value=\"" + $("#aliasrestrictionform-usergroup").val() + "\" name=\"AliasRestrictionForm[UserGroup]\" readonly /></td><td class='col-xs-3'><input type=\"text\" class=\"form-control\" value=\"" + $("#aliasrestrictionform-value").val() + "\" name=\"AliasRestrictionForm[Value]\" readonly /></td><td class='col-xs-2'><input type=\"text\" class=\"form-control\" value=\"" + $("#aliasrestrictionform-rights").val() + "\" name=\"AliasRestrictionForm[Rights]\" readonly /></td>");
                    break;
            }
            break;
        default: // non preview
            var fName = $("<td class='col-xs-11'><input id=\"field" + "_" + source + "_" + intId + "\" type=\"text\" class=\"form-control " + (source == 1 && field == "AliasDependencyForm[DependentsOn][]" ? "dependent-item" : "") + "\" value=\"" + fieldsource + "\" name=\"" + field + "\" /></td>");
            break;
    }
    var removeButton = $("<td class='col-xs-1'><input type=\"button\" class=\"remove btn btn-primary\" value=\"-\" /></td>");
    removeButton.click(function () {
		var con = confirm('Are you sure you want to delete?');

		if(con) {
			$(this).parent().remove();
		}

		//$(this).parent().remove();
    });
    fieldWrapper.append(fName);
    /*fieldWrapper.append(fType);*/
    fieldWrapper.append(removeButton);
    $(destination + "").append(fieldWrapper);
}

function addListGroup(source, destination, field, fieldsource, type) {
    var str = "";
    var lastField = $(destination + " tr:last-child");
    var intId = (lastField && lastField.length && lastField.data("idx") + 1) || 1;
    var fieldWrapper = $("<tr class=\" fieldwrapper button-block\" id=\"field" + intId + "\" tab=\"" + source + "\"/>");
    if (lastField.attr("elno") != undefined) intId = parseInt(lastField.attr("elno")) + 1;
    else intId = 1;
    fieldWrapper.data("idx", source + "_g_" + intId);
    if (type != 'api') {
        if (source == 1 && $('input.list-item').length > 0) {
            g = 0;
            $('input.list-item').each(function () {
                if (g > 0) str += ';';
                str += $(this).val();
                g++;
            });
        }
        else {
            str = fieldsource;
        }
    }
    else {
        str = fieldsource;
    }
    console.log(str);
    var rtc = $("#aliasdependencyform-requesttable").val();
    var fName = $("<td class='col-xs-6'><input type=\"text\" class=\"form-control\" value=\"" + str + "\" name=\"" + field + "\" readonly /></td><td><input type=\"text\" class=\"form-control\" value=\"" + rtc + "\" name=\"AliasDependencyForm[RequestTable][]\" readonly /></td>");
    var removeButton = $("<td class='col-xs-1'><input type=\"button\" class=\"remove btn btn-primary\" value=\"-\" onclick=\"syncRemoval($(this))\" /></td>");
    removeButton.click(function () {
        $(this).parent().remove();
    });
    fieldWrapper.append(fName);
    fieldWrapper.append(removeButton);
    $(destination + "").append(fieldWrapper);
    addListItem(source, ".alias-sub-details.aliasdependencyform-dependentson table>tbody", "AliasDependencyForm[DependentsOn][]", str);
    addListItem(source, ".alias-sub-details.aliasdependencyform-requesttable table>tbody", "AliasDependencyForm[RequestTable][]", $('#aliasdependencyform-requesttable').val());
    addListItem(source, ".alias-sub-details.aliasdependencyform-method table>tbody", "AliasDependencyForm[method][]", 'create');
}

function assessTab(tab, method, el) {
    if (el == undefined || el == null) {
        el = "";
    }
    let destination = "",
        field = "",
        fieldsource = "";
    tab = parseInt(tab);

    switch (tab) {
        case 1:
            if (method != "delete") {
				if($('#customAPI').val() == 0) {
					addListItem(tab, "#w0 .alias-subvalues-list table>tbody", "AliasDependencyForm[DependentsOn][]", $('#aliasdependencyform-dependentson').val(), "preview");
				} else {
					addListItem(tab, "#w" + tab + " .alias-subvalues-list table>tbody", "AliasDependencyForm[DependentsOn][]", $('#aliasdependencyform-dependentson').val(), "preview");
				}
            }
            else {
                var rtc = el.parent().parent().find("td:eq(1) input").val();
                addListItem(tab, ".alias-sub-details.aliasdependencyform-dependentson table>tbody", "AliasDependencyForm[DependentsOn][]", el.parent().parent().find("td:eq(0) input").val());
                addListItem(tab, ".alias-sub-details.aliasdependencyform-requesttable table>tbody", "AliasDependencyForm[RequestTable][]", rtc);
                addListItem(tab, ".alias-sub-details.aliasdependencyform-method table>tbody", "AliasDependencyForm[method][]", 'delete');
            }
            break;
        case 2:
            console.log(tab);
            if (method != "delete") {
                addListItem(tab, "#w" + tab + " .alias-subvalues-list table>tbody", "AliasSecuritySpecForm[AccountType][]", $('#aliassecurityspec-accounttype').val(), "preview");
                addListItem(tab, ".alias-sub-details.aliassecurityspec-accounttype table>tbody", "AliasSecuritySpecForm[AccountType][]", $('#aliassecurityspec-accounttype').val());
                addListItem(tab, ".alias-sub-details.aliassecurityspec-usertype table>tbody", "AliasSecuritySpecForm[UserType][]", $('#aliassecurityspec-usertype').val());
                addListItem(tab, ".alias-sub-details.aliassecurityspec-tenant table>tbody", "AliasSecuritySpecForm[Tenant][]", $('#aliassecurityspec-tenant').val());
                addListItem(tab, ".alias-sub-details.aliassecurityspec-securityfield table>tbody", "AliasSecuritySpecForm[SecurityField][]", $('#aliassecurityspec-securityfield').val());
            }
            else {
                addListItem(tab, ".alias-sub-details.aliassecurityspec-accounttype table>tbody", "AliasSecuritySpecForm[AccountType][]", el.parent().parent().find("td:eq(0) input").val());
                addListItem(tab, ".alias-sub-details.aliassecurityspec-usertype table>tbody", "AliasSecuritySpecForm[UserType][]", el.parent().parent().find("td:eq(1) input").val());
                addListItem(tab, ".alias-sub-details.aliassecurityspec-tenant table>tbody", "AliasSecuritySpecForm[Tenant][]", el.parent().parent().find("td:eq(2) input").val());
                addListItem(tab, ".alias-sub-details.aliassecurityspec-securityfield table>tbody", "AliasSecuritySpecForm[SecurityField][]", el.parent().parent().find("td:eq(3) input[type=text]").val());

            }
            addListItem(tab, ".alias-sub-details.aliassecurityspec-method table>tbody", "AliasSecuritySpecForm[method][]", method);
            break;
        case 3:
            if (method != "delete") {
                addListItem(tab, "#w" + tab + " .alias-subvalues-list table>tbody", "SpecialAccessRestrictionForm[Entity][]", $('#specialaccessrestrictionform-entity').val(), "preview");
                addListItem(tab, ".alias-sub-details.specialaccessrestrictionform-entity table>tbody", "SpecialAccessRestrictionForm[Entity][]", $('#specialaccessrestrictionform-entity').val(), (method == 'delete' ? "novalidate" : ""));
                addListItem(tab, ".alias-sub-details.specialaccessrestrictionform-usergroupvalue table>tbody", "SpecialAccessRestrictionForm[UserGroupValue][]", $('#specialaccessrestrictionform-usergroupvalue').val(), (method == 'delete' ? "novalidate" : ""));
                addListItem(tab, ".alias-sub-details.specialaccessrestrictionform-rights table>tbody", "SpecialAccessRestrictionForm[Rights][]", $('#specialaccessrestrictionform-rights').val(), (method == 'delete' ? "novalidate" : ""));
                addListItem(tab, ".alias-sub-details.specialaccessrestrictionform-method table>tbody", "SpecialAccessRestrictionForm[method][]", method, (method == 'delete' ? "novalidate" : ""));
            }
            else {
                addListItem(tab, ".alias-sub-details.specialaccessrestrictionform-entity table>tbody", "SpecialAccessRestrictionForm[Entity][]", el.parent().parent().find("td:eq(0) input").val(), (method == 'delete' ? "novalidate" : ""));
                addListItem(tab, ".alias-sub-details.specialaccessrestrictionform-usergroupvalue table>tbody", "SpecialAccessRestrictionForm[UserGroupValue][]", el.parent().parent().find("td:eq(1) input").val(), (method == 'delete' ? "novalidate" : ""));
                addListItem(tab, ".alias-sub-details.specialaccessrestrictionform-rights table>tbody", "SpecialAccessRestrictionForm[Rights][]", el.parent().parent().find("td:eq(2) input[type=text]").val(), (method == 'delete' ? "novalidate" : ""));
                addListItem(tab, ".alias-sub-details.specialaccessrestrictionform-id table>tbody", "SpecialAccessRestrictionForm[Id][]", el.parent().parent().find("td:eq(2) input[type=hidden]").val(), (method == 'delete' ? "novalidate" : ""));
            }
            addListItem(tab, ".alias-sub-details.specialaccessrestrictionform-method table>tbody", "SpecialAccessRestrictionForm[method][]", method, (method == 'delete' ? "novalidate" : ""));
            break;
        case 4:
            if (method != "delete") {
                addListItem(tab, "#w" + tab + " .alias-subvalues-list table>tbody", "AliasRestrictionForm[Entity][]", $('#aliasrestrictionform-entity').val(), "preview");
                addListItem(tab, ".alias-sub-details.aliasrestrictionform-entity table>tbody", "AliasRestrictionForm[Entity][]", $('#aliasrestrictionform-entity').val(), (method == 'delete' ? "novalidate" : ""));
                addListItem(tab, ".alias-sub-details.aliasrestrictionform-usergroup table>tbody", "AliasRestrictionForm[UserGroup][]", $('#aliasrestrictionform-usergroup').val(), (method == 'delete' ? "novalidate" : ""));
                addListItem(tab, ".alias-sub-details.aliasrestrictionform-value table>tbody", "AliasRestrictionForm[Value][]", $('#aliasrestrictionform-value').val(), (method == 'delete' ? "novalidate" : ""));
                addListItem(tab, ".alias-sub-details.aliasrestrictionform-rights table>tbody", "AliasRestrictionForm[Rights][]", $('#aliasrestrictionform-rights').val(), (method == 'delete' ? "novalidate" : ""));
            }
            else {
                addListItem(tab, ".alias-sub-details.aliasrestrictionform-entity table>tbody", "AliasRestrictionForm[Entity][]", el.parent().parent().find("td:eq(0) input").val(), (method == 'delete' ? "novalidate" : ""));
                addListItem(tab, ".alias-sub-details.aliasrestrictionform-usergroup table>tbody", "AliasRestrictionForm[UserGroup][]", el.parent().parent().find("td:eq(1) input").val(), (method == 'delete' ? "novalidate" : ""));
                addListItem(tab, ".alias-sub-details.aliasrestrictionform-value table>tbody", "AliasRestrictionForm[Value][]", el.parent().parent().find("td:eq(2) input").val(), (method == 'delete' ? "novalidate" : ""));
                addListItem(tab, ".alias-sub-details.aliasrestrictionform-rights table>tbody", "AliasRestrictionForm[Rights][]", el.parent().parent().find("td:eq(3) input[type=text]").val(), (method == 'delete' ? "novalidate" : ""));
                addListItem(tab, ".alias-sub-details.aliasrestrictionform-id table>tbody", "AliasRestrictionForm[Id][]", el.parent().parent().find("td:eq(3) input[type=hidden]").val(), (method == 'delete' ? "novalidate" : ""));
            }
            addListItem(tab, ".alias-sub-details.aliasrestrictionform-method table>tbody", "AliasRestrictionForm[method][]", method, (method == 'delete' ? "novalidate" : ""));
            break;
    }
}

function syncRemoval(el) {
    if (confirm("Are you sure you want to delete this item?")) {
        if ($(".update_form").length > 0) {
            assessTab(el.parent().parent().attr('tab'), "delete", el);
        }
        else {
            $("[tab=" + el.parent().parent().attr('tab') + "]" + "[elno=" + el.parent().parent().attr('elno') + "]").remove();
        }
        el.parent().parent().remove();
    }
}

function goBack(tab_id) {
	if(tab_id !== '') {
		$('.nav-tabs > li').removeClass('active');
		$('.tab-pane').removeClass('active');

		$('a[href="#'+tab_id+'"]').parent("li").addClass('active');
		$('#'+tab_id).addClass('tab-pane active');
	}
}
