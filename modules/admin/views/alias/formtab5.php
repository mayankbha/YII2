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
    
		<form id="w4" method="post" name="w4">
		    <input type="hidden" name="_csrf_builder" value=
		    "<?= $request->getCsrfToken() ?>" /> 
		    <input id="aliasrestrictionform-aliascode" class="pkValue" type="hidden" name="AliasRestrictionForm[AliasCode]" value=
		    "" /> 

		    <div class="form-group field-aliasrestrictionform-entity required "><!-- has-error -->
		      <label class="control-label" for="aliasrestrictionform-entity">Entity</label>
		      <input type="text" id="aliasrestrictionform-entity" class="form-control" name=
		      "AliasRestrictionForm[Entity]" aria-required="true" aria-invalid="true" value="" />
		      <div class="help-block">
		        <!-- Alias Code cannot be blank. -->
		      </div>
		    </div>

		    <div class="form-group field-aliasrestrictionform-usergroup required "><!-- has-success -->
		      <label class="control-label" for="aliasrestrictionform-usergroup">User/Group/Value</label> 
		      <select id="aliasrestrictionform-usergroup" class="form-control" name="AliasRestrictionForm[UserGroup]"
		      aria-invalid="false">
		      	<option value="U">U - User</option>
		      	<option value="G">G - Group</option>
		      	<option value="V">V - Value</option>
		      </select>
		      <div class="help-block"></div>
		    </div>

		    <div class="form-group field-aliasrestrictionform-value required "><!-- has-error -->
		      <label class="control-label" for="aliasrestrictionform-value">Value</label>
		      <input type="text" id="aliasrestrictionform-value" class="form-control" name=
		      "AliasRestrictionForm[Value]" aria-required="true" aria-invalid="true" value="" />
		      <div class="help-block">
		        <!-- Alias Code cannot be blank. -->
		      </div>
		    </div>

		    <div class="form-group field-aliasrestrictionform-rights required "><!-- has-success -->
		      <label class="control-label" for="aliasrestrictionform-rights">Rights</label> 
		      <select id="aliasrestrictionform-rights" class="form-control" name="AliasRestrictionForm[Rights]"
		      aria-invalid="false">
		      	<option value="N">N - No Access</option>
		      	<option value="R">R - Read Only</option>
		      	<option value="U">U - Update/All</option>
		      </select>
		      <div class="help-block"></div>
		    </div>

		    <div class="button-block">
		      <input type="hidden" id="customAPI" name="customAPI" value="1" /> 
		      <button id="add_alias_restriction" type="button" tab="5" class="add-to-list btn btn-primary">Add to List</button>
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
	    						<th>UserGroup</th>
	    						<th>Value</th>
	    						<th>Rights</th>
	    						<th>&nbsp;</th>
	    					</tr>
	    				</thead>
	    				<tbody>
	    					<?php
	    					if(isset($master_id)){
	    						$data = GetAliasList::jsonToArray(GetAliasList::callAPI("GetAliasRestrictionList", $master_id, 1, 99999999, "", "AliasCode"));
				                $result = $data;
				                sort($result); 
				                $r = 1;
				                foreach ($result as $row) {
				                	echo '<tr class=" fieldwrapper button-block" elno='.$r.' id="field'.$r.'"><td class="col-xs-3"><input type="text" class="form-control" value="'.$row['Entity'].'" name="AliasRestrictionForm[Entity][]"></td><td class="col-xs-3"><input type="text" class="form-control" value="'.$row['UserGroup'].'" name="AliasRestrictionForm[UserGroup]"></td><td class="col-xs-3"><input type="text" class="form-control" value="'.$row['Value'].'" name="AliasRestrictionForm[Value]"></td><td class="col-xs-2"><input type="text" class="form-control" value="'.$row['Rights'].'" name="AliasRestrictionForm[Rights]"><input type="hidden" class="form-control" value="'.$row['Id'].'" name="AliasRestrictionForm[Id]"></td><td class="col-xs-1"><input tab=4 type="button" class="remove btn btn-primary" value="-" onclick="syncRemoval($(this))"></td></tr>';
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
<p><br />&nbsp;</p>