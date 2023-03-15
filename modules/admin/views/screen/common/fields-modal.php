<div class="modal fade" id="fields-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form class="fields-constructor-form" action="#" method="post">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title field-configure-block">
                        <?= Yii::t('app', 'Field settings') ?>
                    </h4>
                    <h4 class="modal-title field-button-block">
                        <?= Yii::t('app', 'Button settings') ?>
                    </h4>
                    <h4 class="modal-title field-label-block">
                        <?= Yii::t('app', 'Label settings') ?>
                    </h4>
                </div>
                <div class="modal-body">
                    <ul class="nav nav-tabs" role="tablist">
                        <li role="presentation" class="active"><a href="#primary-field-configuration" aria-controls="primary-field-configuration" role="tab" data-toggle="tab">Primary</a></li>
                        <li role="presentation"><a href="#notifications-field-configuration" aria-controls="notifications-field-configuration" role="tab" data-toggle="tab">Notifications</a></li>
                    </ul>
                    <div class="tab-content">
                        <div role="tabpanel" class="tab-pane active" id="primary-field-configuration">
                            <div class="row">
                                <div class="col-sm-6 form-group field-configure-block">
                                    <label for="field-data-field"><?= Yii::t('app','Data field')?></label>
                                    <?= \brussens\bootstrap\select\Widget::widget([
                                        'name' => 'data_field',
                                        'options' => [
                                            'id' => 'field-data-field',
                                            'data-live-search' => 'true'
                                        ],
                                        'items' => []
                                    ]) ?>
                                </div>

                                <div class="col-sm-6 form-group field-configure-block field-label-block">
                                    <label for="field-label"><?= Yii::t('app','Label') ?> <sup style="color: grey"><?= Yii::t('app','Default value')?></sup></label>
                                    <div class="input-group" data-target="internationalization-tooltip">
                                        <input id="field-label" type="text" class="form-control" name="field_label" value="">
                                        <span class="input-group-btn">
                                            <button class="btn btn-default"
                                                    data-toggle="modal"
                                                    data-target="#internationalization-modal"
                                                    data-internationalization="#field-label"
                                                    type="button">
                                               <?= Yii::t('app','Internationalization') ?>
                                            </button>
                                        </span>
                                    </div>
                                </div>

                                <div class="col-sm-3 form-group field-configure-block">
                                    <label for="field-type"><?= Yii::t('app','Type') ?></label>
                                    <div>
                                        <select id="field-type" class="form-control" name="field_type" required>
                                            <option value=""><?= Yii::t('app','-- Select --')?></option>
                                        </select>
                                        <span class="input-group-btn pull-up-btn-block" style="display: none;">
                                            <button type="button" data-toggle="modal" data-target="#pullup-modal" class="btn btn-default"><?= Yii::t('app','Pull up') ?></button>
                                        </span>
                                    </div>
                                </div>

                                <div class="col-sm-3 form-group field-configure-block hidden dependent-field-configure-block">
                                    <label for="id-dependent-field"><?= Yii::t('app','Id of dependent field') ?></label>
                                    <div>
                                        <input id="field-id-dependent-field" type="text" class="form-control" name="id-dependent-field" value="">
                                    </div>
                                </div>

                                <div class="col-sm-6 form-group field-configure-block hidden dependent-field-configure-block">
                                    <label for="field-type"><?= Yii::t('app','Lead time') ?></label>
                                    <div>
                                        <input type="text" class="form-control" id="duration">
                                    </div>
                                </div>

                                <div class=" col-sm-6 form-group field-configure-block">
                                    <label for="field-label-orientation"><?= Yii::t('app','Label orientation' )?></label>
                                    <select id="field-label-orientation" class="form-control" name="label_orientation" required>
                                        <option value="LEFT"><?= Yii::t('app','Left')?></option>
                                        <option value="TOP"><?= Yii::t('app','Top')?></option>
                                    </select>
                                </div>

                                <div class="col-sm-6 form-group fields-modal-format-type-block field-configure-block">
                                    <label for="field-format-type"><?= Yii::t('app','Format type')?></label>
                                    <select id="field-format-type" class="form-control" name="format_type">
                                        <option value=""><?= Yii::t('app','-- Select --')?></option>
                                    </select>
                                </div>

                                <div class="col-sm-6 form-group field-configure-block">
                                    <label for="field-length"><?= Yii::t('app','Max Length') ?></label>
                                    <input id="field-length" type="number" class="form-control" min="1" name="field_length" required/>
                                </div>

                                <div class="col-sm-6 form-group field-configure-block">
                                    <label for="field-num-rows"><?= Yii::t('app','Num Rows') ?></label>
                                    <input id="field-num-rows" type="number" class="form-control" name="num_rows" min="1" disabled required/>
                                </div>

                                <div class="col-sm-6 form-group field-configure-block">
                                    <label for="field-width-value"><?= Yii::t('app','Field width')?></label>
                                    <div class="form-inline">
                                        <select id="field-width-type" class="form-control" name="field_width_type">
                                            <option value="" selected><?= Yii::t('app','Default')?></option>
                                            <option value="V"><?= Yii::t('app','By value')?></option>
                                            <option value="L"><?= Yii::t('app','By length')?></option>
                                        </select>
                                        <input id="field-width-value" type="text" name="field_width_value" class="form-control" />
                                    </div>
                                </div>

                                <div class="col-sm-6 form-group field-button-block">
                                    <label for="button-value"><?= Yii::t('app','Value')?></label>
                                    <input id="button-value" type="text" class="form-control" name="value"/>
                                </div>

                                <div class="col-sm-6 form-group field-button-block">
                                    <label for="button-action"><?= Yii::t('app','Button action')?></label>
                                    <select id="button-action" class="form-control" name="button_action">
                                        <option value="" selected><?= Yii::t('app','Default')?></option>
                                        <optgroup label="Action">
                                            <option value="insert"><?= Yii::t('app','Insert')?></option>
                                            <option value="key"><?= Yii::t('app','Inquire')?></option>
                                            <option value="prev-search"><?= Yii::t('app','Previous search result')?></option>
                                            <option value="next-search"><?= Yii::t('app','Next search result')?></option>
                                            <option value="edit"><?= Yii::t('app','Edit')?></option>
                                            <option value="copy"><?= Yii::t('app','Copy')?></option>
                                            <option value="delete"><?= Yii::t('app','Delete')?></option>
                                            <option value="execute"><?= Yii::t('app','Execute')?></option>
                                        </optgroup>
                                        <optgroup label="Stepper">
                                            <option value="prev-step"><?= Yii::t('app','Previous step')?></option>
                                            <option value="next-step"><?= Yii::t('app','Next step')?></option>
                                        </optgroup>
                                    </select>
                                </div>

                                <div class="col-sm-6 form-group field-button-block">
                                    <label for="button-identifier"><?= Yii::t('app','Custom #ID')?></label>
                                    <input id="button-identifier" type="text" class="form-control" name="identifier" pattern="[A-Za-z0-9]+" title="Only letters and numerals" required/>
                                </div>

                                <div class="col-sm-6 form-group field-configure-block field-label-block">
                                    <label for="field-tooltip"><?= Yii::t('app','Tooltip')?> <sup style="color: grey"><?= Yii::t('app','Default value')?></sup></label>
                                    <div class="input-group" data-target="internationalization-tooltip">
                                        <input id="field-tooltip" type="text" class="form-control" name="field_tooltip" />
                                        <span class="input-group-btn">
                                            <button class="btn btn-default"
                                                    data-toggle="modal"
                                                    data-target="#internationalization-modal"
                                                    data-internationalization="#field-tooltip"
                                                    type="button">
                                                    <?= Yii::t('app','Internationalization') ?>
                                            </button>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6 form-group">
                                    <span class="field-configure-block">
                                        <input type="checkbox" id="field-key-field" value="Y" name="key_field" />
                                        <label for="field-key-field"><?= Yii::t('app','Key field')?></label>
                                    </span>
                                    <br />
                                    <span class="field-configure-block">
                                        <input type="checkbox" id="field-copyable-field" value="Y" name="copyable_field">
                                        <label for="field-copyable-field"><?= Yii::t('app','Copyable field')?></label>
                                    </span>
                                    <br />
                                    <span class="field-configure-block">
                                        <input type="checkbox" id="field-edit-type" value="R" name="edit_type">
                                        <label for="field-edit-type"><?= Yii::t('app','Read only')?></label>
                                    </span>
                                </div>
                                <div class="col-sm-6 form-group">
                                    <span class="field-configure-block">
                                        <input type="checkbox" id="field-apply-input-mask" value="Y" name="apply_input_mask">
                                        <label for="field-apply-input-mask"><?= Yii::t('app','Apply input mask')?></label>
                                    </span>
                                    <br />
                                    <span class="field-configure-block">
                                        <input type="checkbox" id="field-always-show" value="Y" name="always_show_field_border" />
                                        <label for="field-key-field"><?= Yii::t('app','Always show field border')?></label>
                                    </span>
                                </div>
                            </div>

                            <input id="field-label-width" type="hidden" class="form-control" name="label_width" />
                            <input id="field-link-column" type="hidden" class="form-control" name="link_column" value=""/>
                            <input id="field-list-name" type="hidden" class="form-control" name="list_name" disabled required/>
                            <input id="field-block-width" type="hidden" class="form-control" name="block_width" value="12" />
                            <input id="field-block-height" type="hidden" class="form-control" name="block_height" value="2" />
                            <input id="field-block-row" type="hidden" class="form-control" name="block_row" />
                            <input id="field-block-col" type="hidden" class="form-control" name="block_col" value="0" />

                            <div class="row additional-fields-row">
                                <div class="fields-modal-type-button-execute clearfix">
                                    <div class="col-sm-4">
                                        <input type="hidden" name="execute_function_pre" class="execute-btn extensions-config extensions-config-execute-pre" disabled/>
                                        <button class="btn btn-default form-control extension-settings-button" data-toggle="modal" data-target="#extensions-modal" type="button" data-type="extension" data-func-type="GetList" data-extension-method="pre" data-extensions="extensions-config-execute-pre">
                                            <span class="glyphicon glyphicon-cog"></span> PRE FUNCTION
                                        </button>
                                    </div>
                                    <div class="col-sm-4">
                                        <input type="hidden" name="execute_function_get" class="execute-library-input" disabled/>
                                        <input type="hidden" name="execute_function_get" class="execute-function-input" disabled/>
                                        <input type="hidden" name="execute_function_custom" class="execute-custom-input" disabled/>
                                        <button class="btn btn-default form-control extension-function-btn" data-toggle="modal" data-target="#execute-function-modal" type="button">
                                            <span class="glyphicon glyphicon-cog"></span> EXECUTE FUNCTION
                                        </button>
                                    </div>
                                    <div class="col-sm-4">
                                        <input type="hidden" name="execute_function_post" class="execute-btn extensions-config extensions-config-execute-post" disabled />
                                        <button class="btn btn-default form-control extension-settings-button" data-toggle="modal" data-target="#extensions-modal" type="button" data-type="extension" data-func-type="GetList" data-extension-method="post" data-extensions="extensions-config-execute-post">
                                            <span class="glyphicon glyphicon-cog"></span> POST FUNCTION
                                        </button>
                                    </div>
                                </div>
                                <div class="fields-modal-type-link clearfix">
                                    <div class="col-sm-6 form-group">
                                        <label><?= Yii::t('app','Type link') ?></label>
                                        <select class="type-link form-control" name="type-link" required disabled>
                                            <option value="_blank"><?= Yii::t('app','Blank')?></option>
                                            <option value="_self"><?= Yii::t('app','Self')?></option>
                                        </select>
                                    </div>
                                </div>
                                <div class="field-dropdown-values clearfix">
                                    <div class="col-sm-6 form-group">
                                        <label for="dropdown-values-input"><?= Yii::t('app', 'Values (Use the delimiter ";")') ?></label>
                                        <input type="text" id="dropdown-values-input" class="form-control" name="dropdown_values" pattern="[\w|\-|\d]+(;[\w|\-|\d]+)*" title="<?= Yii::t('app', "Only letters and numbers. Use the delimiter ';' for drop-down cards") ?>" required disabled />
                                    </div>
                                </div>
                                <div class="external-link-block clearfix">
                                    <div class="clearfix">
                                        <div class="col-sm-6 form-group">
                                            <label><?= Yii::t('app','Menu')?></label>
                                            <select class="form-control field-link-menu" data-external-link="0" name="field_link_menu" data-serial-number="1" disabled required>
                                                <option value=""><?= Yii::t('app','-- Select --')?></option>
                                            </select>
                                        </div>
                                        <div class="col-sm-6 form-group">
                                            <label><?= Yii::t('app','Group Screen')?></label>
                                            <select class="form-control field-group-screen-link" data-external-link="1" name="field_group_screen_link" data-serial-number="2" disabled required>
                                                <option> <?= Yii::t('app','-- Select --')?></option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="clearfix">
                                        <div class="col-sm-6 form-group">
                                            <label><?= Yii::t('app','Screen')?></label>
                                            <select class="form-control field-screen-link" data-external-link="2" name="field_screen_link" data-serial-number="3" disabled required>
                                                <option value=""><?= Yii::t('app','-- Select --')?></option>
                                            </select>
                                        </div>
                                        <div class="col-sm-6 form-group">
                                            <label><?= Yii::t('app','Parameter')?></label>
                                            <select class="form-control field-settings-link" data-external-link="3" name="field_settings_link" data-serial-number="4" disabled required>
                                                <option value=""><?= Yii::t('app','-- Select --')?></option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6 form-group fields-modal-group-block">
                                    <label for="field-group"><?= Yii::t('app','Group name')?></label>
                                    <input id="field-group" type="text" class="form-control" name="field_group" list="groups_list" autocomplete="off"/>
                                    <datalist id="groups_list"></datalist>
                                </div>
                                <?php if (($customQueries = \app\modules\admin\models\CustomQuery::getData()) && !empty($customQueries->list)): ?>
                                    <div class="fields-modal-custom-query">
                                        <div class="col-sm-6 form-group">
                                            <label><?= Yii::t('app','Custom query')?></label>
                                            <select name="custom_query_pk" class="form-control field-custom-query-pk" disabled required>
                                                <option value=""></option>
                                                <?php foreach($customQueries->list as $queryData): ?>
                                                    <option value="<?= $queryData['pk'] ?>">
                                                        <?= $queryData['query_name'] . (($queryData['description']) ? ' - ' . $queryData['description'] : '') ?>
                                                    </option>
                                                <?php endforeach ?>
                                            </select>
                                        </div>
                                        <div class="col-sm-6 form-group">
                                            <label>&nbsp;</label>
                                            <?php foreach($customQueries->list as $queryData): ?>
                                                <?php if (!empty($queryData['query_params'])): ?>
                                                    <select  style="display: none" name="custom_query_param" class="field-custom-query-param form-control" data-pk="<?= $queryData['pk'] ?>" disabled required>
                                                        <?php foreach(explode(', ', $queryData['query_params']) as $param): ?>
                                                            <option value="<?= $param ?>">
                                                                <?= $param ?>
                                                            </option>
                                                        <?php endforeach ?>
                                                    </select>
                                                <?php endif ?>
                                            <?php endforeach ?>
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>
                                <?php endif ?>
                                <?php if ($families = \app\modules\admin\models\DocumentFamily::getDistinctFamilies()): ?>
                                    <div class="fields-modal-document-group-block">
                                        <div class="col-sm-6 form-group">
                                            <label for="field-document-family"><?= Yii::t('app','Document family')?></label>
                                            <select id="field-document-family" name="field_document_family" class="form-control" disabled required>
                                                <option value=""></option>
                                                <?php foreach($families as $familyName => $familyData): ?>
                                                    <option value="<?= $familyName ?>">
                                                        <?= $familyData['family_name'] . (($familyData['family_description']) ? ' - ' . $familyData['family_description'] : '') ?>
                                                    </option>
                                                <?php endforeach ?>
                                            </select>
                                        </div>
                                        <div class="col-sm-6 form-group">
                                            <label style="display: none"><?= Yii::t('app','Document category')?></label>
                                            <?php foreach($families as $familyName => $familyData): ?>
                                                <?php if (!empty($familyData['categories'])): ?>
                                                    <select  style="display: none" name="field_document_category" class="field-document-category form-control" data-family="<?= $familyName ?>" disabled required>
                                                        <?php foreach($familyData['categories'] as $categoryName => $categoryData): ?>
                                                            <option value="<?= $categoryName ?>"><?= $categoryData['category'] . (($categoryData['category_description']) ? ' - ' . $categoryData['category_description'] : '') ?></option>
                                                        <?php endforeach ?>
                                                    </select>
                                                <?php endif ?>
                                            <?php endforeach ?>
                                        </div>

                                        <div class="col-sm-12 form-group">
                                            <table class="table table-bordered">
                                                <tr>
                                                    <th></th>
                                                    <th><?= Yii::t('app','Screen field')?></th>
                                                    <th><?= Yii::t('app','Key field')?></th>
                                                </tr>
                                                <tr>
                                                    <td><input type="checkbox" disabled></td>
                                                    <td><input type="text" class="form-control" /></td>
                                                    <td><?= Yii::t('app','Key')?>1</td>
                                                </tr>
                                                <tr>
                                                    <td><input type="checkbox" disabled></td>
                                                    <td><input type="text" class="form-control" /></td>
                                                    <td><?= Yii::t('app','Key')?>2</td>
                                                </tr>
                                                <tr>
                                                    <td><input type="checkbox" disabled></td>
                                                    <td><input type="text" class="form-control" /></td>
                                                    <td><?= Yii::t('app','Key')?>3</td>
                                                </tr>
                                                <tr>
                                                    <td><input type="checkbox" disabled></td>
                                                    <td><input type="text" class="form-control" /></td>
                                                    <td><?= Yii::t('app','Key')?>4</td>
                                                </tr>
                                                <tr>
                                                    <td><input type="checkbox" disabled></td>
                                                    <td><input type="text" class="form-control" /></td>
                                                    <td><?= Yii::t('app','Key')?>5</td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                <?php endif ?>
                            </div>
                        </div>
                        <div role="tabpanel" class="tab-pane" id="notifications-field-configuration">
                            <button type="button" class="btn btn-outline btn-notification-add">Add notification</button>
                            <br />
                            <br />
                            <div class="notification-block-primary">
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <label>Notification</label>
                                            <select class="form-control notification-field-pk" name="notification_pk[]">
                                                <?php if (($notifications = \app\modules\admin\models\Notification::getData()) && !empty($notifications->list)): ?>
                                                    <option value=""></option>
                                                    <?php foreach($notifications->list as $notificationItem): ?>
                                                        <?= \yii\helpers\Html::tag('option', $notificationItem['notify_name'], [
                                                            'value' => $notificationItem['notify_name'],
                                                            'data-parameters' => empty($notificationItem['params']) ? [] : json_encode(array_map('trim', explode(';', $notificationItem['params'])))
                                                        ]) ?>
                                                    <?php endforeach ?>
                                                <?php else: ?>
                                                    <option value="">-- Empty --</option>
                                                <?php endif ?>
                                            </select>
                                        </div>
                                        <div class="col-sm-6">
                                            <label>Action</label>
                                            <select class="form-control notification-field-action">
                                                <option value="edit">Update</option>
                                                <option value="insert">Insert</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="notification-block-parameter"></div>
                                <hr />
                            </div>
                            <div class="notification-block-exist">

                            </div>
                            <br />
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="text-left" id="id-field">#<span>--</span></div>
                        </div>
                        <div class="col-sm-8">
                            <button type="button" class="btn btn-outline" data-dismiss="modal"><?= Yii::t('app','Close')?></button>
                            <button type="button" data-toggle="modal" data-target="#js-edit-modal" class="btn btn-warning js-edit-btn" onclick='fieldsConstructor.refreshCodeMirror(200)'><?= Yii::t('app','Edit JavaScript')?></button>
                            <button type="button" data-toggle="modal" data-target="#formatting-modal" class="btn btn-success"><?= Yii::t('app','Formatting')?></button>
                            <button type="button" data-toggle="modal" data-target="#access-modal" class="btn btn-danger"><?= Yii::t('app','Access rights')?></button>
                            <button type="submit" class="btn btn-primary btn-save-fields"><?= Yii::t('app','Add')?></button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>