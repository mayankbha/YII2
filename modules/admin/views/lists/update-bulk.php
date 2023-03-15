<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 *
 * @var $this yii\web\View
 * @var $model \app\modules\admin\models\forms\ListsForm
 * @var $models array
 */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$this->title = Yii::t('app', 'Update Bulk');

$count = sizeof($models);

$this->registerJs("
	var count = ".$count.";

    $(document)
        .on('click', '.add-list', function () {
			count = (count + 1);

            var wrapper = $('.list-wrapper'),
                formName = wrapper.attr('data-form-name'),
                iteration = parseInt(wrapper.attr('data-iteration')) + 1,
                newWrapper = $('<div />', {class: 'panel panel-default'}).html(wrapper.html());

            newWrapper.find('input, select, textarea').each(function () {
                var name = $(this).attr('name');
                $(this).attr('name', formName + '[' + count + '][' + name + ']');
            });
            
            wrapper.before(newWrapper);
            wrapper.attr('data-iteration', iteration);

			newWrapper.find('.panel-heading span:first').html('#'+count);
        })
        .on('click', '.remove-list-icon', function () {
            if (confirm('A you\'re a sure want to delete this list?')) {
                $(this).parent().parent().parent().remove();

				count = (count - 1);
            }
        }).on('click', '.ajax-remove-list-icon', function () {
            if (confirm('Are you sure want to delete this list?')) {
				var field_id = $(this).attr('data-info');
                var id = $(this).attr('id');
				var url = $(this).attr('data-url');

				$.ajax({
					type: 'POST',
					url: url,
					cache: false,
					data: {'id' : id},
					success: function(res) {
						//console.log('success :: '+id);

						if(res != '' && res == 0) {
							$('#list-row-'+field_id).remove();
						} else if(res != '' && res == 2) {
							alert('Invalid request! Please try again later.');
						} else {
							alert('Error occured! Please try again later.');
						}
					},
					error: function() {
						//console.log('error :: '+id);

						alert('Error occured! Please try again later.');
					}
				});
            }
        });
");
?>

<h1><?= $this->title ?></h1>

<?php \app\components\ThemeHelper::printFlashes(); ?>

<div class="border-form-block">
	<?php $form = ActiveForm::begin() ?>
		<?php if(!empty($models)) { ?>
			<?php $cnt = 1; foreach($models as $i => $model): ?>
				<div class="panel panel-default" id="list-row-<?php echo $cnt; ?>">
					<div class="panel-heading">
						<span class="text-left"><?php echo '#'.$cnt; ?></span>

						<span class="pull-right"><span class="glyphicon glyphicon-remove ajax-remove-list-icon" id="<?php echo $model->list_name.';'.urlencode($model->entry_name); ?>" data-info="<?php echo $cnt; ?>" data-url="<?php echo Url::to(['/admin/lists/delete-ajax'], true); ?>"></span></span>
					</div>

					<div class="panel-body">
						<div class="col-sm-6">
							<?= $form->field($model, 'list_name')->textInput(['name' => "{$model->formName()}[$i][list_name]"]); ?>
						</div>

						<div class="col-sm-6">
							<?= $form->field($model, 'description')->textInput(['name' => "{$model->formName()}[$i][description]"]); ?>
						</div>

						<div class="col-sm-6">
							<?= $form->field($model, 'entry_name')->textInput(['name' => "{$model->formName()}[$i][entry_name]"]); ?>
						</div>

						<div class="col-sm-6">
							<?= $form->field($model, 'groups')->textInput(['name' => "{$model->formName()}[$i][groups]"]); ?>
						</div>

						<div class="col-sm-6">
							<?= $form->field($model, 'weight')->textInput(['name' => "{$model->formName()}[$i][weight]"]); ?>
						</div>

						<div class="col-sm-6">
							<?= $form->field($model, 'note')->textInput(['name' => "{$model->formName()}[$i][note]"]); ?>
						</div>

						<div class="col-sm-6">
							<?= $form->field($model, 'products')->textInput(['name' => "{$model->formName()}[$i][products]"]); ?>
						</div>

						<div class="col-sm-6">
							<?= $form->field($model, 'restrict_code')->textInput(['name' => "{$model->formName()}[$i][restrict_code]"]); ?>
						</div>
					</div>
				</div>
			<?php $cnt++; endforeach; ?>
		<?php } else { ?>
			No records found!
		<?php } ?>

		<div class="panel panel-default list-wrapper" style="display: none;" data-form-name="<?= $model->formName() ?>" data-iteration="<?= $count ?>">
			<div class="panel-heading">
				<span class="text-left"><?php echo '#'.$count++; ?></span>

				<span class="pull-right"><span class="glyphicon glyphicon-remove remove-list-icon"></span></span>
			</div>

			<div class="panel-body">
				<div class="col-sm-6">
					<?= Html::label(Yii::t('app', 'List Name')) ?>
					<?= Html::textInput("list_name", null, ['class' => 'form-control']) ?>
				</div>

				<div class="col-sm-6">
					<?= Html::label(Yii::t('app', 'Description')) ?>
					<?= Html::textInput("description", null, ['class' => 'form-control']) ?>
				</div>

				<div class="col-sm-6">
					<?= Html::label(Yii::t('app', 'Entry Name')) ?>
					<?= Html::textInput("entry_name", null, ['class' => 'form-control']) ?>
				</div>

				<div class="col-sm-6">
					<?= Html::label(Yii::t('app', 'Groups')) ?>
					<?= Html::textInput("groups", null, ['class' => 'form-control']) ?>
				</div>

				<div class="col-sm-6">
					<?= Html::label(Yii::t('app', 'Weight')) ?>
					<?= Html::textInput("weight", 0, ['class' => 'form-control']) ?>
				</div>

				<div class="col-sm-6">
					<?= Html::label(Yii::t('app', 'Note')) ?>
					<?= Html::textInput("note", null, ['class' => 'form-control']) ?>
				</div>

				<div class="col-sm-6">
					<?= Html::label(Yii::t('app', 'Products')) ?>
					<?= Html::textInput("products", null, ['class' => 'form-control']) ?>
				</div>

				<div class="col-sm-6">
					<?= Html::label(Yii::t('app', 'Restrict Code')) ?>
					<?= Html::textInput("restrict_code", null, ['class' => 'form-control']) ?>
				</div>
			</div>
		</div>

		<div class="form-group pull-left">
			<?= Html::button(Yii::t('app', 'Add more'), ['class' => 'btn btn-default add-list']); ?>
		</div>

		<div class="button-block">
			<?= Html::a(Yii::t('app', 'Back'), Url::toRoute('index'), ['class' => 'btn btn-link']); ?>
			<?= Html::submitButton(Yii::t('app', 'Submit'), ['class' => 'btn btn-primary']); ?>
		</div>
	<?php ActiveForm::end() ?>
</div>
