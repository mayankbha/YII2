<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 */

/**
 * @var $this yii\web\View
 * @var $model \app\modules\admin\models\forms\TableForm
 */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\web\View;

use app\models\ExtensionsList;
use yii\helpers\ArrayHelper;

$col_count = (isset($model->columns) ? (sizeof($model->columns) - 1) : 0);
$constraint_count = (isset($model->constraints) ? (sizeof($model->constraints) - 1) : 0);

//$col_count = 0;
//$constraint_count = 0;

$url = Url::to(['/admin/table/get-table-columns']);

$this->registerJs(/** @lang JavaScript */"
	var col_count = ".$col_count.";
	var constraint_count = ".$constraint_count.";

	function validateColumns(cnt) {
		//alert('validateColumns :: ' + cnt);

		var check = true;
		var name = false;
		var check_name_cnt = 0;

		var add_column_name_id = $('#add_column_name_id_'+cnt);
		var add_column_type_id = $('#add_column_type_id_'+cnt);
		var add_column_nullable_id = $('#add_column_nullable_id_'+cnt);
		var add_column_scale_id = $('#add_column_scale_id_'+cnt);
		var add_column_length_id = $('#add_column_length_id_'+cnt);

		$('.columns').each(function () {
			if($(this).val() != undefined) {
				if(add_column_name_id.val() == $(this).val()) {
					check_name_cnt++;
				}
			}
		});

		if(add_column_name_id.val() == '') {
			check = false;

			add_column_name_id.parent().addClass('has-error');
			var error_msg_div = $('<div />', {class: 'help-block'}).html('Please fill out this field.');
			add_column_name_id.after(error_msg_div);
		} else if(add_column_name_id.val() != '' && check_name_cnt > 1) {
			check = false;

			add_column_name_id.parent().removeClass('has-error');
			add_column_name_id.next('div').remove();

			add_column_name_id.val('');

			add_column_name_id.parent().addClass('has-error');
			var error_msg_div = $('<div />', {class: 'help-block'}).html('Name should be unique.');
			add_column_name_id.after(error_msg_div);
		} else if(add_column_type_id.val() == '') {
			check = false;

			add_column_name_id.parent().removeClass('has-error');
			add_column_name_id.next('div').remove();

			add_column_type_id.parent().addClass('has-error');
			var error_msg_div = $('<div />', {class: 'help-block'}).html('Please fill out this field.');
			add_column_type_id.after(error_msg_div);
		} else if(add_column_type_id.val() == 'VARCHAR' && add_column_length_id.val() == '') {
			check = false;

			add_column_type_id.parent().removeClass('has-error');
			add_column_type_id.next('div').remove();

			add_column_length_id.parent().removeClass('has-error');
			add_column_length_id.next('div').remove();

			add_column_length_id.parent().addClass('has-error');
			var error_msg_div = $('<div />', {class: 'help-block'}).html('Please fill out this field.');
			add_column_length_id.after(error_msg_div);
		} else if(add_column_length_id.val() != '' && isNaN(add_column_length_id.val())) {
			check = false;

			add_column_length_id.val('');

			add_column_length_id.parent().removeClass('has-error');
			add_column_length_id.next('div').remove();

			add_column_length_id.parent().addClass('has-error');
			var error_msg_div = $('<div />', {class: 'help-block'}).html('Only number is allowed.');
			add_column_length_id.after(error_msg_div);
		} else if(add_column_scale_id.val() != '' && isNaN(add_column_scale_id.val())) {
			check = false;

			add_column_length_id.parent().removeClass('has-error');
			add_column_length_id.next('div').remove();

			add_column_scale_id.parent().addClass('has-error');
			var error_msg_div = $('<div />', {class: 'help-block'}).html('Only number is allowed.');
			add_column_scale_id.after(error_msg_div);
		} else {
			check = true;

			add_column_name_id.parent().removeClass('has-error');
			add_column_name_id.next('div').remove();

			add_column_type_id.parent().removeClass('has-error');
			add_column_type_id.next('div').remove();

			add_column_length_id.parent().removeClass('has-error');
			add_column_length_id.next('div').remove();

			add_column_scale_id.parent().removeClass('has-error');
			add_column_scale_id.next('div').remove();
		}

		return check;
	}
	function checkTypeFields(e) {
		var cnt = $(e).attr('data-count');

		//alert(cnt);

		var constraint_column_type_id = $('#constraint_column_type_id_'+cnt);
		var constraint_column_type_id_val = constraint_column_type_id.val();

		var common_reference_contraint_type_class = $('.common_reference_contraint_type_class');

		var constraint_column_columns_id = $('#constraint_column_columns_id_'+cnt);
		var constraint_foreign_key_column_column_id = $('#constraint_foreign_key_column_column_id_'+cnt);

		if(constraint_column_type_id_val == 'FOREIGN KEY') {
			var check = true;

			$('.columns').each(function () {
				var source_table_column_cnt = $(this).attr('data-count');

				if(source_table_column_cnt != undefined) {
					//alert('foreign key source_table_column_cnt :: ' + source_table_column_cnt);

					if(!validateColumns(source_table_column_cnt)) {
						check = false;
						constraint_column_type_id.val('');
					}
				}
			});

			//alert(check);

			if(check) {
				//alert('in check if');

				$('#constraint_foreign_key_column_ref_table_name_id_'+cnt).parent().parent().show();

				constraint_foreign_key_column_column_id.parent().parent().show();
				constraint_foreign_key_column_column_id.html('');
				constraint_column_columns_id.html('');

				$('.columns').each(function () {
					//alert('columns val' + $(this).val());

					if($(this).val() != '') {
						constraint_column_columns_id.append($('<option />', {value: $(this).val(), text: $(this).val()}));
					}
				});
			}
		} else {
			var select_PK_count = 1;
			var check_selected_PK_count = 0;

			var select_UK_count = 1;
			var check_selected_UK_count = 0;

			//var selected_option = $(e).val();

			$('.common_contraint_column_type_class').each(function () {
				if($(this).val() == 'PRIMARY KEY') {
					check_selected_PK_count++;
				} else if($(this).val() == 'UNIQUE KEY') {
					check_selected_UK_count++;
				}
			});

			if(check_selected_PK_count > select_PK_count) {
				alert('Can not apply more than one PK constraint!');
				constraint_column_type_id.val('');
			} else if(check_selected_UK_count > select_UK_count) {
				alert('Can not apply more than one UK constraint!');
				constraint_column_type_id.val('');
			} else {
				var check = true;

				$('.columns').each(function () {
					var source_table_column_cnt = $(this).attr('data-count');

					if(source_table_column_cnt != undefined) {
						//alert('primary key source_table_column_cnt :: ' + source_table_column_cnt);

						if(!validateColumns(source_table_column_cnt)) {
							check = false;
							constraint_column_type_id.val('');
						}
					}
				});

				if(check) {
					$('#constraint_foreign_key_column_ref_table_name_id_'+cnt).parent().parent().hide();

					constraint_foreign_key_column_column_id.parent().parent().hide();
					constraint_foreign_key_column_column_id.html('');
					constraint_column_columns_id.html('');

					$('.columns').each(function () {
						if($(this).val() != '') {
							constraint_column_columns_id.append($('<option />', {value: $(this).val(), text: $(this).val()}));
						}
					});
				}
			}
		}
	}
	function getTableColumns(e) {
		var check = true;
		var cnt = $(e).attr('data-count');

		var selected_source_column = $('#constraint_column_columns_id_'+cnt);

		var fields = {dataType: [], name: []};

		//alert(selected_source_column.val());

		if(selected_source_column.val() != '') {
			$('.common-data-type-class').each(function () {
				var field_cnt = $(this).attr('data-count');

				if(field_cnt != undefined) {
					var dataType = $('#add_column_type_id_'+field_cnt).val();
					var name = $('#add_column_name_id_'+field_cnt).val();

					fields.dataType.push(dataType);
					fields.name.push(name);
				}
			});

			//alert(JSON.stringify(fields));

			if(check) {
				table_name = $(e).find('option:selected').text()
				field_id = $('#constraint_foreign_key_column_column_id_'+cnt);

				if (table_name != '') {
					field_id.html($('<option />', {text: 'Loading...'}));

					$.post('$url', {
						table_name: table_name,
						fields: JSON.stringify(fields),
						selected_source_column: selected_source_column.val()
					}).done(function (data) {
						field_id.html('');

						if (data && data != '') {
							$.each(data, function (i, item) {
								field_id.append($('<option />', {value: item['column_name'], text: item['column_name']}));
							})
						} else {
							alert('No matched column found.');
						}
					});
				}
			}
		} else {
			alert('Please select source table column from Columns first.');
			$('#constraint_foreign_key_column_ref_table_name_id_'+cnt).val('');
		}
	}
    $(document)
        .on('click', '.add-category-family', function () {
			//alert('Before increment :: ' + col_count);

			col_count = (col_count + 1);

			//alert('After increment :: ' + col_count);

			var wrapper = $('.document-family-wrapper'),
				formName = wrapper.attr('data-form-name'),
				iteration = parseInt(wrapper.attr('data-iteration')) + 1,
				newWrapper = $('<div />', {class: 'row columns_row'}).html(wrapper.html());

			if(validateColumns(col_count-1)) {
				newWrapper.find('input, select, textarea').each(function () {
					var name = $(this).attr('name');
					var data_title = $(this).attr('data-title');
					var field_id = data_title+col_count;

					$(this).attr('name', formName + '[columns][' + col_count + '][' + name + ']');
					$(this).attr('data-count', col_count);
					$(this).attr('id', field_id);
				});
	 
				wrapper.before(newWrapper);
				wrapper.attr('data-iteration', iteration);

				$('#check_foreign_key_related_fields_div').val('');
			} else {
				col_count = (col_count - 1);
			}
        })
        .on('click', '.remove-family-icon', function () {
            if (confirm('A you\'re a sure want to delete this field?')) {
                $(this).parents('.row')[0].remove();

				col_count = (col_count - 1);

				//alert(col_count);
            }
        })
		.on('click', '.add-constraint', function () {
			constraint_count = (constraint_count + 1);

            var wrapper = $('.foreign-key-field-main-div'),
                formName = wrapper.attr('data-form-name'),
                iteration = parseInt(wrapper.attr('data-iteration')) + 1,
                newWrapper = $('<div />', {class: 'row columns_row'}).html(wrapper.html());
				selected_constraint_type = $('#check_foreign_key_related_fields_div').find(':selected').text();

            newWrapper.find('input, select, textarea').each(function () {
                var name = $(this).attr('name');
				var data_title = $(this).attr('data-title');
				var field_id = data_title+constraint_count;

				name = name.split('[]');

				if(name.length == 1) {
					$(this).attr('name', formName + '[constraints][' + constraint_count + '][' + name + ']');
				} else if(name.length == 2) {
					$(this).attr('name', formName + '[constraints][' + constraint_count + '][' + name[0] + '][]');
				}

				$(this).attr('data-count', constraint_count);
				$(this).attr('id', field_id);

				//$(this).removeAttr('disabled');
            });
 
            wrapper.before(newWrapper);
            wrapper.attr('data-iteration', iteration);
        })
        .on('click', '.remove-constraint-icon', function () {
            if (confirm('A you\'re a sure want to delete this field?')) {
                $(this).parents('.row')[0].remove();

				constraint_count = (constraint_count - 1);
            }
        })
		.on('click', '.remove-existing-family-icon', function () {
            if (confirm('A you\'re a sure want to delete this field?')) {
                $(this).parents('.row')[0].remove();
            }
        })
		.on('click', '.remove-existing-constraint-icon', function () {
            if (confirm('A you\'re a sure want to delete this field?')) {
                $(this).parents('.row')[0].remove();
            }
        })
		.on('submit', '#table-form', function () {
			var check = true;

			var table_name = $('.table-name-class').val();

			if(table_name == '') {
				check = false;
				
				$('.table-name-class').parent().addClass('has-error');
				var error_msg_div = $('<div />', {class: 'help-block'}).html('Please fill out this field.');
				$('.table-name-class').after(error_msg_div);
			} else {
				check = true;

				$('.table-name-class').parent().removeClass('has-error');
				$('.table-name-class').next('div').remove();
			}

			if(!validateColumns(0)) {
				check = false;
			} else {
				check = true;
			}

			$('.common_contraint_column_type_class').each(function () {
				if($(this).val() != 'PRIMARY KEY') {
					var cnt = $(this).attr('data-count');

					if(cnt != undefined) {
						var selected_source_column = $('#constraint_column_columns_id_'+cnt);
						var selected_FK_column = $('#constraint_foreign_key_column_column_id_'+cnt);

						//alert(selected_source_column.val().length);
						//alert(selected_FK_column.val().length);

						if(selected_source_column.val().length != selected_FK_column.val().length) {
							alert('FK table column count should be equal to source table column count.');
							check = false;
						}
					}
				}
			});

			if(!check) {
				return false;
			}
		});
", View::POS_HEAD);

$tables_arr = array();

foreach($tables as $table) {
	$tables_arr[$table] = $table;
}

//echo "<pre>"; print_r($model);

?>

<?php $form = ActiveForm::begin(['id' => 'table-form']) ?>
	<div class="row">
        <div class="col-sm-6">
			<div class="form-group">
				<label class="control-label">Database Name</label>

				<?= Html::textInput("TableForm[database_name]", !empty($model->database_name) ? $model->database_name : null, ["class" => "form-control"]); ?>
			</div>
        </div>

        <div class="col-sm-6">
			<div class="form-group">
				<label class="control-label">Schema Name</label>

				<?= Html::textInput("TableForm[schema_name]", !empty($model->schema_name) ? $model->schema_name : null, ["class" => "form-control"]); ?>
			</div>
        </div>
    </div>

	<div class="row">
        <div class="col-sm-12">
			<div class="form-group">
				<label class="control-label">Table Name</label>

				<?= Html::textInput('TableForm[table_name]', !empty($model->table_name) ? $model->table_name : null, ["class" => "form-control table-name-class"]); ?>
			</div>
        </div>
    </div>

	<div class="row">
        <div class="col-sm-12">
            <div class="form-group">
                <label>Fields/Columns Details</label>
            </div>
        </div>
    </div>

	<div class="panel panel-default">
		<div class="panel-heading" style="padding :0px !important;">
			<div class="col-sm-3">
				<div class="form-group">
					<label>Name</label>
				</div>
			</div>

			<div class="col-sm-2">
				<div class="form-group">
					<label>Type</label>
				</div>
			</div>

			<div class="col-sm-2">
				<div class="form-group">
					<label>Nullable</label>
				</div>
			</div>

			<div class="col-sm-2">
				<div class="form-group">
					<label>Scale</label>
				</div>
			</div>

			<div class="col-sm-2">
				<div class="form-group">
					<label>Length</label>
				</div>
			</div>

			<div class="col-sm-1">
				&nbsp;
			</div>
		</div>

		<div class="panel-body">
			<?php if($model->columns != '') { $i = sizeof($model->columns); ?>
				<?php foreach($model->columns as $key => $col): ?>
					<div class="row columns_row">
						<div class="col-sm-3">
							<?= Html::textInput("TableForm[columns][$key][name]", $col['name'], ['class' => 'form-control columns', 'data-count' => "$key", 'id' => "add_column_name_id_$key"]); ?>
						</div>

						<div class="col-sm-2">
							<?= Html::dropDownList("TableForm[columns][$key][type]", strtolower($col['type']), $dataTypes, ['prompt' => '-- Select --', 'class' => 'form-control common-data-type-class', 'data-count' => "$key", 'id' => "add_column_type_id_$key"]); ?>
						</div>

						<div class="col-sm-2">
							<?php $nullable_types = array('true' => 'True', 'false' => 'False'); $val = ($col['nullable'] == 0) ? 'false' : 'true'; ?>

							<?= Html::dropDownList("TableForm[columns][$key][nullable]", $val, $nullable_types, ['class' => 'form-control', 'data-count' => "$key", 'id' => "add_column_nullable_id_$key"]); ?>
						</div>

						<div class="col-sm-2">
							<?= Html::textInput("TableForm[columns][$key][scale]", $col['scale'], ['class' => 'form-control', 'data-count' => "$key", 'id' => "add_column_scale_id_$key"]); ?>
						</div>

						<div class="col-sm-2">
							<?= Html::textInput("TableForm[columns][$key][length]", $col['length'], ['class' => 'form-control common-length-class', 'data-count' => "$key", 'id' => "add_column_length_id_$key"]); ?>
						</div>

						<div class="col-sm-1">
							<?php if ($key > 0) : ?><span class="glyphicon glyphicon-remove remove-existing-family-icon"></span><?php endif; ?>
						</div>
					</div>

					<br>
				<?php endforeach; ?>
			<?php } else { $i = 0; ?>
				<div class="row columns_row">
					<div class="col-sm-3">
						<div class="form-group">
							<?= Html::textInput("TableForm[columns][$i][name]", null, ['class' => 'form-control columns', 'data-count' => "$i", 'data-title' => 'add_column_name_id_', 'id' => "add_column_name_id_$i"]) ?>
						</div>
					</div>

					<div class="col-sm-2">
						<div class="form-group">
							<?= Html::dropDownList("TableForm[columns][$i][type]", '', $dataTypes, ['prompt' => '-- Select --', 'class' => 'form-control common-data-type-class', 'data-count' => "$i", 'data-title' => 'add_column_type_id_', 'id' => "add_column_type_id_$i"]) ?>
						</div>
					</div>

					<div class="col-sm-2">
						<div class="form-group">
							<?php $nullable_types = array('true' => 'True', 'false' => 'False'); ?>
							<?= Html::dropDownList("TableForm[columns][$i][nullable]", 'true', $nullable_types, ['class' => 'form-control', 'data-count' => "$i", 'data-title' => 'add_column_nullable_id_', 'id' => "add_column_nullable_id_$i"]) ?>
						</div>
					</div>

					<div class="col-sm-2">
						<div class="form-group">
							<?= Html::textInput("TableForm[columns][$i][scale]", null, ['class' => 'form-control', 'data-count' => "$i", 'data-title' => 'add_column_scale_id_', 'id' => "add_column_scale_id_$i"]) ?>
						</div>
					</div>

					<div class="col-sm-2">
						<div class="form-group">
							<?= Html::textInput("TableForm[columns][$i][length]", null, ['class' => 'form-control common-length-class', 'data-count' => "$i", 'data-title' => 'add_column_length_id_', 'id' => "add_column_length_id_$i"]) ?>
						</div>
					</div>
				</div>
			<?php } ?>

			<div class="row document-family-wrapper" style="display: none;" data-form-name="TableForm" data-iteration="<?= $i ?>">
				<div class="col-sm-3">
					<div class="form-group">
						<?= Html::textInput("name", null, ['class' => 'form-control columns', 'data-title' => 'add_column_name_id_']) ?>
					</div>
				</div>

				<div class="col-sm-2">
					<div class="form-group">
						<?= Html::dropDownList("type", '', $dataTypes, ['prompt' => '-- Select --', 'class' => 'form-control common-data-type-class', 'data-title' => 'add_column_type_id_']) ?>
					</div>
				</div>

				<div class="col-sm-2">
					<div class="form-group">
						<?php $nullable_types = array('true' => 'True', 'false' => 'False'); ?>
						<?= Html::dropDownList("nullable", 'true', $nullable_types, ['class' => 'form-control', 'data-title' => 'add_column_nullable_id_']) ?>
					</div>
				</div>

				<div class="col-sm-2">
					<div class="form-group">
						<?= Html::textInput("scale", null, ['class' => 'form-control', 'data-title' => 'add_column_scale_id_']) ?>
					</div>
				</div>

				<div class="col-sm-2">
					<div class="form-group">
						<?= Html::textInput("length", null, ['class' => 'form-control common-length-class', 'data-title' => 'add_column_length_id_']) ?>
					</div>
				</div>

				<div class="col-sm-1">
					<span class="glyphicon glyphicon-remove remove-family-icon"></span>
				</div>
			</div>
		</div>
	</div>

	<div class="row">
        <div class="col-sm-12">
            <div class="form-group">
                <?= Html::button(Yii::t('app', 'Add More'), ['class' => 'btn btn-default add-category-family']); ?>
            </div>
        </div>
    </div>

	<?php if($model->constraints != '') { $j = sizeof($model->constraints); ?>
		<?php foreach($model->constraints as $key => $col) { ?>
			<div class="row">
				<div class="col-sm-2">
					<div class="form-group">
						<label class="control-label">CONSTRAINT TYPE</label>

						<?php $constraint_types = array('' => '-- Select --', 'PRIMARY KEY' => 'Primary Key', 'UNIQUE KEY' => 'Unique Key', 'FOREIGN KEY' => 'Foreign Key'); ?>
						<?= Html::dropDownList("TableForm[constraints][$key][type]", $col['type'], $constraint_types, ['class' => 'form-control common_contraint_column_type_class', 'data-count' => "$key", 'data-title' => 'constraint_column_type_id_', 'id' => "constraint_column_type_id_$key", 'onchange' => 'checkTypeFields(this)']); ?>
					</div>
				</div>

				<div class="col-sm-3">
					<div class="form-group">
						<label class="control-label">CONSTRAINT NAME</label>

						<?= Html::textInput("TableForm[constraints][$key][name]", $col['name'], ['data-count' => "$key", 'class' => 'form-control', 'data-title' => 'constraint_column_name_id_', 'id' => "constraint_column_name_id_$key"]); ?>
					</div>
				</div>

				<div class="col-sm-2">
					<div class="form-group">
						<label class="control-label">COLUMNS</label>

						<?php if($model->columns != '') { $i = sizeof($model->columns); $col_name_arr = array(); ?>
							<?php foreach($model->columns as $key1 => $col1) { ?>
								<?php $col_name_arr[$col1['name']] = $col1['name']; ?>
							<?php } ?>
						<?php } ?>

						<?= Html::dropDownList("TableForm[constraints][$key][columns]", !empty($col['columns']) ? $col['columns'] : array(), $col_name_arr, ['prompt' => '-- Select --', 'class' => 'form-control', 'data-count' => "$key", 'data-title' => 'constraint_column_columns_id_', 'id' => "constraint_column_columns_id_$key", 'multiple' => 'multiple']); ?>
					</div>
				</div>

				<?php if($col['type'] == 'FOREIGN KEY') { $display = ''; } else { $display = 'display: none'; } ?>

				<div class="col-sm-2" style="<?php echo $display; ?>">
					<div class="form-group">
						<label class="control-label">Ref. Table Name</label>

						<?= Html::dropDownList("TableForm[constraints][$key][ref_table_name]", $col['ref_table_name'], $tables_arr, ['prompt' => '-- Select --', 'class' => 'form-control', 'data-count' => "$key", 'data-title' => 'constraint_foreign_key_column_ref_table_name_id_', 'id' => "constraint_foreign_key_column_ref_table_name_id_$key", 'onchange' => 'getTableColumns(this)']); ?>
					</div>
				</div>

				<div class="col-sm-2" style="<?php echo $display; ?>">
					<div class="form-group">
						<label class="control-label">Reference Columns</label>

						<?php 
							if($col['type'] == 'FOREIGN KEY') {
								$ref_table_name = $col['ref_table_name'];
								$col_list = ExtensionsList::getTableColumns($ref_table_name);

								$ref_tmp_col_list = array();

								foreach($col_list as $key2 => $col2) {
									$ref_tmp_col_list[$col2['column_name']] = $col2['column_name'];
								}

								//$col_list = arrayhelper::map($col_list, function ($data) {return "{$data['column_name']}";}, 'column_name');
								//$col_list = array_map('strtolower', $col_list);
						
								//$ref_tmp_col_list = $col_list;
							} else {
								$ref_tmp_col_list = array();
							}
						?>

						<?= Html::dropDownList("TableForm[constraints][$key][ref_columns]", !empty($col['ref_columns']) ? $col['ref_columns'] : array(), $ref_tmp_col_list, ['class' => 'form-control', 'data-count' => "$key", 'data-title' => 'constraint_foreign_key_column_column_id_', 'id' => "constraint_foreign_key_column_column_id_$key", 'multiple' => 'multiple']) ?>
					</div>
				</div>

				<div class="col-sm-1">
					<div class="form-group">
						<?php if ($key >= 0) { ?><br><span class="glyphicon glyphicon-remove remove-existing-constraint-icon"></span><?php } ?>
					</div>
				</div>
			</div>
		<?php } ?>
	<?php } else { $j = 0; ?>
		<div class="row">
			<div class="col-sm-2">
				<div class="form-group">
					<label class="control-label">CONSTRAINT TYPE</label>

					<?php $constraint_types = array('' => '-- Select --', 'PRIMARY KEY' => 'Primary Key', 'UNIQUE KEY' => 'Unique Key', 'FOREIGN KEY' => 'Foreign Key'); ?>

					<?= Html::dropDownList("TableForm[constraints][$j][type]", '', $constraint_types, ['class' => ' form-control common_contraint_column_type_class', 'data-count' => "$j", 'data-title' => 'constraint_column_type_id_', 'id' => "constraint_column_type_id_$j", 'onchange' => 'checkTypeFields(this)']); ?>
				</div>
			</div>

			<div class="col-sm-3">
				<div class="form-group">
					<label class="control-label">CONSTRAINT NAME</label>

					<?= Html::textInput("TableForm[constraints][$j][name]", null, ['class' => ' form-control', 'data-count' => "$j", 'data-title' => 'constraint_column_name_id_', 'id' => "constraint_column_name_id_$j"]); ?>
				</div>
			</div>

			<div class="col-sm-2">
				<div class="form-group">
					<label class="control-label">COLUMNS</label>

					<?= Html::dropDownList("TableForm[constraints][$j][columns]", '', array(), ['prompt' => '-- Select --', 'class' => 'form-control', 'data-count' => "$j", 'data-title' => 'constraint_column_columns_id_', 'id' => "constraint_column_columns_id_$j", 'multiple' => 'multiple']); ?>
				</div>
			</div>

			<div class="col-sm-2" style="display: none;">
				<div class="form-group">
					<label class="control-label">Ref. Table Name</label>

					<?= Html::dropDownList("TableForm[constraints][$j][ref_table_name]", '', $tables_arr, ['prompt' => '-- Select --', 'class' => 'form-control', 'data-count' => "$j", 'data-title' => 'constraint_foreign_key_column_ref_table_name_id_', 'id' => "constraint_foreign_key_column_ref_table_name_id_$j", 'onchange' => 'getTableColumns(this)']); ?>
				</div>
			</div>

			<div class="col-sm-2" style="display: none;">
				<div class="form-group">
					<label>Reference Columns</label>

					<?= Html::activeDropDownList($model, 'ref_columns', array(), ['prompt' => '-- Select --', 'class' => 'form-control', 'name' => "{TableForm}[constraints][$j][ref_columns]", 'data-count' => "$j", 'data-title' => 'constraint_foreign_key_column_column_id_', 'id' => "constraint_foreign_key_column_column_id_$j", 'multiple' => 'multiple']) ?>
				</div>
			</div>
		</div>
	<?php } ?>
	
	<div class="row foreign-key-field-main-div" style="display: none;" data-form-name="TableForm" data-iteration="<?= $j ?>">
		<div class="col-sm-2">
            <div class="form-group">
				<label class="control-label">CONSTRAINT TYPE</label>

				<?php $constraint_types = array('' => '-- Select --', 'PRIMARY KEY' => 'Primary Key', 'UNIQUE KEY' => 'Unique Key', 'FOREIGN KEY' => 'Foreign Key'); ?>

				<?= Html::dropDownList("type", '', $constraint_types, ['class' => 'form-control common_contraint_column_type_class', 'data-title' => 'constraint_column_type_id_', 'onchange' => 'checkTypeFields(this)']) ?>
            </div>
        </div>

		<div class="col-sm-3">
            <div class="form-group">
				<label class="control-label">CONSTRAINT NAME</label>

				<?= Html::textInput("name", null, ['class' => 'form-control', 'data-title' => 'constraint_column_name_id_']) ?>
            </div>
        </div>

		<div class="col-sm-2">
			<div class="form-group">
				<label class="control-label">COLUMN</label>

				<?= Html::dropDownList("columns", '', array(), ['prompt' => '-- Select --', 'class' => 'form-control', 'data-title' => 'constraint_column_columns_id_', 'multiple' => 'multiple']) ?>
			</div>
		</div>

		<div class="col-sm-2" style="display: none;">
            <div class="form-group">
				<label class="control-label">Ref. Table Name</label>

				<?= Html::dropDownList("ref_table_name", '', $tables_arr, ['class' => 'form-control', 'prompt' => '-- Select --', 'data-title' => 'constraint_foreign_key_column_ref_table_name_id_', 'onchange' => 'getTableColumns(this)']) ?>
            </div>
        </div>

		<div class="col-sm-2" style="display: none;">
			<div class="form-group">
				<label class="control-label">Reference Columns</label>

				<?= Html::dropDownList("ref_columns", '', array(), ['class' => 'form-control', 'prompt' => '-- Select --', 'data-title' => 'constraint_foreign_key_column_column_id_', 'multiple' => 'multiple']) ?>
			</div>
		</div>

		<div class="col-sm-1">
			<div class="form-group">
				<br>
				<span class="glyphicon glyphicon-remove remove-constraint-icon"></span>
			</div>
		</div>
    </div>

	<div class="form-group pull-left add-more-constraint-btn-div">
		<?= Html::button(Yii::t('app', 'Add More'), ['class' => 'btn btn-default add-constraint']); ?>
	</div>

    <div class="button-block">
		<br><br>
        <?= Html::a(Yii::t('app', 'Back'), Url::toRoute('index'), ['class' => 'btn btn-link']); ?>
        <?= Html::submitButton(Yii::t('app', 'Submit'), ['class' => 'btn btn-primary']); ?>
    </div>
<?php ActiveForm::end() ?>
