<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 */

/* @var $this yii\web\View */
/* @var $model \app\modules\admin\models\forms\ListsForm */
use app\models\GetAliasList;
use app\modules\admin\models\forms\AliasForm;
use yii\helpers\Html;
use yii\helpers\Url;
?>

<div class="border-form-block">
    
		<form id="w2" action="<?= Url::toRoute(['/admin/alias/api', 'id' => 'CreateAliasSecuritySpec']) ?>" method=
		  "post" name="w2">
		    <input type="hidden" name="_csrf_builder" value=
		    "<?= $request->getCsrfToken() ?>" /> 
		    <input id="aliassecurityspec-aliascode" class="pkValue" type="hidden" name="AliasSecuritySpecForm[AliasCode]" value=
		    "" /> 
		    <input id="aliassecurityspec-usertype" type="hidden" name="AliasSecuritySpecForm[UserType]" value=
		    "U" /> 

		    <div class="form-group field-aliassecurityspec-accounttype required "><!-- has-success -->
		      <label class="control-label" for="aliassecurityspec-accounttype">Account Type</label> 
		      <select id="aliassecurityspec-accounttype" class="form-control" name="AliasSecuritySpecForm[AccountType]"
		      aria-invalid="false">
		      	<option value="UserType.Internal_User">Internal User</option>
		      	<option value="UserType.External_User">External User</option>
		      </select>
		      <div class="help-block"></div>
		    </div>

		    <div class="form-group field-aliassecurityspec-tenant required "><!-- has-success -->
		      <label class="control-label" for="aliassecurityspec-tenant">Tenant</label> 
		      <select id="aliassecurityspec-tenant" class="form-control" name="AliasSecuritySpecForm[Tenant]"
		      aria-invalid="false">
		      	<option value="1">1</option>
		      	<option value="2">2</option>
		      	<option value="3">3</option>
		      </select>
		      <div class="help-block"></div>
		    </div>

		    <div class="form-group field-aliassecurityspec-securityfield required "><!-- has-success -->
		      <label class="control-label" for="aliassecurityspec-securityfield">Security Field</label> 
		      <select id="aliassecurityspec-securityfield" class="form-control" name="AliasSecuritySpecForm[SecurityField]"
		      aria-invalid="false">
		      	<option value="1">1</option>
		      	<option value="2">2</option>
		      </select>
		      <div class="help-block"></div>
		    </div>

		    <div class="button-block">
		      <input type="hidden" id="customAPI" name="customAPI" value="1" /> 
		      <button id="add_ass" type=
		      "button" tab="3" class="add-to-list btn btn-primary">Add to List</button>
		    </div>

	    	<fieldset class="border-form-block">
		        <legend>
		            <label class="control-label">List</label>
		        </legend> 
	    		<div class="alias-subvalues-list table-responsive">
	    			<table width="100%" cellpadding="10" class="table table-hover">
	    				<thead>
	    					<tr>
	    						<th>Account Type</th>
	    						<th>User Type</th>
	    						<th>Tenant</th>
	    						<th>Security Field</th>
	    						<th>&nbsp;</th>
	    					</tr>
	    				</thead>
	    				<tbody>
	    					<?php
	    					if(isset($master_id)){
	    						$data = GetAliasList::jsonToArray(GetAliasList::callAPI("GetAliasSecuritySpecList", $master_id, 1, 99999999, "", "AliasCode"));
				                $result = $data;
				                sort($result); 
				                $r = 1;
				                foreach ($result as $row) {
				                	echo '<tr class=" fieldwrapper button-block" elno='.$r.' id="field'.$r.'"><td class="col-xs-3"><input type="text" class="form-control" value="'.$row['AccountType'].'" name="AliasSecuritySpecForm[AccountType][]"></td><td class="col-xs-3"><input type="text" class="form-control" value="'.$row['UserType'].'" name="AliasSecuritySpecForm[UserType]"></td><td class="col-xs-3"><input type="text" class="form-control" value="'.$row['Tenant'].'" name="AliasSecuritySpecForm[Tenant]"></td><td class="col-xs-2"><input type="text" class="form-control" value="'.$row['SecurityField'].'" name="AliasSecuritySpecForm[SecurityField]"></td><td class="col-xs-1"><input tab=2 type="button" class="remove btn btn-primary" value="-" onclick="syncRemoval($(this))" /></td></tr>';
				                	$r++;
				                }
	    					}
	    					?>	    					
	    				</tbody>
	    			</table>
	    		</div>
	    	</fieldset>

			<div class="button-block">
				<p>&nbsp;</p>
				<?= Html::a(Yii::t('app', 'Back'), '#', ['class' => 'btn btn-link', 'onclick' => 'goBack("tab_Alias")']); ?>
		    </div>

		  </form>
</div>
<p><br />&nbsp;</p>';