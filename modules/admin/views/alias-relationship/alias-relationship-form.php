<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 */

/* @var $this yii\web\View */
/* @var $model \app\modules\admin\models\forms\AliasForm */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use app\models\GetAliasList;

$this->title = Yii::t('app', $data['action']);

?>

<h1><?= $this->title ?></h1>

<?php \app\components\ThemeHelper::printFlashes(); ?>

<div class="border-form-block">
	<?php $form = ActiveForm::begin() ?>
		<div class="form-group field-aliasrelationshipform-aliasparenttable required">
			<label class="control-label" for="aliasrelationshipform-aliasparenttable">Parent Table</label>

			<select id="aliasrelationshipform-aliasparenttable" class="form-control common-relationship-parent-table-class" name="AliasRelationshipForm[ParentTable]">
				<option value="0">Loading...</option>
			</select>

			<input type="hidden" id="aliasrelationshipform-aliasparenttable_stored" class="form-control" value="<?php if(isset($data['data'][0])) echo $data['data'][0]['ParentTable']; ?>" aria-required="true" />

			<div class="help-block"></div>
		</div>

		<div class="form-group field-aliasrelationshipform-aliasparentfield required">
			<label class="control-label" for="aliasrelationshipform-aliasparentfield">Parent Field</label>

			<select id="aliasrelationshipform-aliasparentfield" class="form-control" name="AliasRelationshipForm[ParentField]">
				<option value="0">Loading...</option>
			</select>

			<input type="hidden" id="aliasrelationshipform-aliasparentfield_stored" class="form-control" value="<?php if(isset($data['data'][0])) echo $data['data'][0]['ParentField']; ?>" aria-required="true" />

			<div class="help-block"></div>
		</div>

		<div class="form-group field-aliasrelationshipform-aliaschildtable required">
			<label class="control-label" for="aliasrelationshipform-aliaschildtable">Child Table</label>
			
			<select id="aliasrelationshipform-aliaschildtable" class="form-control common-relationship-parent-table-class" name="AliasRelationshipForm[ChildTable]">
				<option value="0">Loading...</option>
			</select>

			<input type="hidden" id="aliasrelationshipform-aliaschildtable_stored" class="form-control" value="<?php if(isset($data['data'][0])) echo $data['data'][0]['ChildTable']; ?>" aria-required="true" />

			<div class="help-block"></div>
		</div>

		<div class="form-group field-aliasrelationshipform-aliaschildfield required">
			<label class="control-label" for="aliasrelationshipform-aliaschildfield">Child Field</label>

			<select id="aliasrelationshipform-aliaschildfield" class="form-control" name="AliasRelationshipForm[ChildField]">
				<option value="0">Loading...</option>
			</select>

			<input type="hidden" id="aliasrelationshipform-aliaschildfield_stored" class="form-control" value="<?php if(isset($data['data'][0])) echo $data['data'][0]['ChildField']; ?>" aria-required="true" />

			<div class="help-block"></div>
		</div>

		<div class="button-block">
			<p>&nbsp;</p>

			<?= Html::a(Yii::t('app', 'Back'), Url::toRoute(['/admin/alias-relationship']), ['class' => 'btn btn-link']); ?>

			<?= Html::submitButton(Yii::t('app', 'Submit'), ['class' => 'btn btn-primary']); ?>
		</div>

		<input type="hidden" id="customAPI" name="customAPI" value="arm" />
	<?php ActiveForm::end() ?>
</div>

<p><br />&nbsp;</p>
