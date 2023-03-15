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

$this->title = Yii::t('app', 'Job Scheduler Step 3');

if ($baseLanguages = GetListList::getByNames([GetListList::BASE_NAME_LANGUAGE])) {
    $baseLanguages = ArrayHelper::map($baseLanguages[GetListList::BASE_NAME_LANGUAGE], 'description', 'entry_name');
}

$screenType = JobSchedulerForm::$screen_types[1];

?>

<div class="row">
	<div class="col-sm-12">
		<div class="form-group">
			<label class="control-label search_field_label">Search</label>
			<input class="form-control" id="states_search" type="text" placeholder="Search Field" />
		</div>
	</div>
</div>

<br>

<div class="builder-constructor">
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
