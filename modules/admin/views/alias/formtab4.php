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
    
		<form id="w3" method="post" name="w3">
		    <input type="hidden" name="_csrf_builder" value=
		    "<?= $request->getCsrfToken() ?>" /> 
		    <input id="specialaccessrestrictionform-aliascode" class="pkValue" type="hidden" name="specialaccessrestrictionform[AliasCode]" value=
		    "" /> 

		    <div class="form-group field-specialaccessrestrictionform-entity required "><!-- has-error -->
		      <label class="control-label" for="specialaccessrestrictionform-entity">Entity</label>
		      <input type="text" id="specialaccessrestrictionform-entity" class="form-control" name=
		      "SpecialAccessRestrictionForm[Entity]" aria-required="true" aria-invalid="true" value="" />

		      <div class="help-block">
		        <!-- Alias Code cannot be blank. -->
		      </div>
		    </div>

		    <div class="form-group field-specialaccessrestrictionform-usergroupvalue required "><!-- has-success -->
		      <label class="control-label" for="specialaccessrestrictionform-usergroupvalue">User/Group/Value</label> 
		      <select id="specialaccessrestrictionform-usergroupvalue" class="form-control" name="SpecialAccessRestrictionForm[UserGroupValue]"
		      aria-invalid="false">
		      	<option value="U">U - User</option>
		      	<option value="G">G - Group</option>
		      	<option value="V">V - Value</option>
		      </select>
		      <div class="help-block"></div>
		    </div>

		    <div class="form-group field-specialaccessrestrictionform-rights required "><!-- has-success -->
		      <label class="control-label" for="specialaccessrestrictionform-rights">Rights</label> 
		      <select id="specialaccessrestrictionform-rights" class="form-control" name="SpecialAccessRestrictionForm[Rights]"
		      aria-invalid="false">
		      	<option value="N">N - No Access</option>
		      	<option value="R">R - Read Only</option>
		      	<option value="U">U - Update/All</option>
		      </select>
		      <div class="help-block"></div>
		    </div>

		    <div class="button-block">
		      <input type="hidden" id="customAPI" name="customAPI" value="1" /> 
		      <button id="add_sar" type="button" tab="4" class="add-to-list btn btn-primary">Add to List</button>
		    </div>

	    	<fieldset class="border-form-block">
		        <legend>
		            <label class="control-label">List</label>
		        </legend> 
	    		<div class="alias-subvalues-list table-responsive">
	    			<table width="100%" cellpadding="10" class="table table-hover">
	    				<thead>
	    					<tr>
	    						<th>Entity</th>
	    						<th>UserGroupValue</th>
	    						<th>Rights</th>
	    						<th>&nbsp;</th>
	    					</tr>
	    			</thead>
	    				<tbody>
	    					<?php
	    					if(isset($master_id)){
	    						$data = GetAliasList::jsonToArray(GetAliasList::callAPI("GetSpecialAccessRestrictionList", $master_id, 1, 99999999, "", "AliasCode"));
				                $result = $data;
				                sort($result); 
				                $r = 1;
				                foreach ($result as $row) {
				                	echo '<tr class=" fieldwrapper button-block" elno='.$r.' id="field'.$r.'"><td class="col-xs-4"><input type="text" class="form-control" value="'.$row['Entity'].'" name="SpecialAccessRestrictionForm[Entity][]"></td><td class="col-xs-4"><input type="text" class="form-control" value="'.$row['UserGroupValue'].'" name="SpecialAccessRestrictionForm[UserGroupValue]"></td><td class="col-xs-3"><input type="text" class="form-control" value="'.$row['Rights'].'" name="SpecialAccessRestrictionForm[Rights]"><input type="hidden" class="form-control" value="'.$row['Id'].'" name="SpecialAccessRestrictionForm[Id]"></td><td class="col-xs-1"><input tab=3 type="button" class="remove btn btn-primary" pk="'.$row['Id'].'" value="-" onclick="syncRemoval($(this))" /></td></tr>';
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