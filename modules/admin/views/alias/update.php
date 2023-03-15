<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 */

/* @var $this yii\web\View */
/* @var $model \app\modules\admin\models\forms\ListsForm */
use yii\bootstrap\Tabs;
use app\models\GetAliasList;
use app\modules\admin\models\forms\AliasForm;
use yii\helpers\Url;

$this->title = (Yii::$app->getRequest()->getQueryParam('action') == 'copy_alias' ? Yii::t('app', 'Copy Alias') : Yii::t('app', 'Update Alias'));

//echo "<pre>".print_r($model[0])."</pre>";exit;  

$tabs = []; 
$id = $model[0]['AliasCode']; 
?>


<h1><?= $this->title ?> - <?= $id ?></h1>



<?php \app\components\ThemeHelper::printFlashes(); ?>
<div class="alert-wrap"></div>

<?php

$readonly = (Yii::$app->getRequest()->getQueryParam('action') == 'copy_alias' ? '' : 'readonly');
$form_url = (Yii::$app->getRequest()->getQueryParam('action') == 'copy_alias' ? Url::toRoute(['/admin/alias/create']) : Url::toRoute(['/admin/alias/update', 'id' => $id, 'customAPI' => 1]));
$btn_text = (Yii::$app->getRequest()->getQueryParam('action') == 'copy_alias' ? 'Copy' : 'Update');

$tabs[0] = '<div class="border-form-block"> 
        <form id="w0" action="' . $form_url . '" class="update_form" method=
          "post" name="w0">
            <input type="hidden" name="_csrf_builder" value=
            "'.$request->getCsrfToken().'" /> 

            <div class="form-group field-aliasCode required "><!-- has-error -->
              <label class="control-label" for="aliasCode">'.AliasForm::attributeLabels()['AliasCode'] .'</label>
              <input type="text" id="aliasCode" class="form-control" name=
              "AliasForm[AliasCode]" aria-required="true" aria-invalid="true" value="'.$id.'" '.$readonly.' />

              <div class="help-block">
                <!-- Alias Code cannot be blank. -->
              </div>
            </div>

            <div class="form-group field-aliasform-aliastype required "><!-- has-success -->
              <label class="control-label" for="aliasform-aliastype">Type</label> 
              <select id="aliasform-aliastype" class="form-control" name="AliasForm[AliasType][]"
              aria-invalid="false">';
                foreach (GetAliasList::getAliasTypesDropdown() as $k => $v) {
                    $tabs[0] .= '<option value="'.$k.'" '.($k == $model[0]['AliasType'] ? 'selected' : '').'>
                      '.$v.'
                    </option>';
                }
              $tabs[0] .='</select>

              <div class="help-block"></div>
            </div>

            <div class="form-group field-aliasform-aliasdescription">
              <label class="control-label" for="aliasform-aliasdescription">Description</label> 
              <textarea id="aliasform-aliasdescription" class="form-control" name=
              "AliasForm[AliasDescription]">'. $model[0]['AliasDescription'] .'</textarea>

              <div class="help-block"></div>
            </div>

            <div class="form-group field-aliasform-aliasinfo">
              <label class="control-label" for="aliasform-aliasinfo">Info</label> 
              <textarea id="aliasform-aliasinfo" class="form-control" name=
              "AliasForm[AliasInfo]">'. $model[0]['AliasInfo'] .'</textarea>

              <div class="help-block"></div>
            </div>

            <div class="form-group field-aliasform-aliasformat">
              <label class="control-label" for="aliasform-aliasformat">Format</label>
              <input type="text" id="aliasform-aliasformat" class="form-control" name=
              "AliasForm[AliasFormat]" value="'. $model[0]['AliasFormat'] .'" />

              <div class="help-block"></div>
            </div>

            <div class="form-group field-aliasform-aliasformattype">
              <label class="control-label" for="aliasform-aliasformattype">Format Type</label>
              <input type="text" id="aliasform-aliasformattype" class="form-control" name=
              "AliasForm[AliasFormatType]" value="'. $model[0]['AliasFormatType'] .'" />

              <div class="help-block"></div>
            </div>

            <div class="form-group field-aliasform-aliasedits">
              <label class="control-label" for="aliasform-aliasedits">Edits</label> <input type=
              "text" id="aliasform-aliasedits" class="form-control" name="AliasForm[AliasEdits]"
              value="'. $model[0]['AliasEdits'] .'" />

              <div class="help-block"></div>
            </div>

            <div class="form-group field-aliasform-aliasdatabasetable required">
              <label class="control-label" for="aliasform-aliasdatabasetable">Database
              Table</label> <select id="aliasform-aliasdatabasetable" class="form-control" name="AliasForm[AliasDatabaseTable]"><option value="0">Loading...</option></select><input type="hidden" id="aliasform-aliasdatabasetable_stored" class=
              "form-control" value="'. $model[0]['AliasDatabaseTable'] .'" aria-required=
              "true" />

              <div class="help-block"></div>
            </div>

            <div class="form-group field-aliasform-aliasdatabasefield required">
              <label class="control-label" for="aliasform-aliasdatabasefield">Database
              Field</label> <select id="aliasform-aliasdatabasefield" class="form-control" name="AliasForm[AliasDatabaseField]"><option value="0">Loading...</option></select><input type="hidden" id="aliasform-aliasdatabasefield_stored" class=
              "form-control" value="'. $model[0]['AliasDatabaseField'] .'" aria-required=
              "true" />

              <div class="help-block"></div>
            </div>

            <div class="form-group field-aliasform-defalutgroupuserisnoaccess">
                <input type="hidden" name="AliasForm[DefalutGroupUserIsNoAccess]" value="'.$model[0]['DefalutGroupUserIsNoAccess'].'"><label><input type="checkbox" id="aliasform-defalutgroupuserisnoaccess" name="AliasForm[DefalutGroupUserIsNoAccess]" value="1" '. ($model[0]['DefalutGroupUserIsNoAccess'] == 'T' ? 'checked' : '') .'> Default Group/User is no access</label>

                <div class="help-block"></div>
            </div>

            <div class="form-group field-aliasform-defaultvalueisnoaccess">
                <input type="hidden" name="AliasForm[DefaultValueIsNoAccess]" value="'.$model[0]['DefaultValueIsNoAccess'].'"><label><input type="checkbox" id="aliasform-defaultvalueisnoaccess" name="AliasForm[DefaultValueIsNoAccess]" value="1" '. ($model[0]['DefaultValueIsNoAccess'] == 'T' ? 'checked' : '') .'> Default Value
              is no access</label>

                <div class="help-block"></div>
            </div>

            <fieldset class="border-form-block">
                <legend>
                    <label class="control-label">Evaluation</label>
                </legend> 
                <div class="form-group field-aliasform-aliasmodule">
                    <label class="control-label">Module Evaluation</label>
                    <input type="hidden" id="AliasForm_AliasModule" value="'.$model[0]['AliasModuleType'].'">
                    <div id="aliasform-aliasmodule" readonly="">
                        <label>
                            <input type="radio" name="AliasForm[AliasModule]" value="CPP" disabled="disabled"> C++ module
                        </label>
                        <label>
                            <input type="radio" name="AliasForm[AliasModule]" value="JS" disabled="disabled"> JavaScript module
                        </label>
                        <label>
                            <input type="radio" name="AliasForm[AliasModule]" value="PY" disabled="disabled"> Python module
                        </label>
                        <label>
                            <input type="radio" name="AliasForm[AliasModule]" value="" checked="" disabled="disabled"> False
                        </label>
                    </div>
                    <div class="help-block"></div>
                </div>      
                <div class="form-group field-aliasform-aliassqlstatement">
                    <label class="control-label" for="aliasform-aliassqlstatement">SQL Statement
                    </label>
                    <input type="hidden" value="'. $model[0]['AliasSQLStatement'] .'" />
                    <textarea id="aliasform-aliassqlstatement" class="form-control" name="AliasForm[AliasSQLStatement]" readonly="readonly" disabled="disabled">'. base64_decode($model[0]['AliasSQLStatement']) .'</textarea>
                    <div class="help-block"></div>
                </div>    
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
              <input type="hidden" id="customAPI" name="customAPI" value="1" /> 
              <a class="btn btn-link" href="' . Url::toRoute(['/admin/alias/index']) . '">Back</a> 
              <button type="submit" class="btn btn-primary">'.$btn_text.'</button>
            </div>
          </form>
</div>
<p><br />&nbsp;</p>';


$tabs[1] = $this->render('formtab2', ['request' => $request, 'master_id' => $id]);
$tabs[2] = $this->render('formtab3', ['request' => $request, 'master_id' => $id]);
$tabs[3] = $this->render('formtab4', ['request' => $request, 'master_id' => $id]);
$tabs[4] = $this->render('formtab5', ['request' => $request, 'master_id' => $id]);

$tabTypesArr = AliasForm::tabLabels(); 
$t = 0;  
foreach ($tabTypesArr as $key => $value) {
    $tabTypes[] = [
            'label' => $value, 
            'content' => $tabs[$t],
            'options' => ['id' => 'tab_'.$key, 'key' => $key],
            //'active' => ($t==0?true:false)
        ];
    $t++;
}

echo Tabs::widget([
    'items' => $tabTypes,
]);

?>