<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 */

/**
 * @var $this yii\web\View
 * @var $model ScreenForm
 * @var $pullUpList array
 */

use app\modules\admin\models\forms\ScreenForm;
use app\modules\admin\models\forms\JobSchedulerForm;
use app\models\GetListList;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

$this->title = Yii::t('app', 'Job Scheduler Step 2');

if ($baseLanguages = GetListList::getByNames([GetListList::BASE_NAME_LANGUAGE])) {
    $baseLanguages = ArrayHelper::map($baseLanguages[GetListList::BASE_NAME_LANGUAGE], 'description', 'entry_name');
}

$screenType = JobSchedulerForm::$screen_types[1];

?>

<div class="builder-constructor">
    <div class="tab-content">
        <div role="tabpanel" class="tab-pane active" id="policy_dashboard">
            <div style="min-height: 400px;">
                <div id="new-tab-block">
                    <div class="clearfix">

						<!--<div class="alert alert-warning header-section section-panel" data-row="0" data-col="0" style="position: relative">
							<div class="header-section-content panel-body">
								<div class="loader"></div>
							</div>

							<span class="panel-controls">
								<span class="glyphicon glyphicon-collapse-down fields-button-config" style="display: none;"></span>
								<span class="glyphicon glyphicon-comment fields-label-config" style="display: none;"></span>
								<span class="glyphicon glyphicon-import fields-constructor-btn" data-toggle="modal" data-target="#fields-modal" aria-hidden="true" style="display: none;"></span>
								<span class="glyphicon glyphicon-cog setting-icon" data-row="0" data-col="0" data-toggle="modal" data-target="#setting-modal" aria-hidden="true" title="<?= Yii::t('app', 'Setting section') ?>" style="display: none;"></span>
							</span>
						</div>-->

						<div class="stats-section">
                            <?php $colWidth = ($screenType['col_count'] == 1) ? 12 : 6; ?>
                            <?php for ($i = 1; $i <= $screenType['row_count']; $i++): ?>
                                <div class="row">
                                    <?php for ($j = 1; $j <= $screenType['col_count']; $j++): ?>
                                        <div class="col-sm-<?= $colWidth ?>">
                                            <div class="panel panel-default panel-window section-panel"
                                                 data-row="<?= $i ?>" data-col="<?= $j ?>" data-type="chart">
                                                <div class="panel-heading">
                                                    <h3 class="panel-title"><?= Yii::t('app', 'New section') ?></h3>
                                                    <span class="panel-controls">
                                                        <span class="glyphicon glyphicon-collapse-down fields-button-config" style="display: none;"></span>
                                                        <span class="glyphicon glyphicon-comment fields-label-config" style="display: none;"></span>
                                                        <span class="glyphicon glyphicon-import fields-constructor-btn"
                                                              data-toggle="modal" data-target="#fields-modal"
                                                              aria-hidden="true"
                                                              style="display: none;"></span>
                                                        <span class="glyphicon glyphicon-cog setting-icon"
                                                              data-row="<?= $i ?>" data-col="<?= $j ?>"
                                                              data-toggle="modal" data-target="#setting-modal"
                                                              aria-hidden="true"
                                                              style="display: none;"></span>
                                                    </span>
                                                </div>
                                                <div class="panel-body">
                                                    <div class="loader"></div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endfor ?>
                                </div>
                            <?php endfor ?>
                        </div>
						
                    </div>
                </div>
            </div>

            <div class="btn-group nav-right-group" role="group">
                <button type="button" class="btn btn-default">Fields Configuration</button>
            </div>
        </div>

		<div role="tabpanel" class="tab-pane active">
			<div class="row">
				<div class="col-sm-12">
					<div class="form-group">
						<label class="control-label search_field_label">Search</label>
						<input class="form-control" id="states_search" type="text" placeholder="Search Field" />
					</div>

					<div class="form-group">
						<div class="nav-left-group" role="group">
							<div class="special-btns special-sub-btns-insert active">
								<a class="btn btn-link screen-insert-btn" href="javascript:void(0);" data-mode="insert">
									<span class="glyphicon glyphicon-file" aria-hidden="true"></span>
								</a>

								<a href="javascript:void(0);" class="btn btn-link screen-edit-btn" data-mode="edit" style="display: none;">
									<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
								</a>

								<a href="javascript:void(0);" class="btn btn-link screen-remove-btn" data-action="/codiac/web/site/delete-data" style="display: none;">
									<span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
								</a>
							</div>

							<div class="special-sub-btns active" style="display: none;">
								<a class="btn btn-link left-navigation-button-cancel screen-cancel-btn" href="javascript:void(0);" data-mode="empty">
									<span class="glyphicon glyphicon-remove-circle" aria-hidden="true"></span>
								</a>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div id="alias_framework_div"></div>

			<br><br>
		</div>
    </div>

	<?php $form = ActiveForm::begin([
        'id' => 'new-screen-create',
        'action' => ($model->id) ? Url::toRoute(['update', 'id' => $model->id]) : Url::toRoute(['create']),
        'fieldConfig' => [
            'template' => '{input}',
            'options' => ['tag' => false]
        ],
    ]); ?>

    <?= $form->field($model, 'job_name')->hiddenInput()->label(false); ?>
    <?= $form->field($model, 'job_description')->hiddenInput()->label(false); ?>
    <?= $form->field($model, 'launch_type')->hiddenInput()->label(false); ?>
    <?= $form->field($model, 'is_active')->hiddenInput()->label(false); ?>
    <?= $form->field($model, 'launch_condition')->hiddenInput()->label(false); ?>
    <?= $form->field($model, 'launch_params')->hiddenInput()->label(false); ?>

	<?php echo Html::hiddenInput('lib_name', $model->jobs_params->function_extensions_job_params->lib_name); ?>

    <?php
		$model->jobs_params = json_encode($model->jobs_params);
		echo $form->field($model, 'jobs_params')->hiddenInput(['id' => 'form-jobs-params'])->label(false);
    ?>

	<?php
		$model->template_layout = json_encode($model->template_layout);
		echo $form->field($model, 'template_layout')->hiddenInput(['id' => 'form-template-layout'])->label(false);
    ?>

	<?php echo Html::hiddenInput('lib_name', ''); ?>

    <div class="form-group row pull-right" style="margin-right: 0">
        <?= Html::a(Yii::t('app', 'Back'), Url::current(['return' => true]), ['class' => 'btn btn-link']); ?>

		<?php
			if($model->id)
				echo Html::submitButton(Yii::t('app', 'Update'), ['class' => 'btn btn-primary pull-right']);
			else
				echo Html::submitButton(Yii::t('app', 'Create'), ['class' => 'btn btn-primary pull-right']);
		?>
    </div>
    <?php ActiveForm::end() ?>
</div>

<?= $this->render('common/build-modal', ['model' => $model]); ?>
<?= $this->render('common/fields-modal'); ?>
<?= $this->render('common/internationalization-modal', ['languages' => $baseLanguages]); ?>
<?= $this->render('common/table-modal'); ?>
<?= $this->render('common/pullup-modal', ['pullUpList' => $pullUpList]); ?>
<?= $this->render('common/formatting-modal', ['formClass' => 'table-constructor-form', 'modalID' => 'formatting-modal-table']); ?>
<?= $this->render('common/formatting-modal', ['formClass' => 'section-formatting-form', 'modalID' => 'formatting-modal-section']); ?>
<?= $this->render('common/formatting-modal', ['formClass' => 'fields-constructor-form', 'modalID' => 'formatting-modal']); ?>
<?= $this->render('common/extensions-modal'); ?>
<?= $this->render('common/execute-function-modal', ['model' => $model]); ?>

<?= $this->render('common/js-edit-modal', ['formClass' => 'fields-constructor-form', 'modalID' => 'js-edit-modal', 'jsTemplates' => $jsTemplates]); ?>
<?= $this->render('common/js-edit-modal', ['formClass' => 'table-constructor-form', 'modalID' => 'js-edit-table', 'jsTemplates' => $jsTemplates]); ?>

<?= $this->render('common/access-modal', ['formClass' => 'table-constructor-form', 'modalID' => 'access-modal-table']); ?>
<?= $this->render('common/access-modal', ['formClass' => 'fields-constructor-form', 'modalID' => 'access-modal']); ?>
