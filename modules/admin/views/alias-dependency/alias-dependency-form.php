<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 */

/* @var $this yii\web\View */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use app\models\GetAliasList;

use app\models\GetAliasInfo;
use yii\helpers\ArrayHelper;

use yii\web\View;

if (($aliasList = GetAliasInfo::getData([],[],['field_out_list' => ['AliasDatabaseTable']])) && !empty($aliasList->list)) {
    $aliasList = $aliasList->list;
}

$alias_type_list = GetAliasList::getAliasTypesDropdown();

$final_alias_type = array();

foreach($alias_type_list as $alias_type) {
	if($alias_type == 'Database Field' || $alias_type == 'Array') {
		if($alias_type == 'Database Field')
			$alias_type = 'Alias';

		$final_alias_type[$alias_type] = $alias_type;
	}
}

$this->title = Yii::t('app', $data['action']);

if(isset($data['data'][0]) && !empty($data['data'][0]['DependentsOn'])) {
	$dependentsOn_explode = explode(';', $data['data'][0]['DependentsOn']);
}

$this->registerJs(/** @lang JavaScript */"
    $(document)
		.on('submit', '#w0', function () {
			//alert('ppppppppppppp');

			var check = true;

			var alias_type_id = $('#aliasform-aliastype');
			var alias_table_id = $('#aliasform-aliasdatabasetable');
			var request_table_id = $('#aliasdependencyform-requesttable');
			var dependentson_id = $('#aliasdependencyform-dependentson');
			var dependency_type_id = $('#aliasdependencyform-dependencytype');

			alias_type_id.parent().removeClass('has-error');
			alias_type_id.next('div').remove();

			alias_table_id.parent().removeClass('has-error');
			alias_table_id.next('div').remove();

			request_table_id.parent().removeClass('has-error');
			request_table_id.next('div').remove();

			/*dependentson_id.parent().removeClass('has-error');
			dependentson_id.next('div').remove();*/

			dependency_type_id.parent().removeClass('has-error');
			dependency_type_id.next('div').remove();

			if(alias_type_id.val() == '') {
				check = false;

				alias_type_id.parent().addClass('has-error');
				var error_msg_div = $('<div />', {class: 'help-block'}).html('Please fill out this field.');
				alias_type_id.after(error_msg_div);
			} else if(alias_table_id.val() == '') {
				check = false;

				alias_type_id.parent().removeClass('has-error');
				alias_type_id.next('div').remove();

				alias_table_id.parent().addClass('has-error');
				var error_msg_div = $('<div />', {class: 'help-block'}).html('Please fill out this field.');
				alias_table_id.after(error_msg_div);
			} else if(request_table_id.val() == '') {
				check = false;

				alias_table_id.parent().removeClass('has-error');
				alias_table_id.next('div').remove();

				request_table_id.parent().addClass('has-error');
				var error_msg_div = $('<div />', {class: 'help-block'}).html('Please fill out this field.');
				request_table_id.after(error_msg_div);
			} else if(dependentson_id.val() == '') {
				check = false;

				request_table_id.parent().removeClass('has-error');
				request_table_id.next('div').remove();

				dependentson_id.parent().addClass('has-error');
				var error_msg_div = $('<div />', {class: 'help-block'}).html('Please fill out this field.');
				dependentson_id.after(error_msg_div);
			} else if(dependency_type_id.val() == '') {
				check = false;

				/*dependentson_id.parent().removeClass('has-error');
				dependentson_id.next('div').remove();*/

				dependency_type_id.parent().addClass('has-error');
				var error_msg_div = $('<div />', {class: 'help-block'}).html('Please fill out this field.');
				dependency_type_id.after(error_msg_div);
			} else if(dependency_type_id.val() == 'PKS') {
				if($('#w0 .alias-subvalues-list table>tbody').html() == '') {
					var con = confirm('List can not be blank!!!');

					if(con) {
						check = false;
					} else {
						check = false;
					}
				}
			} else {
				check = true;
			}

			if(!check) {
				return false;
			}
		});
", View::POS_HEAD);

?>

<h1><?= $this->title ?></h1>
<?php \app\components\ThemeHelper::printFlashes(); ?>


<div class="border-form-block">
	<?php $form = ActiveForm::begin() ?>
		<div class="form-group field-aliasform-aliastype required">
			<label class="control-label" for="aliasform-aliastype">Alias Type</label>

			<?= Html::dropDownList('AliasDependencyForm[AliasType]', !empty($data['data'][0]['AliasType']) ? $data['data'][0]['AliasType'] : null, $final_alias_type, [
				'prompt' => '-- select alias type --',
				'id' => 'aliasform-aliastype',
				'class' => 'form-control'])
			?>
		</div>

		<div class="form-group field-aliasform-aliasdatabasetable required ">
			<label class="control-label" for="aliasform-aliasdatabasetable">Alias Table</label>

			<?= Html::dropDownList('AliasDependencyForm[AliasDatabaseTable]', !empty($data['data'][0]['AliasTable']) ? $data['data'][0]['AliasTable'] : null, array(), [
				'prompt' => 'Loading...',
				'id' => 'aliasform-aliasdatabasetable',
				'class' => 'form-control'])
			?>

			<input type="hidden" id="aliasform-aliasdatabasetable_stored" class="form-control" value="<?php if(isset($data['data'][0])) echo $data['data'][0]['AliasTable']; ?>" aria-required="true" />
		</div>

		<div class="form-group field-aliasdependencyform-requesttable required">
			<label class="control-label" for="aliasdependencyform-requesttable">Request Table</label>

			<?= Html::dropDownList('AliasDependencyForm[RequestTable]', !empty($data['data'][0]['RequestTable']) ? $data['data'][0]['RequestTable'] : null, ArrayHelper::map($aliasList, 'AliasDatabaseTable', 'AliasDatabaseTable'), [
				'prompt' => '',
				'id' => 'aliasdependencyform-requesttable',
				'class' => 'form-control'])
			?>
		</div>

		<div class="form-group field-aliasdependencyform-dependentson">
			<label class="control-label" for="aliasdependencyform-dependentson">Dependents On</label>

			<?= \brussens\bootstrap\select\Widget::widget([
				'name' => 'data_field',
				'options' => [
					'id' => 'aliasdependencyform-dependentson',
					'class' => 'form-control',
					'name' => 'AliasDependencyForm[DependentsOn]',
					'data-live-search' => 'true'
				],
				'items' => []
			]) ?>

			<input type="hidden" id="aliasform-aliasdependentson_stored" class="form-control" value="<?php if(isset($dependentsOn_explode) && !empty($dependentsOn_explode)) echo $dependentsOn_explode[0]; ?>" aria-required="true" />
		</div>

		<div class="form-group field-aliasdependencyform-dependencytype  required"><!-- has-success -->
			<label class="control-label" for="aliasdependencyform-dependencytype">Dependency Type</label>

			<?php $dependency_type = array('PKS' => 'PKS', 'REL' => 'REL'); ?>

			<?= Html::dropDownList('AliasDependencyForm[DependencyType]', !empty($data['data'][0]['DependencyType']) ? $data['data'][0]['DependencyType'] : null, $dependency_type, [
				'prompt' => '-- select dependency type --',
				'id' => 'aliasdependencyform-dependencytype',
				'class' => 'form-control'])
			?>
		</div>

		<div class="button-block">
			<button type="button" id="add_dependency" tab="2" class="add-to-list atl-dependency btn btn-primary">Add to List</button>
		</div>

		<fieldset class="border-form-block">
			<legend>
				<label class="control-label">List</label>
			</legend>

			<div class="alias-subvalues-list" style='display: none'>
				<table width="100%" cellpadding="10" class="table table-hover">
					<thead>
						<tr>
							<th>Dependent</th>
							<th>&nbsp;</th>
						</tr>
					</thead>

					<tbody><?php if(isset($data['data']) && !empty($data['data']) && sizeof($data['data']) == 1) { ?>
							<?php $r = 1; foreach($dependentsOn_explode as $key => $val) { ?>
								<tr class="fieldwrapper button-block" id="field<?php echo $r; ?>" elno="<?php echo $r; ?>" tab="<?php echo $r; ?>">
									<td class='col-xs-11'><input type="text" class="form-control list-item" value="<?php echo $val; ?>" name="AliasDependencyForm[DependentsOn][]" readonly /><input type="hidden" class="form-control" value="<?php if(isset($data['data'][0]) && !empty($data['data'][0])) { echo $data['data'][0]['RequestTable']; } else { echo ''; } ?>" name="AliasDependencyForm[RequestTable][]" readonly /></td>
									<td class='col-xs-1'><input type="button" class="remove btn btn-primary" value="-" /></td>
								</tr>
							<?php $r++; } ?>
						<?php } ?></tbody>
				</table>
			</div>
		</fieldset>

		<br />

		<div class="button-block">
			<p>&nbsp;</p>

			<?= Html::a(Yii::t('app', 'Back'), Url::toRoute(['/admin/alias-dependency']), ['class' => 'btn btn-link']); ?>

			<?= Html::submitButton(Yii::t('app', 'Submit'), ['class' => 'btn btn-primary']); ?>
		</div>

		<input type="hidden" id="customAPI" name="customAPI" value="0" />
	<?php ActiveForm::end() ?>
</div>