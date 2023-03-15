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
	$launch_condition_every_week_day_of_the_week = '';
	$launch_condition_every_week_day = '';

	if($model->launch_condition && $model->launch_type == 'JobLaunchType.Cron') {
		$launch_condition_explode = explode(' ', $model->launch_condition);
		$launch_condition_every_week_day_of_the_week = $launch_condition_explode[4];
		$launch_condition_every_week_day = $launch_condition_explode[1].':'.$launch_condition_explode[0];
	}
?>

<div class="modal fade" id="job-launch-cron-type-every-week-day-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?= Yii::t('app', 'Cron Type :: Every Week Day') ?></h4>
            </div>

            <div class="modal-body">
				<div class="form-group">
                    <label><?= Yii::t('app', 'Run') ?></label>

                    <?= Html::dropDownList('cron_every_week_day', '', array('every_week_day' => 'Every Week Day'), ['class' => 'form-control', 'id' => 'every_week_day']) ?>
                </div>

				<div class="form-group">
                    <label><?= Yii::t('app', 'Day of The Week') ?></label>

					<?php $days = array('0' => 'Sunday', '1' => 'Monday', '2' => 'Tuesday', '3' => 'Wednesday', '4' => 'Thursday', '5' => 'Friday', '6' => 'Saturday'); ?>
                    <?= Html::dropDownList('day_of_the_week', $launch_condition_every_week_day_of_the_week, $days, ['class' => 'form-control', 'id' => 'day_of_the_week']) ?>
                </div>

				<div class="form-group">
                    <label><?= Yii::t('app', 'Time') ?></label>

                    <div class='input-group date' id='cron_every_week_day_timepicker'>
						<?= Html::input('text', $launch_condition_every_week_day, 'cron_every_week_day_timepicker', ['class' => 'form-control cron_every_week_day_timepicker']) ?>

						<span class="input-group-addon">
							<span class="glyphicon glyphicon-calendar"></span>
						</span>
					</div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-outline" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary btn-cron-every-week-day-done" data-dismiss="modal">Done</button>
            </div>
        </div>
    </div>
</div>