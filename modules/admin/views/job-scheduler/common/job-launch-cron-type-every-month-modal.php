<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 */

/* @var $this yii\web\View */

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use app\models\CustomLibs;

?>

<?php
	$launch_condition_every_month_day_of_the_month = '';
	$launch_condition_every_month = '';

	if($model->launch_condition && $model->launch_type == 'JobLaunchType.Cron') {
		$launch_condition_explode = explode(' ', $model->launch_condition);
		$launch_condition_every_month_day_of_the_month = $launch_condition_explode[2];
		$launch_condition_every_month = $launch_condition_explode[1].':'.$launch_condition_explode[0];
	}
?>

<div class="modal fade" id="job-launch-cron-type-every-month-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?= Yii::t('app', 'Cron Type :: Every Month') ?></h4>
            </div>

            <div class="modal-body">
				<div class="form-group">
                    <label><?= Yii::t('app', 'Run') ?></label>

                    <?= Html::dropDownList('cron_every_month', '', array('every_month' => 'Every Month'), ['class' => 'form-control', 'id' => 'every_month']) ?>
                </div>

				<div class="form-group">
                    <label><?= Yii::t('app', 'Day of the Month') ?></label>

					<?php $day_of_month = array('' => '-- Select Day Of Month --'); for($d = 1; $d < 32; $d++) { ?>
						<?php array_push($day_of_month, $d); ?>
					<?php } ?>

                    <?= Html::dropDownList('day_of_the_month', $launch_condition_every_month_day_of_the_month, $day_of_month, ['class' => 'form-control', 'id' => 'day_of_the_month']) ?>
                </div>

				<div class="form-group">
                    <label><?= Yii::t('app', 'Time') ?></label>

                    <div class='input-group date' id='cron_every_month_timepicker'>
						<?= Html::input('text', $launch_condition_every_month, 'cron_every_month_timepicker', ['class' => 'form-control cron_every_month_timepicker']) ?>

						<span class="input-group-addon">
							<span class="glyphicon glyphicon-calendar"></span>
						</span>
					</div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-outline" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary btn-cron-every-month-done" data-dismiss="modal">Done</button>
            </div>
        </div>
    </div>
</div>