$(function() {
	var jobs_params_json = $('#form-jobs-params').val();

	if(jobs_params_json != undefined && jobs_params_json != '') {
		//alert(jobs_params_json);

		var jobs_params_json_obj = JSON.parse(jobs_params_json);

		//alert(jobs_params_json_obj.function_extensions_job_params.alias_framework_info.request_primary_table);

		var tableCol = '';

		if(jobs_params_json_obj.function_extensions_job_params.search_function_info.config) {
			$.each(jobs_params_json_obj.function_extensions_job_params.search_function_info.config.func_inparam_configuration, function(index, value) {
				tableCol += value;

				if(index > 0)
					tableCol += '/';
			});
		} else if(jobs_params_json_obj.function_extensions_job_params.search_custom_query) {
			$.each(jobs_params_json_obj.function_extensions_job_params.search_custom_query.query_params, function(index, value) {
				tableCol += value.name;

				if(index > 0)
					tableCol += '/';
			});
		}

		//alert(tableCol);

		$('.search_field_label').html('Search for ' + tableCol + ' in ' + jobs_params_json_obj.function_extensions_job_params.alias_framework_info.request_primary_table + ' table');

		if(jobs_params_json_obj.function_extensions_job_params.search_function_info.config) {
			var func_param = jobs_params_json_obj.function_extensions_job_params.search_function_info.config;
			var searchType = 'simple';
		} else {
			var func_param = jobs_params_json_obj.function_extensions_job_params.search_custom_query;
			var searchType = 'multi';
		}
	}

	var states = [];

	/*$.post('/codiac_builder/web/admin/job-scheduler/get-table-data', {
		'tableName': jobs_params_json_obj.function_extensions_job_params.alias_framework_info.request_primary_table,
		'tableCol': tableCol,
		'libName': $jobs_params_json_obj.function_extensions_job_params.lib_name,
		'search': 'B'
	}).done(function (data) {
		alert(data);

		states = ['blah', 'blah2', 'portfolio blend 1', 'blah7', 'BLAH7', '1'];
	});*/

	//states = ['blah', 'blah2', 'portfolio blend 1', 'blah7', 'BLAH7', '1'];

	$('#states_search').typeahead({
		//source: states,
		source: function(query, process) {
			//alert(query);
			//alert(searchType);
			return $.ajax({
				url: '/codiac_builder/web/admin/job-scheduler/get-table-data',
				type: 'post',
				data: {query: query, libName: jobs_params_json_obj.function_extensions_job_params.lib_name, searchType: searchType, tableName: jobs_params_json_obj.function_extensions_job_params.alias_framework_info.request_primary_table, tableCol: tableCol, func_param: func_param},
				dataType: 'json',
				success: function(res) {
					

					return process(res);
				}
			});
		},
		updater:function(item) {
			//alert(item);

			$.post('/codiac_builder/web/admin/job-scheduler/search-data', {
				'value': item,
				'jobs_params': $('#form-jobs-params').val(),
				'template_layout': $('#form-template-layout').val()
			}).done(function (data) {
				alert(data);

				var data1 = JSON.parse(data);

				//alert(data1.search_result);

				//alert(data1.html);

				var jobs_params_json = $('#form-jobs-params').val();

				//alert(jobs_params_json);

				var jobs_params_json_obj = JSON.parse(jobs_params_json);

				var func_param_json_obj = data1.search_result;

				jobs_params_json_obj.function_extensions_job_params.search_function_info.data = func_param_json_obj;

				json_string = JSON.stringify(jobs_params_json_obj);

				//alert(json_string);

				$('#form-jobs-params').val(json_string);

				var template_layout_json = $('#form-template-layout').val();

				//alert(template_layout_json);

				var template_layout_json_obj = JSON.parse(template_layout_json);

				template_layout_json_obj[0].alias_framework_function = 'view';

				template_layout_json_string = JSON.stringify(template_layout_json_obj);

				//alert(template_layout_json_string);

				console.log(template_layout_json_string);

				if(data1.html != '') {
					$('.special-sub-btns-insert').show();
					$('.screen-remove-btn').show();
					$('.screen-edit-btn').show();
					$('.special-sub-btns').hide();
				}

				$('#form-template-layout').val(template_layout_json_string);

				$('#alias_framework_div').html(data1.html);
			});

			//don't forget to return the item to reflect them into input
			return item;
		}
	});

	$(document).on('click', '.screen-insert-btn', function () {
		$.ajax({
            type: 'POST',
            cache: false,
            url: '/codiac_builder/web/admin/job-scheduler/show-output-fields',
            data: {'template_layout': $('#form-template-layout').val()},
            success: function (data) {
				//alert(data);

				var data1 = JSON.parse(data);

				$('.special-sub-btns-insert').hide();
				$('.screen-remove-btn').hide();
				$('.screen-edit-btn').hide();
				$('.special-sub-btns').show();

				var template_layout_json = $('#form-template-layout').val();

				//alert(template_layout_json);

				var template_layout_json_obj = JSON.parse(template_layout_json);

				template_layout_json_obj[0].alias_framework_function = 'create';

				template_layout_json_string = JSON.stringify(template_layout_json_obj);

				//alert(template_layout_json_string);

				console.log(template_layout_json_string);

				$('#states_search').val('');

				$('#form-template-layout').val(template_layout_json_string);

				$('#alias_framework_div').html(data1.html);
            },
            error: function (data) {
				
            }
        });
	});

	$(document).on('click', '.screen-edit-btn', function () {
		$('.special-sub-btns-insert').hide();
		$('.screen-remove-btn').hide();
		$('.screen-edit-btn').hide();
		$('.special-sub-btns').show();

		var template_layout_json = $('#form-template-layout').val();

		//alert(template_layout_json);

		var template_layout_json_obj = JSON.parse(template_layout_json);

		template_layout_json_obj[0].alias_framework_function = 'update';

		template_layout_json_string = JSON.stringify(template_layout_json_obj);

		//alert(template_layout_json_string);

		console.log(template_layout_json_string);

		$('#form-template-layout').val(template_layout_json_string);

		$('.common-alias-function-inputs').removeAttr('readonly');
	});

	$(document).on('click', '.screen-cancel-btn', function () {
		$('.special-sub-btns-insert').show();
		$('.screen-remove-btn').hide();
		$('.screen-edit-btn').hide();
		$('.special-sub-btns').hide();

		var template_layout_json = $('#form-template-layout').val();

		//alert(template_layout_json);

		var template_layout_json_obj = JSON.parse(template_layout_json);

		template_layout_json_obj[0].alias_framework_function = 'view';

		template_layout_json_string = JSON.stringify(template_layout_json_obj);

		//alert(template_layout_json_string);

		console.log(template_layout_json_string);

		$('#form-template-layout').val(template_layout_json_string);

		$('#states_search').val('');
		$('#alias_framework_div').html('');
	});

	$(document).on('change', '#joblaunchform-launch_type', function () {
		if($(this).val() == 'JobLaunchType.At') {
			$('#launch_type_at_div').show();
			$('#launch_type_common_div').hide();
			$('#launch_type_cron_div').hide();
			//$('#cron_type_select_div').hide();
		} else if($(this).val() == 'JobLaunchType.Cron') {
			$('#launch_type_cron_div').show();
			//$('#cron_type_select_div').hide();
			$('#launch_type_common_div').hide();
			$('#launch_type_at_div').hide();
		} else {
			$('#launch_type_common_div').show();
			$('#launch_type_at_div').hide();
			$('#launch_type_cron_div').hide();
			//$('#cron_type_select_div').hide();
		}
	});

	$(document).on('change', '#cron_types', function () {
		$('#cron_type_select_div').show();

		if($(this).val() == 'every_day') {
			$('#cron_type_every_day').show();
			$('#cron_type_every_week_day').hide();
			$('#cron_type_every_weekend').hide();
			$('#cron_type_every_month').hide();
			$('#cron_type_custom').hide();
		} else if($(this).val() == 'every_week_day') {
			$('#cron_type_every_day').hide();
			$('#cron_type_every_week_day').show();
			$('#cron_type_every_weekend').hide();
			$('#cron_type_every_month').hide();
			$('#cron_type_custom').hide();
		} else if($(this).val() == 'every_weekend') {
			$('#cron_type_every_day').hide();
			$('#cron_type_every_week_day').hide();
			$('#cron_type_every_weekend').show();
			$('#cron_type_every_month').hide();
			$('#cron_type_custom').hide();
		} else if($(this).val() == 'every_month') {
			$('#cron_type_every_day').hide();
			$('#cron_type_every_week_day').hide();
			$('#cron_type_every_weekend').hide();
			$('#cron_type_every_month').show();
			$('#cron_type_custom').hide();
		} else if($(this).val() == 'custom') {
			$('#cron_type_every_day').hide();
			$('#cron_type_every_week_day').hide();
			$('#cron_type_every_weekend').hide();
			$('#cron_type_every_month').hide();
			$('#cron_type_custom').show();
		}
	});

	$('input[name="launch_type_condition_radio"]').on('change', function() {
		var radioValue = $('input[name="launch_type_condition_radio"]:checked').val();

		if(radioValue == 1) {
			$('#datepickerdiv').show();
			$('#timepickerdiv').hide();
			$('#datetimepickerdiv').hide();
		} else if(radioValue == 2) {
			$('#timepickerdiv').show();
			$('#datepickerdiv').hide();
			$('#datetimepickerdiv').hide();
		} else if(radioValue == 3) {
			$('#datetimepickerdiv').show();
			$('#timepickerdiv').hide();
			$('#datepickerdiv').hide();
		}
	});

	if($('#joblaunchform-launch_params').val() != '' && $('#joblaunchform-launch_params').val() != undefined) {
		var tree_result = [];

		var tree_pathArray = atob($('#joblaunchform-launch_params').val());
		tree_pathArray = JSON.parse(tree_pathArray);

		$.each(tree_pathArray, function(index, value) {
			if(value != '') {
				var childs = traverseJson(value, false, tree_pathArray.func_extensions_job);
				tree_result.push({"state":{"opened": true}, "text": index, "children": childs});
			}
		});

		$("#jstree1").jstree({
			"core" : {
				"animation" : 250,
				"data" : tree_result,
				"themes":{
					"icons": false
				}
			}
		});
	}

	$(document).on('click', '.job-launch-params-load', function () {
		var url = $(this).attr('data-url');

		$('#job-launch-params-modal .modal-body').append('<div class="loader"></div>');

		var job_launch_params = $('#joblaunchform-launch_params').val();

		$.ajax({
            type: 'POST',
            cache: false,
            url: url,
            data: {'action': 'test'},
            success: function (data) {
				var result = [];

				if(job_launch_params != '') {
					var pathArray = atob(job_launch_params);

					pathArray = JSON.parse(pathArray);
				} else {
					pathArray = {"func_extensions_job":[]};
				}

				$.each(data, function(index, value) {
					if(value != '') {
						if(job_launch_params !== '') {
							var childs = traverseJson(value, false, pathArray.func_extensions_job);
							result.push({"state":{"opened": true}, "text": index, "children": childs});
						} else {
							var childs = traverseJson(value, false, pathArray.func_extensions_job);
							result.push({"text": index, "children": childs});
						}
					}
				});

				$("#jstree").jstree("destroy");

				setTimeout(function() {
					$("#jstree").jstree({
						'plugins': ['checkbox'],
						"checkbox": {
							"three_state" : true,
							"keep_selected_style" : false
						},
						"core" : {
							"multiple" : "on",
							"animation" : 250,
							"data" : result,
							"themes":{
								"icons":false
							}
						}
					});

					$('#job-launch-params-modal .modal-body').find('.loader').remove();
				}, 2000);
            },
            error: function (data) {
				$('#job-launch-params-modal .modal-body').find('.loader').remove();
            }
        });
	});

	$(document).on('click', '.btn-cron-every-day-done', function () {
		var every_day = $('.cron_every_day_timepicker').val();
		var every_day_split = every_day.split(":");

		var job_launch_condition_string = every_day_split[1]+' '+every_day_split[0]+' '+'*'+' '+'*'+' '+'*';

		$('.joblaunchform-launch_condition').val(job_launch_condition_string);
	});

	$(document).on('click', '.btn-cron-every-week-day-done', function () {
		var every_week_day = $('#day_of_the_week').val();
		var every_week_day_time = $('.cron_every_week_day_timepicker').val();
		var every_week_day_time_split = every_week_day_time.split(":");

		var job_launch_condition_string = every_week_day_time_split[1]+' '+every_week_day_time_split[0]+' '+'*'+' '+'*'+' '+every_week_day;

		$('.joblaunchform-launch_condition').val(job_launch_condition_string);
	});

	$(document).on('click', '.btn-cron-every-weekend-done', function () {
		var cron_every_weekend_timepicker = $('.cron_every_weekend_timepicker').val();
		var cron_every_weekend_timepicker_split = cron_every_weekend_timepicker.split(":");

		var job_launch_condition_string = cron_every_weekend_timepicker_split[1]+' '+cron_every_weekend_timepicker_split[0]+' '+'*'+' '+'*'+' '+'5';

		$('.joblaunchform-launch_condition').val(job_launch_condition_string);
	});

	$(document).on('click', '.btn-cron-every-month-done', function () {
		var day_of_the_month = $('#day_of_the_month').val();
		var cron_every_month_timepicker = $('.cron_every_month_timepicker').val();
		var cron_every_month_timepicker_split = cron_every_month_timepicker.split(":");

		var job_launch_condition_string = cron_every_month_timepicker_split[1]+' '+cron_every_month_timepicker_split[0]+' '+day_of_the_month+' '+'*'+' '+'*';

		$('.joblaunchform-launch_condition').val(job_launch_condition_string);
	});

	$(document).on('click', '.btn-cron-custom-done', function () {
		var minute = $('#minute').val();
		var hour = $('#hour').val();
		var day_of_month = $('#day_of_month').val();
		var month = $('#month').val();
		var day_of_week = $('#day_of_week').val();

		var job_launch_condition_string = minute+' '+hour+' '+day_of_month+' '+month+' '+day_of_week;

		$('.joblaunchform-launch_condition').val(job_launch_condition_string);
	});

	$(document).on('click', '.btn-launch-params-done', function () {
		var jsTreeLib = $("#jstree").jstree(true); // Js Tree object
		var jsonOptions = $("#jstree").jstree(true).get_json('#', {flat:false}); // Getting 'dirty' json tree by JsTree API

		var launch_params = getFilteredJsonTree(jsTreeLib, jsonOptions); //Main function for creating json tree for OSOC API
		launch_params = launch_params.reduce((a, b) => Object.assign(a, b), {});

		$("#jstree").jstree("destroy");

		$('#joblaunchform-launch_params').val(btoa(JSON.stringify(launch_params)));
	});

	$(document).on('keyup', '.joblaunchform-launch_condition', function () {
		$('.joblaunchform-launch_condition').val($(this).val());
	});

	$('#datepicker').datetimepicker({
		format: 'DD/MM/YYYY',
	}).on('dp.change', function (e) {
		$('.joblaunchform-launch_condition').val(e.date.format('DD/MM/YYYY'));
	});

	$('#timepicker').datetimepicker({
		format: 'hh:mm:ss',
	}).on('dp.change', function (e) {
		$('.joblaunchform-launch_condition').val(e.date.format('hh:mm:ss'));
	});

	$('#datetimepicker').datetimepicker({
		format: 'DD/MM/YYYY hh:mm:ss',
	}).on('dp.change', function (e) {
		$('.joblaunchform-launch_condition').val(e.date.format('DD/MM/YYYY hh:mm:ss'));
	});

	$('#cron_every_day_timepicker').datetimepicker({
		format: 'hh:mm',
	});

	$('#cron_every_week_day_timepicker').datetimepicker({
		format: 'hh:mm',
	});

	$('#cron_every_weekend_timepicker').datetimepicker({
		format: 'hh:mm',
	});

	$('#cron_every_month_timepicker').datetimepicker({
		format: 'hh:mm',
	});
});

function getFilteredJsonTree(jsTreeLib, jsonObj) {
	if (isEmpty(jsonObj)) return;

	var branch = [];
	$.each(jsonObj, function(i, node){
		if(jsTreeLib.is_undetermined(jsTreeLib.get_node(node.id)) || node.state.selected) {
			var leaf = createProperty(node);
			var children = getFilteredJsonTree(jsTreeLib, node.children);
			if (children) {
				leaf.push(children);
			}

			var property = leaf;
			var propertyValue = [];

			if (typeof leaf !== "string") {
				var property = leaf[0];
				var propertyValue = leaf[1];
			}

			var obj = {};

			Object.defineProperty(obj, property, {
				enumerable: true,
				configurable: true,
				writable: true,
				value: propertyValue
			});

			branch.push(obj);
		}
	});

	return branch;
}

function isEmpty(obj) {
	for(var key in obj) {
		if(obj.hasOwnProperty(key))
			return false;
	}
	return true;
}

function createProperty(obj) {
	var result = [];
	if (obj.children.length > 0) {
		result.push(obj.text);
	} else {
		result = obj.text;
	}
	return result;
}

function getFlat(array) {
	return array.reduce((r, o) => r.concat(...Object.entries(o).map(([k, v]) => [k, ...getFlat(v)])), []);
}

var pathArray_last_value = '';

function traverseJson(o, isChild, pathArray) {
	var i;
	var name;
	var res = [];
	var isEmptyObject = false;

	var kk = 0;

	for (var k in o) {
		var jsonObj = {};
		jsonObj.state = {};
		i = o[k];

		if (typeof i == 'string') {
			isChild = false;
			jsonObj.text = i;
		} else {
			var obj = Object.values(i);

			name = Object.keys(i)[0];

			if (obj.length > 0) {
				if(typeof pathArray[kk] === 'object') {
					var child_array = getFlat(pathArray);

					if(pathArray_last_value === undefined || pathArray_last_value === '') {
						pathArray_last_value = Object.keys(pathArray[kk])[0];
					}
				}

				if(typeof pathArray[kk] === 'object' && name === pathArray_last_value) {
					jsonObj.state.opened = true;

					if(child_array.length == 1) {
						jsonObj.state.selected = true;
					}

					pathArray_last_value = '';
				}

				jsonObj.text = name;

				var value = obj[0];
				var childrens = [];

				var jj = 0;

				for (var j in value) {
					if (typeof value[j] === 'object') {
						var ob = [value[j]];

						if(typeof pathArray[kk] === 'object') {
							childrens.push(this.traverseJson(ob, true, [Object.values(pathArray[kk])[0][jj]]));
						} else {
							childrens.push(this.traverseJson(ob, true, pathArray));
						}

						value = Object.values(i)[0];
					} else if (typeof value[j] !== 'object' && typeof value[j] !== 'function') {
						var settings = {};

						settings.text = value[j];

						childrens.push(settings);
					}

					if(pathArray_last_value === '')
						jj++;
				}

				jsonObj.children = childrens;
			} else {
				isEmptyObject = true;
			}
		}

		if (!isEmptyObject) {
			if (!isChild) {
				res.push(jsonObj);
			} else {
				return jsonObj;
			}
		} else {
			isEmptyObject = false;
		}

		if(pathArray_last_value === '')
			kk++;
	}

	return res;
}
