<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 *
 * @var $this yii\web\View
 * @var $model \app\modules\admin\models\forms\ExtensionFunctionForm
 */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use app\models\GetListList;

use yii\helpers\ArrayHelper;
use insolita\iconpicker\Iconpicker;
use app\models\GetAliasInfo;
use app\models\CustomLibs;
use app\modules\admin\models\GroupScreen;
use app\modules\admin\models\forms\JobSchedulerForm;
use kdn\yii2\JsonEditor;

if (($libraryList = CustomLibs::getModelInstance()) && !empty($libraryList->lib_list)) {
    $libraryList = ArrayHelper::map($libraryList->lib_list, 'lib_name', function ($data) {
        return $data['lib_name'] . (!empty($data['lib_descr']) ? ' - ' . $data['lib_descr'] : '');
    });
}

if (($aliasList = GetAliasInfo::getData([],[],['field_out_list' => ['AliasDatabaseTable']])) && !empty($aliasList->list)) {
    $aliasList = ArrayHelper::map($aliasList->list, 'AliasDatabaseTable', 'AliasDatabaseTable');
} else {
    $aliasList = [];
}

$JobSchedulerLaunchTypeLists = GetListList::getArrayForSelectByNames([GetListList::BASE_NAME_JOB_SCHEDULER_TYPE], true, false);

$active_status = array('Y' => 'Active', 'N' => 'Inactive');

//echo "<pre>"; print_r($model); die;

?>

<style>
	a.btn, a.btn:hover {
		color: #000 !important;
	}

	.custom-form-control { border: none !important; }
</style>

<?php $form = ActiveForm::begin() ?>
    <div class="row">
        <div class="col-sm-6">
            <?= $form->field($model, 'job_name')->input('text', ['class' => 'form-control nt-save-form']) ?>
        </div>

        <div class="col-sm-6">
            <?= $form->field($model, 'job_description')->input('text', ['class' => 'form-control nt-save-form']) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6">
            <?= $form->field($model, 'launch_type')->dropDownList($JobSchedulerLaunchTypeLists[GetListList::BASE_NAME_JOB_SCHEDULER_TYPE], ['id' => 'joblaunchform-launch_type', ['class' => 'form-control nt-save-form']]);?>
        </div>

        <div class="col-sm-6">
			<?= $form->field($model, 'is_active')->radioList($model::$types, ['class' => 'form-control custom-form-control nt-save-form']); ?>
        </div>

        <div class="col-sm-6">
			<div id="launch_type_common_div" <?php if($model->launch_type == "JobLaunchType.At" || $model->launch_type == "JobLaunchType.Cron") { ?>style="display: none;"<?php } else { ?>style="display: block;"<?php } ?>>
				<div class="form-group">
					<?= Html::label(Yii::t('app', 'Launch Condition')) ?>

					<?= Html::activeInput('text', $model, 'launch_condition', ['class' => 'form-control joblaunchform-launch_condition nt-save-form']) ?>
				</div>
			</div>

			<div id="launch_type_at_div" <?php if($model->launch_type == "JobLaunchType.At") { ?>style="display: block;"<?php } else { ?>style="display: none;"<?php } ?>>
				<?php
					$launch_condition_at_radio_selected_date = true;
					$launch_condition_at_radio_selected_time = false;
					$launch_condition_at_radio_selected_both = false;

					$datepickerdiv = 'block';
					$timepickerdiv = 'none';
					$datetimepickerdiv = 'none';

					if($model->launch_condition && $model->launch_type == 'JobLaunchType.At') {
						$launch_condition_explode = explode(' ', $model->launch_condition);

						if(sizeof($launch_condition_explode) == 2) {
							$launch_condition_at_radio_selected_both = true;
							$datetimepickerdiv = 'block';
							$datepickerdiv = 'none';
						} else if($launch_condition_explode[0] != '') {
							$datepickerdiv = 'none';

							$launch_condition_explode_time = explode(':', $launch_condition_explode[0]);

							if(sizeof($launch_condition_explode_time) == 3) {
								$launch_condition_at_radio_selected_time = true;
								$timepickerdiv = 'block';
							} else {
								$launch_condition_at_radio_selected_date = true;
								$datepickerdiv = 'block';
							}
						}
					}
				?>

				<div class="form-group">
					<?= Html::radio('launch_type_condition_radio', $launch_condition_at_radio_selected_date, ['label' => 'Date', 'value' => 1]) ?>
					<?= Html::radio('launch_type_condition_radio', $launch_condition_at_radio_selected_time, ['label' => 'Time', 'value' => 2]) ?>
					<?= Html::radio('launch_type_condition_radio', $launch_condition_at_radio_selected_both, ['label' => 'Both', 'value' => 3]) ?>
				</div>

				<div class="form-group" id="datepickerdiv" style="display: <?php echo $datepickerdiv; ?>">
					<div class='input-group date' id='datepicker'>
						<?= Html::activeInput('text', $model, 'launch_condition', ['class' => 'form-control joblaunchform-launch_condition nt-save-form']) ?>

						<span class="input-group-addon">
							<span class="glyphicon glyphicon-calendar"	></span>
						</span>
					</div>
				</div>

				<div class="form-group" id="timepickerdiv" style="display: <?php echo $timepickerdiv; ?>;">
					<div class='input-group date' id='timepicker'>
						<?= Html::activeInput('text', $model, 'launch_condition', ['class' => 'form-control joblaunchform-launch_condition nt-save-form']) ?>

						<span class="input-group-addon">
							<span class="glyphicon glyphicon-calendar"></span>
						</span>
					</div>
				</div>

				<div class="form-group" id="datetimepickerdiv" style="display: <?php echo $datetimepickerdiv; ?>;">
					<div class='input-group date' id='datetimepicker'>
						<?= Html::activeInput('text', $model, 'launch_condition', ['class' => 'form-control joblaunchform-launch_condition nt-save-form']) ?>

						<span class="input-group-addon">
							<span class="glyphicon glyphicon-calendar"></span>
						</span>
					</div>
				</div>
			</div>

			<div id="launch_type_cron_div" <?php if($model->launch_type == "JobLaunchType.Cron") { ?>style="display: block;"<?php } else { ?>style="display: none;"<?php } ?>>
				<div class="form-group">
					<?= Html::label(Yii::t('app', 'Cron Type')) ?>

					<?php
						$launch_condition_cron_preset_selected = '';
						$cron_type_select_div = 'none';

						$cron_type_every_day = 'none';
						$cron_type_every_week_day = 'none';
						$cron_type_every_weekend = 'none';
						$cron_type_every_month = 'none';
						$cron_type_custom = 'none';

						if($model->launch_condition && $model->launch_type == 'JobLaunchType.Cron') {
							$cron_type_select_div = 'block';

							$launch_condition_explode = explode(' ', $model->launch_condition);

							if($launch_condition_explode[0] != '*' && $launch_condition_explode[1] != '*') {
								$launch_condition_cron_preset_selected = 'every_day';
								$cron_type_every_day = 'table';
							} else if($launch_condition_explode[0] != '*' && $launch_condition_explode[1] != '*' && $launch_condition_explode[4] != '*') {
								$launch_condition_cron_preset_selected = 'every_week_day';
								$cron_type_every_week_day = 'table';
							} else if($launch_condition_explode[0] != '*' && $launch_condition_explode[1] != '*' && $launch_condition_explode[4] = '5') {
								$launch_condition_cron_preset_selected = 'every_weekend';
								$cron_type_every_weekend = 'table';
							} else if($launch_condition_explode[0] != '*' && $launch_condition_explode[1] != '*' && $launch_condition_explode[2] != '*') {
								$launch_condition_cron_preset_selected = 'every_month';
								$cron_type_every_month = 'table';
							} else {
								$launch_condition_cron_preset_selected = 'custom';
								$cron_type_custom = 'table';
							}
						}
					?>

					<?php $cron_types = array('' => '-- Select --', 'every_day' => 'Every Day', 'every_week_day' => 'Every Week Day', 'every_weekend' => 'Every Weekend', 'every_month' => 'Every Month', 'custom' => 'Custom'); ?>
					<?= Html::dropDownList('cron_day_of_month', $launch_condition_cron_preset_selected, $cron_types, ['class' => 'form-control nt-save-form', 'id' => 'cron_types']) ?>
				</div>

				<div class="form-group" id="cron_type_select_div" style="display: <?php echo $cron_type_select_div; ?>;">
					<?= Html::label(Yii::t('app', 'Cron Setting')) ?>

					<div class="input-group" id="cron_type_every_day" style="display: <?php echo $cron_type_every_day; ?>;">
						<span class="input-group-btn">
							<button class="btn btn-default job-launch-cron-type" data-toggle="modal" data-target="#job-launch-cron-type-every-day-modal" type="button">
								<span class="glyphicon glyphicon-cog"></span>
							</button>
						</span>

						<?= Html::activeInput('text', $model, 'launch_condition', ['class' => 'form-control joblaunchform-launch_condition nt-save-form']) ?>
					</div>

					<div class="input-group" id="cron_type_every_week_day" style="display: <?php echo $cron_type_every_week_day; ?>;">
						<span class="input-group-btn">
							<button class="btn btn-default job-launch-cron-type" data-toggle="modal" data-target="#job-launch-cron-type-every-week-day-modal" type="button">
								<span class="glyphicon glyphicon-cog"></span>
							</button>
						</span>

						<?= Html::activeInput('text', $model, 'launch_condition', ['class' => 'form-control joblaunchform-launch_condition nt-save-form']) ?>
					</div>

					<div class="input-group" id="cron_type_every_weekend" style="display: <?php echo $cron_type_every_weekend; ?>;">
						<span class="input-group-btn">
							<button class="btn btn-default job-launch-cron-type" data-toggle="modal" data-target="#job-launch-cron-type-every-weekend-modal" type="button">
								<span class="glyphicon glyphicon-cog"></span>
							</button>
						</span>

						<?= Html::activeInput('text', $model, 'launch_condition', ['class' => 'form-control joblaunchform-launch_condition nt-save-form']) ?>
					</div>

					<div class="input-group" id="cron_type_every_month" style="display: <?php echo $cron_type_every_month; ?>;">
						<span class="input-group-btn">
							<button class="btn btn-default job-launch-cron-type" data-toggle="modal" data-target="#job-launch-cron-type-every-month-modal" type="button">
								<span class="glyphicon glyphicon-cog"></span>
							</button>
						</span>

						<?= Html::activeInput('text', $model, 'launch_condition', ['class' => 'form-control joblaunchform-launch_condition nt-save-form']) ?>
					</div>

					<div class="input-group" id="cron_type_custom" style="display: <?php echo $cron_type_custom; ?>;">
						<span class="input-group-btn">
							<button class="btn btn-default job-launch-cron-type" data-toggle="modal" data-target="#job-launch-cron-type-custom-modal" type="button">
								<span class="glyphicon glyphicon-cog"></span>
							</button>
						</span>

						<?= Html::activeInput('text', $model, 'launch_condition', ['class' => 'form-control joblaunchform-launch_condition nt-save-form']) ?>
					</div>
				</div>
			</div>
        </div>

        <div class="col-sm-6">
			<div class="form-group">
				<?= Html::label(Yii::t('app', 'Launch Params')) ?>

				<div class="input-group" id="job_launch_params_div">
					<span class="input-group-btn">
						<button class="btn btn-default job-launch-params-load" data-toggle="modal" data-target="#job-launch-params-modal" data-url="<?php echo Url::to(['/admin/job-scheduler/lib-function-job'], true); ?>" type="button">
							<span class="glyphicon glyphicon-cog"></span>
						</button>
					</span>

					<?= Html::activeInput('hidden', $model, 'launch_params', ['class' => 'form-control nt-save-form', 'id' => 'joblaunchform-launch_params']) ?>
				</div>
			</div>

			<div class="form-group">
				<div id="jstree1"></div>
			</div>
		</div>
    </div>

	<?php if(!empty($builder)) { ?>
		<div class="row">
			<div class="col-sm-6">
				<div class="form-group">
					<label class="control-label">Library</label>

					<?php
						if (empty($update)) {
							echo Html::dropDownList('lib_name', !empty($model->jobs_params->function_extensions_job_params->lib_name) ? $model->jobs_params->function_extensions_job_params->lib_name : [], $libraryList, ['prompt' => '-- Select --', 'class' => 'form-control library-first-step nt-save-form', 'required' => true]);

							//echo Html::activeDropDownList(!empty($model->jobs_params->function_extensions_job_params->lib_name) ? $model->jobs_params->function_extensions_job_params->lib_name : null, 'lib_name', $libraryList, ['prompt' => '-- Select --', 'required' => true, 'class' => 'form-control library-first-step nt-save-form']);
						} else {
							echo Html::activeInput('text', $model->jobs_params->function_extensions_job_params->lib_name, 'lib_name', ['disabled' => true, 'required' => true, 'class' => 'form-control library-first-step nt-save-form']);
						}
					?>
				</div>
			</div>

			<div class="col-sm-6">
				<div class="form-group">
					<label class="control-label">Primary table</label>

					<?= Html::hiddenInput('is_use_alias_framework', !empty($model->jobs_params->function_extensions_job_params->alias_framework_info->enable) ? $model->jobs_params->function_extensions_job_params->alias_framework_info->enable : true, ['class' => 'is-use-alias-framework nt-save-form']) ?>

					<?= Html::dropDownList('request_primary_table', !empty($model->jobs_params->function_extensions_job_params->alias_framework_info->request_primary_table) ? $model->jobs_params->function_extensions_job_params->alias_framework_info->request_primary_table : [], $aliasList, ['class' => 'form-control primary-table nt-save-form', 'required' => true]) ?>
				</div>
			</div>
		</div>

		<div class="row alias-framework-functions hidden">
			<div class="col-sm-4">
				<div class="form-group">
					<label><?= Yii::t('app','Function name for update')?></label>
					<?= Html::dropDownList('alias_framework_func_update', null, [], [
						'prompt' => '',
						'class' => 'form-control alias-framework-func-update',
						'required' => true,
						'disabled' => empty($model->jobs_params->function_extensions_job_params->alias_framework_info->data_source_update) || empty($model->jobs_params->alias_framework_info->enable),
						'data' => [
							'value' => !empty($model->jobs_params->function_extensions_job_params->alias_framework_info->data_source_update) ? $model->jobs_params->alias_framework_info->data_source_update : null
						]
					]); ?>
				</div>
			</div>

			<div class="col-sm-4">
				<div class="form-group">
					<label><?= Yii::t('app','Function name for delete') ?></label>
					<?= Html::dropDownList('alias_framework_func_delete', null, [], [
						'prompt' => '',
						'class' => 'form-control alias-framework-func-delete',
						'required' => true,
						'disabled' => empty($model->jobs_params->function_extensions_job_params->alias_framework_info->data_source_delete) || empty($model->jobs_params->alias_framework_info->enable),
						'data' => [
							'value' => !empty($model->jobs_params->function_extensions_job_params->alias_framework_info->data_source_delete) ? $model->jobs_params->alias_framework_info->data_source_delete : null
						]
					]); ?>
				</div>
			</div>

			<div class="col-sm-4">
				<div class="form-group">
					<label><?= Yii::t('app','Function name for insert') ?></label>
					<?= Html::dropDownList('alias_framework_func_insert', null, [], [
						'prompt' => '',
						'class' => 'form-control alias-framework-func-insert',
						'required' => true,
						'disabled' => empty($model->jobs_params->function_extensions_job_params->alias_framework_info->data_source_insert) || empty($model->jobs_params->alias_framework_info->enable),
						'data' => [
							'value' => !empty($model->jobs_params->function_extensions_job_params->alias_framework_info->data_source_insert) ? $model->jobs_params->alias_framework_info->data_source_insert : null
						]
					]); ?>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-sm-6">
				<div class="form-group field-tabform-functions">
					<?= Html::label(Yii::t('app', 'Simple search')) ?>

					<div class="input-group">
						<span class="input-group-addon">
							<input name="search-configuration-radio" class="search-configuration-radio" value="<?= JobSchedulerForm::SIMPLE_SEARCH_TYPE ?>" type="radio" title="Enable search type">
						</span>

						<span class="input-group-btn">
							<button class="btn btn-default library-first-step-settings" data-toggle="modal" data-target="#setting-library-modal" type="button" disabled>
								<span class="glyphicon glyphicon-cog"></span>
							</button>
						</span>

						<?= Html::dropDownList('search-function-name', null, [], [
							'class' => 'form-control search-function-name nt-save-form',
							'aria-required' => "true",
							'disabled' => true
						]) ?>
					</div>

					<?= Html::hiddenInput('config', json_encode(!empty($model->jobs_params->function_extensions_job_params->search_function_info->config) ? $model->jobs_params->function_extensions_job_params->search_function_info->config : null), ['class' => 'search-function-config nt-save-form']
					) ?>
				</div>
			</div>

			<div class="col-sm-6">
				<div class="form-group search-block-wrapper">
					<?= Html::label(Yii::t('app', 'Multi-search with custom query')) ?>
					<div class="input-group">
						<span class="input-group-addon">
							<input name="search-configuration-radio" class="search-configuration-radio" value="<?= JobSchedulerForm::CUSTOM_SEARCH_TYPE ?>" type="radio" title="Enable search type">
						</span>
						<span class="input-group-btn">
							<button class="btn btn-default search-configuration-button" data-toggle="modal" data-target="#search-configuration-modal" type="button" disabled>
								<span class="glyphicon glyphicon-cog"></span>
							</button>
						</span>
						<?= Html::dropDownList(
							'search_block_select',
							!empty($model->jobs_params->function_extensions_job_params->search_custom_query->query_pk) ? $model->jobs_params->function_extensions_job_params->search_custom_query->query_pk : null,
							$customQueryList,
							[
								'prompt' => '-- Select --',
								'id' => 'search-block-select',
								'class' => 'form-control nt-save-form',
								'aria-required' => "true",
								'disabled' => true
							]
						) ?>
					</div>
				</div>
			</div>

			<?= Html::hiddenInput('search_custom_query', json_encode(!empty($model->jobs_params->function_extensions_job_params->search_custom_query) ? $model->jobs_params->function_extensions_job_params->search_custom_query : NULL), ['class' => 'search-custom-query nt-save-form']) ?>
		</div>
	<?php } ?>

    <div class="button-block">
		<?= Html::a(Yii::t('app', 'Back'), Url::toRoute('index'), ['class' => 'btn btn-link']); ?>

		<?php if (empty($builder)): ?>
            <?= Html::submitButton(Yii::t('app', 'Submit'), ['class' => 'btn btn-primary']); ?>
        <?php else: ?>
            <?= Html::submitButton(Yii::t('app', 'Next'), ['class' => 'btn btn-primary next-step-btn']); ?>
        <?php endif ?>
    </div>
<?php ActiveForm::end() ?>

<?php if (!empty($builder)): ?>
    <?php if (empty($update) && !empty(Yii::$app->request->get()['return'])): ?>
        <script>screenCreator.ntSaveForms();</script>
    <?php elseif (empty($update)): ?>
        <script>screenCreator.ntClearForms();</script>
        <script>screenCreator.ntSaveForms();</script>
    <?php else: ?>
        <script>screenCreator.ntClearForms();</script>
    <?php endif; ?>
<?php endif; ?>

<?php echo $this->render('common/job-launch-cron-type-every-day-modal', ['model' => $model]); ?>
<?php echo $this->render('common/job-launch-cron-type-every-week-day-modal', ['model' => $model]); ?>
<?php echo $this->render('common/job-launch-cron-type-every-weekend-modal', ['model' => $model]); ?>
<?php echo $this->render('common/job-launch-cron-type-every-month-modal', ['model' => $model]); ?>
<?php echo $this->render('common/job-launch-cron-type-custom-modal', ['model' => $model]); ?>
<?php echo $this->render('common/job-launch-params-modal', ['model' => $model]); ?>

<?php if (!empty($builder)) { ?>
	<?php echo $this->render('common/library-modal', ['model' => $model]); ?>
	<?php echo $this->render('common/custom-query-modal'); ?>
<?php } ?>
