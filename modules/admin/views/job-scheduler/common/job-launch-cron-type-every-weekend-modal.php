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
	$launch_condition_every_weekend = '';

	if($model->launch_condition && $model->launch_type == 'JobLaunchType.Cron') {
		$launch_condition_explode = explode(' ', $model->launch_condition);
		$launch_condition_every_weekend = $launch_condition_explode[1].':'.$launch_condition_explode[0];
	}
?>

<div class="modal fade" id="job-launch-cron-type-every-weekend-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?= Yii::t('app', 'Cron Type :: Every Weekend') ?></h4>
            </div>

            <div class="modal-body">
				<div class="form-group">
                    <label><?= Yii::t('app', 'Run') ?></label>

                    <?= Html::dropDownList('cron_every_weekend', '', array('every_weekend' => 'Every Weekend'), ['class' => 'form-control', 'id' => 'every_weekend']) ?>
                </div>

				<div class="form-group">
                    <label><?= Yii::t('app', 'Time') ?></label>

                    <div class='input-group date' id='cron_every_weekend_timepicker'>
						<?= Html::input('text', $launch_condition_every_weekend, 'cron_every_weekend_timepicker', ['class' => 'form-control cron_every_weekend_timepicker']) ?>

						<span class="input-group-addon">
							<span class="glyphicon glyphicon-calendar"></span>
						</span>
					</div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-outline" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary btn-cron-every-weekend-done" data-dismiss="modal">Done</button>
            </div>
        </div>
    </div>
</div>