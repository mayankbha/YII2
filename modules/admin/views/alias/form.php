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
?>

<?php $form = ActiveForm::begin() ?>
<?php
/*echo "<pre>";
//print_r($form->field($model, 'AliasType'));
echo $_SESSION['screenData']['sessionData']['sessionhandle']."<br />".
     $_SESSION['screenData']["app\models\UserAccount"]->tenant_code."<br />".
     $_SESSION['screenData']["app\models\UserAccount"]->document_group."<br />".
     $_SESSION['screenData']["app\models\UserAccount"]->group_area."<br />".
     $_SESSION['screenData']["app\models\UserAccount"]->account_name."<br />".
     $_SESSION['screenData']["app\models\UserAccount"]->account_type."<br />".
    "</pre>"; */
$AliasModuleChoices = ['' => 'False', 'CPP' => 'C++ module', 'JS' => 'JavaScript module', 'PY' => 'Python module'];
$model->AliasCode = 'Alias.';
$model->AliasModule = '';
?>
    <?= $form->field($model, 'AliasCode')->textInput(['id'=> 'aliasCode', 'readonly'=> true]); ?>
    <?= $form->field($model, 'AliasType[]')->dropDownList(
            GetAliasList::getAliasTypesDropdown()
    ); ?>
	<?= $form->field($model, 'AliasDescription')->textInput(); ?>
	<?= $form->field($model, 'AliasInfo')->textarea(); ?>
	<?= $form->field($model, 'AliasFormat')->input('text'); ?>
	<?= $form->field($model, 'AliasFormatType')->textInput(['readonly'=> true]); ?>
	<?= $form->field($model, 'AliasEdits')->input('text'); ?>
	<?= $form->field($model, 'AliasDatabaseTable')->dropDownList( ["Loading..."]); ?>
	<?= $form->field($model, 'AliasDatabaseField')->dropDownList( ["Loading..."]); ?>
    <?= $form->field($model,'DefalutGroupUserIsNoAccess')->checkBox(); ?>
    <?= $form->field($model,'DefaultValueIsNoAccess')->checkBox(); ?>
    <fieldset class="border-form-block">
        <legend>
            <label class="control-label">Evaluation</label>
        </legend>
    	<?= $form->field($model,'AliasModule')->radioList($AliasModuleChoices, ['readonly'=> true]); ?>
    	<?= $form->field($model, 'AliasSQLStatement')->textarea(['readonly'=> true]); ?>
    </fieldset>

    <div class="hidden alias-sub-details aliasdependencyform-dependentson">
        <table width="100%">
                <tbody></tbody>
        </table>
    </div>

    <div class="hidden alias-sub-details aliasdependencyform-requesttable">
        <table width="100%">
            <tbody></tbody>
        </table>
    </div>

    <div class="hidden alias-sub-details aliasdependencyform-method">
        <table width="100%">
                <tbody></tbody>
        </table>
    </div>

    <div class="hidden alias-sub-details aliassecurityspec-usertype">
        <table width="100%">
                <tbody></tbody>
        </table>
    </div>

    <div class="hidden alias-sub-details aliassecurityspec-accounttype">
        <table width="100%">
                <tbody></tbody>
        </table>
    </div>

    <div class="hidden alias-sub-details aliassecurityspec-tenant">
        <table width="100%">
                <tbody></tbody>
        </table>
    </div>

    <div class="hidden alias-sub-details aliassecurityspec-securityfield">
        <table width="100%">
                <tbody></tbody>
        </table>
    </div>

    <div class="hidden alias-sub-details aliassecurityspec-method">
        <table width="100%">
                <tbody></tbody>
        </table>
    </div>

    <div class="hidden alias-sub-details specialaccessrestrictionform-entity">
        <table width="100%">
                <tbody></tbody>
        </table>
    </div>

    <div class="hidden alias-sub-details specialaccessrestrictionform-usergroupvalue">
        <table width="100%">
                <tbody></tbody>
        </table>
    </div>

    <div class="hidden alias-sub-details specialaccessrestrictionform-rights">
        <table width="100%">
                <tbody></tbody>
        </table>
    </div>

    <div class="hidden alias-sub-details specialaccessrestrictionform-id">
        <table width="100%">
                <tbody></tbody>
        </table>
    </div>

    <div class="hidden alias-sub-details specialaccessrestrictionform-method">
        <table width="100%">
                <tbody></tbody>
        </table>
    </div>

    <div class="hidden alias-sub-details aliasrestrictionform-entity">
        <table width="100%">
                <tbody></tbody>
        </table>
    </div>

    <div class="hidden alias-sub-details aliasrestrictionform-value">
        <table width="100%">
                <tbody></tbody>
        </table>
    </div>

    <div class="hidden alias-sub-details aliasrestrictionform-usergroup">
        <table width="100%">
                <tbody></tbody>
        </table>
    </div>

    <div class="hidden alias-sub-details aliasrestrictionform-rights">
        <table width="100%">
                <tbody></tbody>
        </table>
    </div>

    <div class="hidden alias-sub-details aliasrestrictionform-id">
        <table width="100%">
                <tbody></tbody>
        </table>
    </div>

    <div class="hidden alias-sub-details aliasrestrictionform-method">
        <table width="100%">
                <tbody></tbody>
        </table>
    </div>

    <div class="button-block">
        <p>&nbsp;</p>
        <input type="hidden" id="customAPI" name="customAPI" value="1">
        <?= Html::a(Yii::t('app', 'Back'), Url::toRoute('index'), ['class' => 'btn btn-link']); ?>
        <?= Html::submitButton(Yii::t('app', 'Submit'), ['class' => 'btn btn-primary']); ?>
    </div>


<?php ActiveForm::end() ?>
