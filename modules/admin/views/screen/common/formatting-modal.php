<div class="modal fade" id="<?= $modalID ?>" tabindex="-1" role="dialog" aria-labelledby="formattingModal">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form class="<?= $formClass ?>" action="#" method="post">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title"><?= Yii::t('app', 'Formatting') ?></h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-6 form-group field-configure-block">
                            <h3 style="margin-top: 0">Label</h3>
                        </div>

                        <div class="col-sm-6 form-group field-configure-block">
                            <h3 style="margin-top: 0">Field</h3>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6 form-group field-configure-block field-label-block">
                            <label for="<?= $modalID ?>-formatting-label-bold" class="custom-checkbox">
                                <input id="<?= $modalID ?>-formatting-label-bold" type="checkbox" name="label_bold" value="1" />
                                <span><b>B</b></span>
                            </label>

                            <label for="<?= $modalID ?>-formatting-label-italic" class="custom-checkbox">
                                <input id="<?= $modalID ?>-formatting-label-italic" type="checkbox" name="label_italic" value="1" />
                                <span><i>I</i></span>
                            </label>

                            <label for="<?= $modalID ?>-formatting-label-strike" class="custom-checkbox">
                                <input id="<?= $modalID ?>-formatting-label-strike" type="checkbox" name="label_strike" value="1" />
                                <span><s>S</s></span>
                            </label>

                            <label for="<?= $modalID ?>-formatting-label-underline" class="custom-checkbox">
                                <input id="<?= $modalID ?>-formatting-label-underline" type="checkbox" name="label_underline" value="1" />
                                <span><u>U</u></span>
                            </label>
                        </div>

                        <div class="col-sm-6 form-group field-configure-block field-button-block">
                            <label for="<?= $modalID ?>-formatting-field-bold" class="custom-checkbox">
                                <input id="<?= $modalID ?>-formatting-field-bold" type="checkbox" name="field_bold" value="1" />
                                <span><b>B</b></span>
                            </label>

                            <label for="<?= $modalID ?>-formatting-field-italic" class="custom-checkbox">
                                <input id="<?= $modalID ?>-formatting-field-italic" type="checkbox" name="field_italic" value="1" />
                                <span><i>I</i></span>
                            </label>

                            <label for="<?= $modalID ?>-formatting-field-strike" class="custom-checkbox">
                                <input id="<?= $modalID ?>-formatting-field-strike" type="checkbox" name="field_strike" value="1" />
                                <span><s>S</s></span>
                            </label>

                            <label for="<?= $modalID ?>-formatting-field-underline" class="custom-checkbox">
                                <input id="<?= $modalID ?>-formatting-field-underline" type="checkbox" name="field_underline" value="1" />
                                <span><u>U</u></span>
                            </label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6 form-group field-configure-block field-label-block">
                            <label for="<?= $modalID ?>-label-text-color">Text color</label>
                            <?= \kartik\color\ColorInput::widget([
                                'name' => 'label_text_color',
                                'options' => [
                                    'id' => $modalID . '-label-text-color',
                                    'placeholder' => 'Choose text color ...',
                                    'data-type' => 'color'
                                ]
                            ]); ?>
                        </div>

                        <div class="col-sm-6 form-group field-configure-block field-button-block">
                            <label for="<?= $modalID ?>-field-text-color">Text color</label>
                            <?= \kartik\color\ColorInput::widget([
                                'name' => 'field_text_color',
                                'options' => [
                                    'id' => $modalID . '-field-text-color',
                                    'placeholder' => 'Choose text color ...',
                                    'data-type' => 'color'
                                ]
                            ]); ?>
                        </div>

                        <div class="col-sm-6 form-group field-configure-block field-label-block">
                            <label for="<?= $modalID ?>-label-text-gb-color">Text background color</label>
                            <?= \kartik\color\ColorInput::widget([
                                'name' => 'label_bg_color',
                                'options' => [
                                    'id' => $modalID . '-label-text-gb-color',
                                    'placeholder' => 'Choose text background color ...',
                                    'data-type' => 'color'
                                ]
                            ]); ?>
                        </div>

                        <div class="col-sm-6 form-group field-configure-block field-button-block">
                            <label for="<?= $modalID ?>-field-text-gb-color">Text background color</label>
                            <?= \kartik\color\ColorInput::widget([
                                'name' => 'field_bg_color',
                                'options' => [
                                    'id' => $modalID . '-field-text-gb-color',
                                    'placeholder' => 'Choose text background color ...',
                                    'data-type' => 'color'
                                ]
                            ]); ?>
                        </div>

                        <div class="col-sm-6 form-group field-configure-block field-label-block">
                            <label for="<?= $modalID ?>-label-font-family">Font family</label>
                            <select id="<?= $modalID ?>-label-font-family" class="form-control" name="label_font_family">
                                <option value="">-- Empty --</option>
                                <option style="font-family: 'Arial'" value="Arial">Arial</option>
                                <option style="font-family: 'Comic Sans MS'" value="Comic Sans MS">Comic Sans MS</option>
                                <option style="font-family: 'Courier New'" value="Courier New">Courier New</option>
                                <option style="font-family: 'Georgia'" value="Georgia">Georgia</option>
                                <option style="font-family: 'Lucida Sans Unicode'" value="Lucida Sans Unicode">Lucida Sans Unicode</option>
                                <option style="font-family: 'Tahoma'" value="Tahoma">Tahoma</option>
                                <option style="font-family: 'Times New Roman'" value="Times New Roman">Times New Roman</option>
                                <option style="font-family: 'Trebuchet MS'" value="Trebuchet MS">Trebuchet MS</option>
                                <option style="font-family: 'Verdana'" value="Verdana">Verdana</option>
                            </select>
                        </div>

                        <div class="col-sm-6 form-group field-configure-block field-button-block">
                            <label for="<?= $modalID ?>-field-font-family">Font family</label>
                            <select id="<?= $modalID ?>-field-font-family" class="form-control" name="field_font_family">
                                <option value="">-- Empty --</option>
                                <option style="font-family: 'Arial'" value="Arial">Arial</option>
                                <option style="font-family: 'Comic Sans MS'" value="Comic Sans MS">Comic Sans MS</option>
                                <option style="font-family: 'Courier New'" value="Courier New">Courier New</option>
                                <option style="font-family: 'Georgia'" value="Georgia">Georgia</option>
                                <option style="font-family: 'Lucida Sans Unicode'" value="Lucida Sans Unicode">Lucida Sans Unicode</option>
                                <option style="font-family: 'Tahoma'" value="Tahoma">Tahoma</option>
                                <option style="font-family: 'Times New Roman'" value="Times New Roman">Times New Roman</option>
                                <option style="font-family: 'Trebuchet MS'" value="Trebuchet MS">Trebuchet MS</option>
                                <option style="font-family: 'Verdana'" value="Verdana">Verdana</option>
                            </select>
                        </div>

                        <div class="col-sm-6 form-group field-configure-block field-label-block">
                            <label for="<?= $modalID ?>-label-font-size">Font size</label>
                            <input id="<?= $modalID ?>-label-font-size" type="number" min="8" max="48" name="label_font_size" class="form-control" />
                        </div>

                        <div class="col-sm-6 form-group field-configure-block field-button-block">
                            <label for="<?= $modalID ?>-field-font-size">Font size</label>
                            <input id="<?= $modalID ?>-field-font-size" type="number"  min="8" max="48" name="field_font_size" class="form-control" />
                        </div>

                        <div class="col-sm-6 form-group field-configure-block field-label-block">
                            <label for="<?= $modalID ?>-label-text-align">Text align</label>
                            <select id="<?= $modalID ?>-label-text-align" name="label_text_align" class="form-control">
                                <option value="left">left</option>
                                <option value="center">center</option>
                                <option value="right">right</option>
                                <option value="justify">justify</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline" data-dismiss="modal"><?= Yii::t('app','Close') ?></button>
                    <button type="button" class="btn btn-outline" onclick="$(this).parents('form')[0].reset();  $(this).parents('form').find('*[data-type=\'color\']').trigger('change');"><?= Yii::t('app','Reset')?></button>
                    <button type="submit" class="btn btn-primary btn-save-table"><?= Yii::t('app','Save')?></button>
                </div>
            </form>
        </div>
    </div>
</div>