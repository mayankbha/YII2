<div class="modal fade" id="table-modal" tabindex="-1" role="dialog" aria-labelledby="tableModal">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form class="table-constructor-form" action="#" method="post">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title"><?= Yii::t('app', 'Config column') ?></h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <!--<div class="col-sm-6 form-group">
                            <label for="table-label-align">Label align</label>
                            <select id="table-label-align" class="form-control" name="label_align" required>
                                <option value="LEFT">Left</option>
                                <option value="RIGHT">Right</option>
                                <option value="CENTER">Center</option>
                            </select>
                        </div>-->

                        <!--<div class="col-sm-6 form-group">
                            <label for="table-sorting-column">Sorting</label>
                            <select id="table-sorting-column" class="form-control" name="sorting_column">
                                <option value="">No</option>
                                <option value="Y">Yes</option>
                            </select>
                        </div>-->

                        <!--<div class=" col-sm-6 form-group">
                            <label for="table-filter-column">Filter</label>
                            <select id="table-filter-column" class="form-control" name="filter_column">
                                <option value="">No</option>
                                <option value="Y">Yes</option>
                            </select>
                        </div>-->

                        <!--<div class="col-sm-6 form-group">
                            <label for="table-default-filter-value">Default filter value</label>
                            <input id="table-default-filter-value" type="text" class="form-control" name="default_filter_value" disabled />
                        </div>-->

                        <div class="col-sm-6 form-group">
                            <label for="table-param-type">Param type</label>
                            <div>
                                <select id="table-param-type" class="form-control" name="param_type" required>
                                    <option value="">-- SELECT --</option>
                                </select>
                                <span class="input-group-btn pull-up-btn-table" style="display: none;">
                                    <button type="button" data-toggle="modal" data-target="#pullup-modal" class="btn btn-default"><?= Yii::t('app','Pull up') ?></button>
                                </span>
                            </div>
                        </div>

                        <div class="col-sm-6 form-group">
                            <label for="table-format-type">Format type</label>
                            <select id="table-format-type" class="form-control" name="format_type">
                                <option value="">-- EMPTY --</option>
                            </select>
                        </div>

                        <div class="col-sm-6 form-group">
                            <label for="field-width-value-table">Field width</label>
                            <input id="field-width-type-table" type="hidden" name="field_width_type" value="V">
                            <input id="field-width-value-table" type="text" name="field_width_value" placeholder="100px or 100% or 1em" class="form-control" />
                        </div>

                        <!--<div class="col-sm-6 form-group">
                            <label for="table-show-column">Show column</label>
                            <select id="table-show-column" class="form-control" name="show_column">
                                <option value="">No</option>
                                <option value="Y">Yes</option>
                            </select>
                        </div>-->

                        <input id="table-link-column" type="hidden" name="link_column" value="" />
                        <input id="table-list-name" type="hidden" name="list_name" disabled required />

                        <div class="col-sm-12 table-additional-fields-row">
                            <?php if (($customQueries = \app\modules\admin\models\CustomQuery::getData()) && !empty($customQueries->list)): ?>
                                <div class="row fields-modal-custom-query">
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
                            <div class="row fields-modal-type-button-execute" style="margin-bottom: 15px">
                                <div class="col-sm-6">
                                    <label>Value</label>
                                    <input type="text" name="value" class="form-control" disabled/>
                                    <input id="table-button-action" type="hidden" name="button_action" value="execute" disabled />
                                </div>
                            </div>
                            <div class="row fields-modal-type-button-execute">
                                <div class="col-sm-4">
                                    <input type="hidden" name="execute_function_pre" class="execute-btn extensions-config extensions-table-config-execute-pre" disabled/>
                                    <button class="btn btn-default form-control extension-settings-button" data-toggle="modal" data-target="#extensions-modal" type="button" data-type="extension" data-func-type="GetList" data-extension-method="pre" data-extensions="extensions-table-config-execute-pre">
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
                                    <input type="hidden" name="execute_function_post" class="execute-btn extensions-config extensions-table-config-execute-post" disabled />
                                    <button class="btn btn-default form-control extension-settings-button" data-toggle="modal" data-target="#extensions-modal" type="button" data-type="extension" data-func-type="GetList" data-extension-method="post" data-extensions="extensions-table-config-execute-post">
                                        <span class="glyphicon glyphicon-cog"></span> POST FUNCTION
                                    </button>
                                </div>
                            </div>
                            <div class="row fields-modal-type-link ">
                                <div class="col-sm-6 form-group">
                                    <label><?= Yii::t('app','Type link') ?></label>
                                    <select class="type-link form-control" name="type-link" required disabled>
                                        <option value="_blank"><?= Yii::t('app','Blank')?></option>
                                        <option value="_self"><?= Yii::t('app','Self')?></option>
                                    </select>
                                </div>
                            </div>
                            <div class="row external-link-block">
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
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline" data-dismiss="modal"><?= Yii::t('app','Close') ?></button>
                    <button type="button" data-toggle="modal" data-target="#js-edit-table" class="btn btn-warning js-edit-table js-edit-btn" onclick='fieldsConstructor.refreshCodeMirror(200)'><?= Yii::t('app','Edit JavaScript')?></button>
                    <button type="button" data-toggle="modal" data-target="#formatting-modal-table" class="btn btn-success"><?= Yii::t('app','Formatting')?></button>
                    <button type="button" data-toggle="modal" data-target="#access-modal-table" class="btn btn-danger"><?= Yii::t('app','Access rights')?></button>
                    <button type="submit" class="btn btn-primary btn-save-table" ><?= Yii::t('app','Save')?></button>
                </div>
            </form>
        </div>
    </div>
</div>