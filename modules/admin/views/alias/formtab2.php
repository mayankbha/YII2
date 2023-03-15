<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 */

/* @var $this yii\web\View */
/* @var $model \app\modules\admin\models\forms\ListsForm */
use app\models\GetAliasList;
use yii\helpers\Url;
use app\models\GetAliasInfo;
use \yii\helpers\Html;
use yii\helpers\ArrayHelper;

if (($aliasList = GetAliasInfo::getData([],[],['field_out_list' => ['AliasDatabaseTable']])) && !empty($aliasList->list)) {
    $aliasList = $aliasList->list;
}
?>

<div class="border-form-block">

		<form id="w1" action="<?= Url::toRoute(['/admin/alias/api', 'id' => 'CreateAliasDependency']) ?>" method="post" name="w1">
		    <input type="hidden" name="_csrf_builder" value=
		    "<?= $request->getCsrfToken() ?>" />
		    <input id="aliasdependencyform-aliascode" class="pkValue" type="hidden" name="AliasDependencyForm[AliasCode]" value=
		    "" />

		    <div class="form-group field-aliasdependencyform-requesttable required "><!-- has-success -->
		      <label class="control-label" for="aliasdependencyform-requesttable">Primary Table</label>
                <?= Html::dropDownList('AliasDependencyForm[RequestTable]', null, ArrayHelper::map($aliasList, 'AliasDatabaseTable', 'AliasDatabaseTable'), [
                    'prompt' => '',
                    'id' => 'aliasdependencyform-requesttable',
                    'class' => 'form-control'])
                ?>
		      <div class="help-block"></div>
		    </div>

		    <div class="form-group field-aliasdependencyform-dependentson">
		      <label class="control-label" for="aliasdependencyform-dependentson">Dependents On</label>
                <?= \brussens\bootstrap\select\Widget::widget([
                    'name' => 'data_field',
                    'options' => [
                        'id' => 'aliasdependencyform-dependentson',
                        'class' => 'form-control',
                        'name' => 'AliasDependencyForm[DependentsOn]',
                        'data-live-search' => 'true'
                    ],
                    'items' => []
                ]) ?>
		    </div>

		    <div class="button-block">
		      <button type="button" id="add_dependency" tab="2" class="add-to-list atl-dependency btn btn-primary">Add to List</button>
		    </div>

	    	<fieldset class="border-form-block">
		        <legend>
		            <label class="control-label">List</label>
		        </legend>
	    		<div class="alias-subvalues-list" style='display: none'>
	    			<table width="100%" cellpadding="10" class="table table-hover">
	    				<thead>
	    					<tr>
	    						<th>Dependent</th>
	    						<th>&nbsp;</th>
	    					</tr>
	    				</thead>
	    				<tbody></tbody>
	    			</table>
	    		</div>
	    	</fieldset>

            <br />
		    <div class="button-block">
                <button type="button" id="group_dependencies_clear" tab="2" class="pull-left btn btn-outline">Clear</button>
		      <input type="hidden" id="customAPI" name="customAPI" value="1" />  
		      <button type="button" id="group_dependencies" tab="2" class="group-list btn btn-primary">Group Dependencies</button>
		    </div>
            <br />

	    	<fieldset class="border-form-block">
		        <legend>
		            <label class="control-label">List Groups</label>
		        </legend>
	    		<div class="alias-subvalues-list-group table-responsive" style='display: none'>
	    			<table width="100%" cellpadding="10" class="table table-hover">
	    				<thead>
	    					<tr>
	    						<th>Dependents On</th>
	    						<th>Primary table</th>
	    						<th>&nbsp;</th>
	    					</tr>
	    				</thead>
	    				<tbody>
	    					<?php
	    					if(isset($master_id)){
	    						$data = GetAliasList::jsonToArray(GetAliasList::callAPI("GetAliasDependencyList", $master_id, 1, 99999999, "", "AliasCode"));
				                $result = $data;
				                sort($result);
				                $r = 1;

				                foreach ($result as $row) {
				                	echo '<tr class=" fieldwrapper button-block" tab="1" elno='.$r.' id="field'.$r.'"><td class="col-xs-6"><input type="text" class="form-control" value="'.$row['DependentsOn'].'" name="AliasDependencyForm[DependentsOn][]" /></td><td><input type="text" class="form-control" name="AliasDependencyForm[RequestTable][]" value="'. $row['RequestTable'] . '" /></td><td class="col-xs-1"><input tab=1 type="button" class="remove btn btn-primary" value="-" onclick="syncRemoval($(this))"></td></tr>';
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
				<!-- <button type="button" id="remove_dependency_group" tab="2" class="remove-list-group btn btn-primary">Remove Dependency</button> -->
		    </div>

		  </form>

</div>
<p><br />&nbsp;</p>


<script type="text/javascript">
</script>

