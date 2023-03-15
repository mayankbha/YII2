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
	$launch_condition_custon_cron_minute = '*';
	$launch_condition_custon_cron_hour = '*';
	$launch_condition_custon_cron_day_of_month = '*';
	$launch_condition_custon_cron_month = '*';
	$launch_condition_custon_cron_day_of_week = '*';

	if($model->launch_condition && $model->launch_type == 'JobLaunchType.Cron') {
		$launch_condition_explode = explode(' ', $model->launch_condition);

		$launch_condition_custon_cron_minute = $launch_condition_explode[0];
		$launch_condition_custon_cron_hour = $launch_condition_explode[1];
		$launch_condition_custon_cron_day_of_month = $launch_condition_explode[2];
		$launch_condition_custon_cron_month = $launch_condition_explode[3];
		$launch_condition_custon_cron_day_of_week = $launch_condition_explode[4];
	}
?>
			
<div class="modal fade" id="job-launch-cron-type-custom-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?= Yii::t('app', 'Cron Type :: Custom Configuration') ?></h4>
            </div>

            <div class="modal-body">
                <div class="row">
					<div class="col-sm-4">
						<div class="form-group">
							<?= Html::label(Yii::t('app', 'Minute')) ?>

							<div class="input-group">
								<?php $minute = array('*' => '-- Select Minute --'); for($m = 0; $m < 60; $m++) { ?>
									<?php array_push($minute, $m); ?>
								<?php } ?>

								<?= Html::dropDownList('cron_minute', $launch_condition_custon_cron_minute, $minute, ['class' => 'form-control', 'id' => 'minute']) ?>
							</div>
						</div>
					</div>

					<div class="col-sm-4">
						<div class="form-group">
							<?= Html::label(Yii::t('app', 'Hour')) ?>

							<div class="input-group">
								<?php $hour = array('*' => '-- Select Hour --'); for($h = 0; $h < 24; $h++) { ?>
									<?php array_push($hour, $h); ?>
								<?php } ?>

								<?= Html::dropDownList('cron_hour', $launch_condition_custon_cron_hour, $hour, ['class' => 'form-control', 'id' => 'hour']) ?>
							</div>
						</div>
					</div>

					<div class="col-sm-4">
						<div class="form-group">
							<?= Html::label(Yii::t('app', 'Day of Month')) ?>

							<div class="input-group">
								<?php $day_of_month = array('*' => '-- Select Day Of Month --'); for($d = 1; $d < 32; $d++) { ?>
									<?php array_push($day_of_month, $d); ?>
								<?php } ?>

								<?= Html::dropDownList('cron_day_of_month', $launch_condition_custon_cron_day_of_month, $day_of_month, ['class' => 'form-control', 'id' => 'day_of_month']) ?>
							</div>
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-sm-4">
						<div class="form-group">
							<?= Html::label(Yii::t('app', 'Month')) ?>

							<div class="input-group">
								<?php $month = array('*' => '-- Select Month --'); for($m = 1; $m < 13; $m++) { ?>
									<?php array_push($month, $m); ?>
								<?php } ?>

								<?= Html::dropDownList('cron_month', $launch_condition_custon_cron_month, $month, ['class' => 'form-control', 'id' => 'month']) ?>
							</div>
						</div>
					</div>

					<div class="col-sm-4">
						<div class="form-group">
							<?= Html::label(Yii::t('app', 'Day of Week')) ?>

							<div class="input-group">
								<?php $day_of_week = array('0' => 'Sunday', '1' => 'Monday', '2' => 'Tuesday', '3' => 'Wednesday', '4' => 'Thursday', '5' => 'Friday', '6' => 'Saturday'); ?>
								<?= Html::dropDownList('cron_day_of_week', $launch_condition_custon_cron_day_of_week, $day_of_week, ['class' => 'form-control', 'id' => 'day_of_week']) ?>
							</div>
						</div>
					</div>
				</div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-outline" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary btn-cron-custom-done" data-dismiss="modal">Done</button>
            </div>
        </div>
    </div>
</div>