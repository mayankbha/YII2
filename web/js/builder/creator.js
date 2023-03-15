/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 *
 * Main class. General functionality, configure section and render tables functionality.
 * @class creatorApp
 */

const FIELD_TYPE_TEXT = 'Text';
const FIELD_TYPE_TEXTAREA = 'Textarea';
const FIELD_TYPE_NUMERIC = 'Numeric';
const FIELD_TYPE_LIST = 'List';
const FIELD_TYPE_MULTI_SELECT = 'Multi-select list';
const FIELD_TYPE_CHECKBOX = 'Checkbox';
const FIELD_TYPE_RADIO = 'Radio';
const FIELD_TYPE_HIDDEN = 'Hidden';
const FIELD_TYPE_DOCUMENT = 'Document';
const FIELD_TYPE_ALERT = 'Alert';
const FIELD_TYPE_INLINE_SEARCH = 'Inline search';
const FIELD_TYPE_LINK = 'Link';
const FIELD_TYPE_LABEL = 'Label';
const FIELD_TYPE_BUTTON = 'Button';
const FIELD_TYPE_DATALIST = 'Datalist';
const FIELD_TYPE_DATALIST_RELATION = 'Relation datalist';
const FIELD_TYPE_WITH_DEPENDENT_FIELD = 'With dependent field';

const FORMAT_TYPE_NONE = 'None';
const FORMAT_TYPE_EMAIL = 'Email';
const FORMAT_TYPE_DATE = 'Date';
const FORMAT_TYPE_DATE_TIME = 'Date/Time';
const FORMAT_TYPE_CURRENCY = 'Currency';
const FORMAT_TYPE_LINK_LOCAL = 'Local';

Array.prototype.move = function(from, to) {
    this.splice(to, 0, this.splice(from, 1)[0]);
};

$.fn.serializeArrayWithInternationalization = function () {
    var rcheckableType = ( /^(?:checkbox|radio)$/i ),
        rCRLF = /\r?\n/g,
        rsubmitterTypes = /^(?:submit|button|image|reset|file)$/i,
        rsubmittable = /^(?:input|select|textarea|keygen)/i;

    return this.map(function () {
        var elements = $.prop(this, "elements");
        return elements ? $.makeArray(elements) : this;
    })
        .filter(function () {
            var type = this.type;

            return this.name && !$(this).is(":disabled") &&
                rsubmittable.test(this.nodeName) && !rsubmitterTypes.test(type) &&
                (this.checked || !rcheckableType.test(type));
        })
        .map(function (i, elem) {
            var val = $(this).val(),
                data = $(this).data('internationalization');

            if (val == null) {
                return null;
            }

            var result = {name: elem.name, value: val};
            if (data) {
                return $.extend(result, {internationalization: data});
            }

            return result;
        }).get();
};

$.fn.popoverInternationalization = function () {
    return $(this).popover({
        html: true,
        content: function() {
            var internationalizationSelector = $(this).find('[data-internationalization]').attr('data-internationalization'),
                data = $(internationalizationSelector).data('internationalization'),
                html = '';

            if (data) {
                $.each(data, function (key, value) {
                    if (key != document.documentElement.lang) {
                        html += '<b>' + key + ':</b> ' + value + '<br />';
                    }
                })
            }

            return html;
        },
        placement: 'bottom',
        trigger: 'hover'
    });
};

var creatorApp = function () {
    const SECTION_TYPE_EDIT = 'LIST';
    const SECTION_TYPE_GRID = 'TABLE';
    const SECTION_TYPE_DOCUMENT = 'DOCUMENT';
    const SECTION_TYPE_CHART_LINE = 'CHART-LINE';
    const SECTION_TYPE_CHART_PIE = 'CHART-PIE';
    const SECTION_TYPE_CHART_BAR_VERTICAL = 'CHART-BAR-VERTICAL';
    const SECTION_TYPE_CHART_BAR_HORIZONTAL = 'CHART-BAR-HORIZONTAL';
    const SECTION_TYPE_CHART_DOUGHNUT = 'CHART-DOUGHNUT';

    const LINE_CHARTS = [
        SECTION_TYPE_CHART_BAR_VERTICAL,
        SECTION_TYPE_CHART_BAR_HORIZONTAL,
        SECTION_TYPE_CHART_LINE
    ];
    const PIE_CHARTS = [
        SECTION_TYPE_CHART_PIE,
        SECTION_TYPE_CHART_DOUGHNUT
    ];

    const ALL_CHARTS = LINE_CHARTS.concat(PIE_CHARTS);

    this.load = function () {
        var me = this;
        $(document).ready(function () {
            sessionStorage.clear();
            me.init();
            me.bindLoadEvents();
        });
    };

    //Set url for getting function parameters. Used in modules/admin/controllers/ScreenController.php
    this.setParamsUrl = function (url) {
        this.param.library.getParamsUrl = url;
    };

    //Set url for getting library function. Used in modules/admin/controllers/ScreenController.php
    this.setFuncUrl = function (url) {
        this.param.library.getFuncUrl = url;
    };

    this.setLinkUrl = function (url) {
        this.param.library.getLinkUrl = url;
    };

    this.setSearchConfigParamUrl = function (url) {
        this.param.library.searchConfigParamUrl = url;
    };

    //Set url for getting library function extensions. Used in modules/admin/controllers/ScreenController.php
    this.setFuncExtensionUrl = function (url) {
        this.param.library.getFuncExtensionUrl = url;
    };

    this.registerJsTemplate = function (object) {
        var q = this;
        $(document).on('change', '.js-templates', function (obj) {
            var idTemplate = $(obj.target).val();
            if (idTemplate) {
                if (q.isCommonJSTab.call(this)) {
                    var activeTab = $('.js-generator-section.tab-pane.active');
                    var jsSection = activeTab.find('[data-id=cjss' + idTemplate + ']');

                    jsSection.show().addClass('opened');
                    if( jsSection.find('.without-var-section').length) {
                        jsSection.find('.without-var-section').val('1');
                    }
                } else {
                    $('.tab-content .tab-content .active .CodeMirror').each(function (i, obj) {
                        var value = obj.CodeMirror.getValue();

                        obj.CodeMirror.setValue((value ? value + "\n" : value) + object[idTemplate]);
                        obj.CodeMirror.refresh();
                    });

                    $('.js-templates').val('');
                }
            }
        });
    };

    this.isCommonJSTab = function () {
        return $(this).parents('.fields-constructor-form').find('.common-javascript-tab').hasClass('active');
    };

    this.param = {
        library: {
            select: '#setting-modal .screen-lib',
            functionSelect: '#setting-modal .screen-lib-func',
            getParamsUrl: null,
            getFuncUrl: null,
            getFuncExtensionUrl: null,
            getLinkUrl: null,
            searchConfigParamUrl: null
        },
        settingBtn: '.setting-icon',
        layoutLabel: {
            block: '#setting-modal .layout-label-group',
            input: '#setting-modal .layout-label-group .layout_label'
        },
        configureBlock: '#setting-modal .render-configure',
        typeBtn: '#setting-modal .section-type input',
        submitFormBtn: '#setting-modal .btn-save-settings',
        funcBlockWrapper: '.func-block-wrapper',
        panelBlock: '.section-panel',
        formSecondStep: '#new-screen-create',
        templateLayoutElement: '#form-template-layout',
        inTable: {
            className: 'in-table',
        },
        outTable: {
            className: 'out-table',
            checkbox: 'param-table-checkbox',
            label: 'out-table-label',
            paramType: 'out-table-param-type',
            formatType: 'out-table-format-type',
            key: 'out-table-key',
            removeButton: 'out-table-remove-row'
        },
        chartTable: {
            formatType: 'chart-table-format-type'
        },
        layoutTable: {
            colCount: 'table-col-count',
            showType: 'table-show-type',
            labelOrientation: 'table-label-orientation',
            AFKeyPart: 'table-alias-framework-key-part',
            AFisUseTenant: 'table-alias-framework-use-tenant',
            AFAddRow: 'table-alias-framework-add-row',
            AFDeleteRow: 'table-alias-framework-delete-row',
            addRowButton: 'add-table-row-button'
        },
        sectionFormatting: {
            button: '.section-formatting-button',
            form: '.section-formatting-form',
            modal: '#formatting-modal-section'
        },
        sectionJavaScript: {
            button: '.section-javascript-button',
            form: '.section-javascript-form',
            modal: '#javascript-modal-section'
        },
        internationalizationModal: '#internationalization-modal',
        accessButtonSave: '.btn-save-access'
    };

    this.paramTypes = {};
    this.paramTypes[FIELD_TYPE_TEXT] = [FORMAT_TYPE_NONE, FORMAT_TYPE_EMAIL, FORMAT_TYPE_DATE, FORMAT_TYPE_DATE_TIME];
    this.paramTypes[FIELD_TYPE_NUMERIC] = [FORMAT_TYPE_NONE, FORMAT_TYPE_CURRENCY];
    this.paramTypes[FIELD_TYPE_LINK] = [FORMAT_TYPE_LINK_LOCAL];
    this.paramTypes[FIELD_TYPE_LIST] = null;
    this.paramTypes[FIELD_TYPE_TEXTAREA] = null;
    this.paramTypes[FIELD_TYPE_MULTI_SELECT] = null;
    this.paramTypes[FIELD_TYPE_CHECKBOX] = null;
    this.paramTypes[FIELD_TYPE_RADIO] = null;
    this.paramTypes[FIELD_TYPE_HIDDEN] = null;
    this.paramTypes[FIELD_TYPE_DOCUMENT] = null;
    this.paramTypes[FIELD_TYPE_INLINE_SEARCH] = null;
    this.paramTypes[FIELD_TYPE_LABEL] = null;
    this.paramTypes[FIELD_TYPE_BUTTON] = null;
    this.paramTypes[FIELD_TYPE_DATALIST] = null;
    this.paramTypes[FIELD_TYPE_DATALIST_RELATION] = null;
    this.paramTypes[FIELD_TYPE_WITH_DEPENDENT_FIELD] = [FORMAT_TYPE_DATE, FORMAT_TYPE_DATE_TIME];;

    //default template structure for section
    this.templateStack = {
        "row_num": null,
        "col_num": null,
        "data_source_get": null,
        "layout_type": null,
        "layout_label": null,
        "layout_label_internationalization": {},
        "layout_fields": null,
        "layout_table": {
            "count": null,
            "show_type": null,
            "label_orientation": null,
            "column_configuration": {}
        },
        "layout_configuration": {
            "params": [],
            "labels": {},
            "format_type": {}
        },
        "layout_formatting": null
    };
    this.templateLayout = [];
    this.isAliasFramework = false;

    //Initial method
    this.init = function () {
        var g = this,
            layout = $(this.param.templateLayoutElement).val();

        if (layout) {
            if (layout = $.parseJSON(layout)) {
                if (layout['template_layout']) {
                    this.templateLayout = layout['template_layout'];
                }

                this.isAliasFramework = layout['alias_framework'] && layout['alias_framework']['enable'];
            }
        }

        fieldsConstructor.init();
        tableConstructor.init();

        //Update sections
        this.getLibFunctionList($(this.param.library.select).val(), 'GETTER', function () {
            g.updateSectionsInfo();
        });

        $('[data-target="internationalization-tooltip"]').popoverInternationalization();
    };

    //Events when changing section settings
    this.bindLoadEvents = function () {
        var g = this,
            stack = null,
            library = null;
        $(document)
            //Click on section settings button
            .on('click', g.param.settingBtn, function () {
                stack = $.extend(true, {}, g.templateStack);
                stack.row_num = $(this).attr('data-row');
                stack.col_num = $(this).attr('data-col');
                library = $(g.param.library.select).val();

                if (stack.row_num == '0' && stack.row_num == '0') $(g.param.layoutLabel.block).hide();
                else $(g.param.layoutLabel.block).show();

                g.resetModal(g.param.configureBlock);

                var template = g.getTemplate(stack.row_num, stack.col_num);
                if (template) {
                    stack = template;

                    g.getLibFunctionList(library, 'GETTER', function (data) {
                        $(g.param.library.functionSelect).html('');

                        if (data && stack.data_source_get) {
                            $.each(data, function (i, item) {
                                if (item['func_layout_type'].search(stack.layout_type) >= 0) {
                                    g.appendToSelect($(g.param.library.functionSelect), item['func_name'], item['func_name'] + ((item['func_descr']) ? ': ' + item['func_descr'] : ''));
                                }
                            });

                            $(g.param.library.functionSelect).val(stack.data_source_get);
                            $(g.param.funcBlockWrapper).show();
                            $(g.param.submitFormBtn).show();
                            $(g.param.layoutLabel.input).val(stack.layout_label).data('internationalization', stack.layout_label_internationalization).show();

                            g.radioButtonSetValue(g.param.typeBtn, stack.layout_type);
                        }

                        $(g.param.library.functionSelect).trigger('change');
                    });

                    $(g.param.sectionFormatting.form)[0].reset();
                    if (typeof stack['layout_formatting'] === "object") {
                        $.each(stack['layout_formatting'], function (i, item) {
                            var findInput = $(g.param.sectionFormatting.form).find('*[name="' + item.name + '"]');
                            if (findInput.attr('type') === 'checkbox') findInput.prop('checked', true);
                            else findInput.val(item.value);
                        });
                    }
                    $(g.param.sectionFormatting.form).find('*[data-type="color"]').trigger('change');
                }

                if (stack.layout_type  == SECTION_TYPE_EDIT || stack.layout_type  == SECTION_TYPE_GRID) $(g.param.sectionFormatting.button).show();
                else $(g.param.sectionFormatting.button).hide();
            })
            //Selected function on section settings modal
            .on('change', g.param.library.functionSelect, function () {
                stack.data_source_get = $(this).val();

                if (stack.data_source_get) {
                    g.initConfig(library, stack);
                    g.setInfo(g.param.configureBlock, false);
                    $(g.param.submitFormBtn).show();
                } else {
                    $(g.param.configureBlock).html('');
                }
            })
            //Click save section settings button
            .on('click', g.param.submitFormBtn, function () {
                try {
                    stack['data_source_get'] = $(g.param.library.functionSelect).val();

                    if (LINE_CHARTS.indexOf(stack['layout_type']) != -1) {
                        stack['layout_configuration']['params'] = {};
                        stack['layout_configuration']['params']['x'] = g.getValues('.' + g.param.outTable.className + ' .' + g.param.outTable.checkbox);
                        stack['layout_configuration']['params']['y'] = g.getValues('.' + g.param.inTable.className + ' .' + g.param.outTable.checkbox);
                    } else {
                        stack['layout_configuration']['params'] = g.getValues('.' + g.param.outTable.checkbox);
                    }
                    stack['layout_configuration']['labels'] = g.getValues('.' + g.param.outTable.label, true);
                    stack['layout_configuration']['labels_internationalization'] = g.getValuesInternationalization('.' + g.param.outTable.label);
                    stack['layout_configuration']['format_type'] = g.getValues('.' + g.param.chartTable.formatType, true);

                    stack['layout_label'] = $(g.param.layoutLabel.input).val();
                    stack['layout_label_internationalization'] = $(g.param.layoutLabel.input).data('internationalization');

                    stack['layout_table']['count'] = $('.' + g.param.layoutTable.colCount).val();
                    stack['layout_table']['show_type'] = $('.' + g.param.layoutTable.showType).val();
                    stack['layout_table']['label_orientation'] = $('.' + g.param.layoutTable.labelOrientation).val();

                    if (g.isAliasFramework) {
                        var keyParts = [];

                        stack['layout_table']['alias_framework'] = {use_tenant: $('.' + g.param.layoutTable.AFisUseTenant).prop('checked')};
                        $('.' + g.param.layoutTable.AFKeyPart).each(function () {
                            keyParts.push($(this).val());
                        });
                        stack['layout_table']['alias_framework']['key_part'] = keyParts;
                    } else {
                        delete stack['layout_table']['alias_framework'];
                    }

                    g.saveTemplate(stack);
                } catch (e) {
                    g.setInfo(g.param.configureBlock, 'danger', 'Structure of template has been changed. Please delete this tab and create new')
                }
            })
            //Selected type of section on section settings modal
            .on('change', g.param.typeBtn, function () {
                var funcSelect = $(g.param.library.functionSelect);

                stack.layout_type = $(this).val();

                g.getLibFunctionList(library, 'GETTER', function (data) {
                    if (data) {
                        funcSelect.html('');
                        $.each(data, function (i, item) {
                            if (item['func_layout_type'].search(stack.layout_type) >= 0) {
                                g.appendToSelect(funcSelect, item['func_name'], item['func_name'] + ((item['func_descr']) ? ': ' + item['func_descr'] : ''));
                            }
                        });
                    }

                    $(g.param.library.functionSelect).trigger('change');
                });

                $(g.param.funcBlockWrapper).show();

                if (stack.layout_type  == SECTION_TYPE_EDIT || stack.layout_type  == SECTION_TYPE_GRID) $(g.param.sectionFormatting.button).show();
                else $(g.param.sectionFormatting.button).hide();
            })
            //Checked checkbox in params table of section settings modal
            .on('click', '.' + g.param.outTable.checkbox, function () {
                var name = $(this).attr('name'),
                    prop = $(this).prop('checked');

                $(this).parents('tr').find('input:not(:checkbox), select, button').val('').data('internationalization', null).prop('disabled', !prop);

                if ($(this).parents('.' + g.param.outTable.className).length > 0) {
                    $('.' + g.param.inTable.className + ' .' + g.param.outTable.checkbox + '[name="' + name + '"]').prop('disabled', prop);
                    $('.' + g.param.outTable.className + ' .' + g.param.outTable.checkbox + ':not(:checked)').prop('disabled', prop);
                } else if ($(this).parents('.' + g.param.inTable.className).length > 0) {
                    $('.' + g.param.outTable.className + ' .' + g.param.outTable.checkbox + '[name="' + name + '"]').prop('disabled', prop);
                }
            })
            //Click save tab template button
            .on('submit', g.param.formSecondStep, function () {
                
                var selector = $(g.param.templateLayoutElement);
                if (!g.validateSections()) {
                    alert('First configure all sections');
                    return false;
                }

                g.addFieldsToTemplate();

                var template = $.parseJSON(selector.val());
                template['template_layout'] = g.templateLayout;
                selector.val(JSON.stringify(template));
            })
            .on('submit', g.param.sectionFormatting.form, function (e) {
                e.preventDefault();

                $('.spectrum-source').prop('disabled', true); //Don't include additional color type field to serialize
                stack['layout_formatting'] = $(this).serializeArray();
                $('.spectrum-source').prop('disabled', false); //Don't include additional color type field to serialize

                $(g.param.sectionFormatting.modal).find('.close').trigger('click');
                $(g.param.sectionJavaScript.form).find('.close').trigger('click');
                g.saveTemplate(stack);
                g.setInfo(g.param.configureBlock, 'success', 'Formatting has been saved!');
            })
            .on('submit', g.param.internationalizationModal + ' form', function (event) {
                event.preventDefault();

                var serialize = $(this).serializeArray(),
                    internationalizationSelector = $(this).data('internationalization'),
                    reformattedSerialize = {};

                $.each(serialize, function () {
                    if (this.value) {
                        reformattedSerialize[this.name] = this.value;
                    }
                });

                $(g.param.internationalizationModal).modal('hide');
                $(internationalizationSelector).data('internationalization', reformattedSerialize);
                if (reformattedSerialize[document.documentElement.lang]) {
                    $(internationalizationSelector).val(reformattedSerialize[document.documentElement.lang]);
                    delete reformattedSerialize[document.documentElement.lang];
                }
            })
            .on('show.bs.modal', g.param.internationalizationModal, function (event) {
                var internationalizationSelector = $(event.relatedTarget).attr('data-internationalization'),
                    data = $(internationalizationSelector).data('internationalization'),
                    form = $(event.currentTarget).find('form'),
                    serverLanguage = $(internationalizationSelector).val();

                form[0].reset();
                form.data('internationalization', internationalizationSelector);

                if (data) {
                    $.each(data, function (key, value) {
                        form.find('[name="' + key + '"]').val(value);
                    });
                }
                if (serverLanguage) {
                    form.find('[name="' + document.documentElement.lang + '"]').val(serverLanguage);
                }
            })
            .on("hidden.bs.modal", '.modal', function () {
                if ($('.modal:visible').length) {
                    $('body').addClass('modal-open');
                }
            })
            .on('click', '.' + g.param.layoutTable.AFDeleteRow, function () {
                if ($('.' + g.param.layoutTable.AFDeleteRow).length > 1) {
                    $(this).parents('tr').remove();
                }
            })
            .on('click', '.' + g.param.layoutTable.AFAddRow, function () {
                var keyPartRow = $('.' + g.param.layoutTable.AFDeleteRow).parents('tr').last();
                $(this).parents('tr').before('<tr>' + keyPartRow.html() + '</tr>');
            })
            .on('click', '.' + g.param.layoutTable.addRowButton, function () {
                var tableBody = $(this).parents('table').find('tbody'),
                    inputName = '__button_' + Math.random().toString(36).substring(7),
                    deleteButton = $('<span />', {class: 'glyphicon glyphicon-remove ' + g.param.outTable.removeButton}),
                    exampleRow;

                exampleRow = tableBody.find('tr:nth-child(2)').clone();
                exampleRow.find('input').attr('name', inputName);
                exampleRow.find('.' + g.param.outTable.checkbox).prop('checked', true).attr('value', inputName).hide();
                exampleRow.find('.' + g.param.outTable.checkbox).after(deleteButton);
                exampleRow.find('button').attr('data-internationalization', '.' + g.param.outTable.label + '[name="' + inputName + '"]')
                exampleRow.find('td:nth-child(2)').html(inputName);
                exampleRow.find('input, button, select').prop('disabled', false).val('');

                tableBody.append(exampleRow);
            })
            .on('click', '.' + g.param.outTable.removeButton, function () {
                $(this).parents('tr').remove();
            })
            .on('click', g.param.accessButtonSave, function () {
                $(this).parents('.modal').modal('hide');
            })
    };

    this.setStyles = function (object, data, attr) {
        if (!attr) attr = 'field';

        var textDecoration = '';
        textDecoration += (data[attr + '_strike']) ? 'line-through' : '';
        textDecoration += (data[attr + '_underline']) ? ' underline' : '';

        object.css({
            'text-decoration': (textDecoration) ? textDecoration : null,
            'font-weight': (data[attr + '_bold']) ? 'bold' : null,
            'font-style': (data[attr + '_italic']) ? 'italic' : null,
            'color': (data[attr + '_text_color']) ? data[attr + '_text_color'] : null,
            'background-color': (data[attr + '_bg_color']) ? data[attr + '_bg_color'] : null,
            'font-family': (data[attr + '_font_family']) ? data[attr + '_font_family'] : null,
            'font-size': (data[attr + '_font_size']) ? data[attr + '_font_size'] : null,
        });
    };

    //Save form of first step for returned. Used in /modules/admin/views/screen/form.php
    this.ntSaveForms = function () {
        var saveForms = $(".nt-save-form");

        saveForms.each(function () {
            var cl = 'ntSaveForms' + $(this).attr('name');

            $(this).attr('data-storage-name', cl);
            if (localStorage[cl] && localStorage[cl].length > 0 && !$(this).val()) {
                $(this).val(localStorage[cl]);
            }
        });

        saveForms.change(function () {
            localStorage[$(this).attr('data-storage-name')] = $(this).val();
        });
    };

    //Clear saving form of first step. Used in /modules/admin/views/screen/form.php
    this.ntClearForms = function () {
        $(".nt-save-form").each(function () {
            var cl = 'ntSaveForms' + $(this).attr('name');
            localStorage.removeItem(cl);
        });
    };

    /**
     * Getting table with specified parameters
     * @param {Array} paramsObject -
     * @param {?Array} paramsTrNamed
     * @param {boolean} [leftHead] - Set true if orientation of table is left (optional)
     * @returns {object}
     */
    this.getTable = function (paramsObject, paramsTrNamed, leftHead) {
        var isFullHead = false,
            table = $('<table />', {"class": 'table table-bordered'}),
            th = $('<tr />');

        if ($.isArray(paramsObject[0])) leftHead = true;
        if (!leftHead) $('<thead />').appendTo(table);

        $('<tbody />').appendTo(table);

        if (paramsObject && Object.keys(paramsObject).length > 0) {
            $.each(paramsObject, function (i, item) {
                var tr = $('<tr />');
                $.each(item, function (key, value) {
                    var td = (leftHead && key == 0) ? $('<th />') : $('<td />');
                    td.html(value).appendTo(tr);

                    if (!isFullHead && !leftHead) $('<th />', {text: key}).appendTo(th);
                    if (paramsTrNamed) {
                        $.each(paramsTrNamed, function () {
                            if (this == key) tr.attr('data-' + this, value);
                        });
                    }
                });
                tr.appendTo(table.find('tbody'));
                isFullHead = true;
            });
            th.appendTo(table.find('thead'));
        }

        return table;
    };


    /**
     * Added column to table object
     * @param {object} table - object object of table
     * @param {object} data - Data of column for added
     * @param {addColumnCallback} callback
     * @returns {object}
     */
    this.addColumnToTable = function (table, data, callback) {
        if (typeof data !== "object") return table;

        var thValue = (data.th) ? data.th : '',
            index = data.index,
            elementLength = table.find('thead tr th').length;

        //this is not best solution
        if (index < 0) {
            index = 0;
            elementLength += 1;
        }

        if ((elementLength - 1) <= index) table.find('thead tr th:last-child').after($('<th />', {text: thValue}));
        else table.find('thead tr th').eq(index).before($('<th />', {text: thValue}));

        table.find('tbody tr').each(function () {
            var td = $('<td />'),
                value = '';

            if (typeof callback === "function") value = callback(this);

            td.append(value);

            if ((elementLength - 1) <= index) $(this).find('td:last-child').after(td);
            else $(this).find('td').eq(index).before(td);
        });

        return table;
    };
    /**
     * @callback addColumnCallback
     * @param {Object} data
     */

    /**
     * Append table with chart settings type
     * @param {string} inSelector
     * @param {Array} params - Object returned by function getFunctionParams
     * @param {creatorApp.templateStack} stack
     * @returns {null}
     */
    this.renderChartTypeData = function (inSelector, params, stack) {
        var result = null;

        if (LINE_CHARTS.indexOf(stack['layout_type']) != -1) {
            result = this.getTableForChartLineType(params, stack);
        } else if (PIE_CHARTS.indexOf(stack['layout_type']) != -1) {
            result = this.getTableForChartPieType(params, stack);
        }

        $(inSelector).empty();
        if (result) {
            $(inSelector).append(result);
        } else {
            this.setInfo(this.param.configureBlock, 'info', 'Has no params!');
            return null;
        }
    };

    /**
     * Getting table for chart line type with specified parameters
     * @param {Array} params
     * @param {creatorApp.templateStack} stack
     * @returns {object}
     */
    this.getTableForChartLineType = function (params, stack) {
        var g = this,
            attrProperty = 'alias_field',
            inTable = this.getPrepareTable(params, stack, ['label']),
            outTable = this.getPrepareTable(params, stack, []);

        inTable = this.addColumnToTable(inTable, {
            th: 'format_type',
            index: 2
        }, function (tr) {
            var trData = $(tr).attr('data-' + attrProperty),
                select = $('<select />', {
                    name: trData,
                    "class": 'form-control ' + g.param.chartTable.formatType
                }).append(
                    $('<option />', {value: '', text: '-- Select --'}),
                    $('<option />', {value: FIELD_TYPE_NUMERIC, text: FIELD_TYPE_NUMERIC}),
                    $('<option />', {value: FORMAT_TYPE_CURRENCY, text: FORMAT_TYPE_CURRENCY})
                );

            if (stack['layout_configuration']['format_type'] && (trData in stack['layout_configuration']['format_type'])) {
                select.val(stack['layout_configuration']['format_type'][trData]);
            }
            return select;
        });

        inTable = this.addColumnToTable(inTable, {index: -1}, function (tr) {
            var trData = $(tr).attr('data-' + attrProperty),
                input = $('<input />', {
                    type: 'checkbox',
                    name: trData,
                    value: trData,
                    "class": g.param.outTable.checkbox
                });

            if ($.inArray(trData, stack['layout_configuration']['params']['y']) >= 0) input.prop('checked', true);
            else {
                if ($.inArray(trData, stack['layout_configuration']['params']['x']) >= 0) input.prop('disabled', true);
                $(tr).find('input:not(:checkbox), select').val('').data('internationalization', null).prop('disabled', true);
            }

            return input;
        });

        outTable = this.addColumnToTable(outTable, {
            th: 'format_type',
            index: 2
        }, function (tr) {
            var trData = $(tr).attr('data-' + attrProperty),
                select = $('<select />', {
                    name: trData,
                    "class": 'form-control ' + g.param.chartTable.formatType
                }).append(
                    $('<option />', {value: '', text: '-- Select --'}),
                    $('<option />', {value: FIELD_TYPE_TEXT, text: FIELD_TYPE_TEXT}),
                    $('<option />', {value: FIELD_TYPE_NUMERIC, text: FIELD_TYPE_NUMERIC}),
                    $('<option />', {value: FORMAT_TYPE_CURRENCY, text: FORMAT_TYPE_CURRENCY}),
                    $('<option />', {value: FORMAT_TYPE_DATE, text: FORMAT_TYPE_DATE}),
                    $('<option />', {value: FORMAT_TYPE_DATE_TIME, text: FORMAT_TYPE_DATE_TIME})
                );

            if (stack['layout_configuration']['format_type'] && (trData in stack['layout_configuration']['format_type'])) {
                select.val(stack['layout_configuration']['format_type'][trData]);
            }

            return select;
        });

        var flag = false;
        outTable = this.addColumnToTable(outTable, {index: -1}, function (tr) {
            var trData = $(tr).attr('data-' + attrProperty),
                input = $('<input />', {
                    type: 'checkbox',
                    name: trData,
                    value: trData,
                    "class": g.param.outTable.checkbox
                });

            if ($.inArray(trData, stack['layout_configuration']['params']['x']) >= 0) {
                input.prop('checked', true);
                flag = true;
            } else $(tr).find('input:not(:checkbox), select').val('').prop('disabled', true);

            return input;
        });

        if (flag) outTable.find('input[type="checkbox"]:not(:checked)').prop('disabled', true);

        outTable.addClass(g.param.outTable.className);
        inTable.addClass(g.param.inTable.className);

        outTable = this.appendFindColumnToTable(outTable);
        inTable = this.appendFindColumnToTable(inTable);

        return $('<div />').html(inTable).append(outTable);
    };

    this.appendFindColumnToTable = function (table) {
        if (this.isAliasFramework) {
            var tr = $('<tr />'),
                td = $('<td />', {colspan: table.find('tr td').length}),
                input = $('<input />', {type: 'text', placeholder: 'Alias field...', class: 'form-control pull-left'});

            input.change(function () {
                var value = $(this).val();

                if (value) {
                    table.find('tr').each(function () {
                        var data = $(this).attr('data-alias_field');
                        if (data && (data.toUpperCase().indexOf(value.toUpperCase()) < 0)) {
                            $(this).hide();
                        } else {
                            $(this).show();
                        }
                    });
                } else {
                    table.find('tr').show();
                }
            });

            td.append(input);
            tr.append(td);
            table.find('tbody').prepend(tr);
        }

        return table;
    };

    /**
     * Getting table for chart pie type with specified parameters
     * @param {Array} params
     * @param {creatorApp.templateStack} stack
     * @returns {object}
     */
    this.getTableForChartPieType = function (params, stack) {
        var g = this,
            attrProperty = 'alias_field',
            table = this.getPrepareTable(params, stack);

        table = this.addColumnToTable(table, {
            th: 'format_type',
            index: 3
        }, function (tr) {
            var trData = $(tr).attr('data-' + attrProperty),
                select = $('<select />', {
                    name: trData,
                    "class": 'form-control ' + g.param.chartTable.formatType
                }).append(
                    $('<option />', {value: '', text: '-- Select --'}),
                    $('<option />', {value: FIELD_TYPE_NUMERIC, text: FIELD_TYPE_NUMERIC}),
                    $('<option />', {value: FORMAT_TYPE_CURRENCY, text: FORMAT_TYPE_CURRENCY})
                );

            if (stack['layout_configuration']['format_type'] && (trData in stack['layout_configuration']['format_type'])) {
                select.val(stack['layout_configuration']['format_type'][trData]);
            }

            select.prop('disabled', !$(tr).find('input[type="checkbox"]').prop('checked'));
            return select;
        });

        return this.appendFindColumnToTable(table);
    };

    /**
     * Getting table for grid type with specified parameters
     * @param {string} inSelector
     * @param {Array} params
     * @param {creatorApp.templateStack} stack
     * @return {null}
     */
    this.renderTableTypeData = function (inSelector, params, stack) {
        var columnCountInput = $('<input />', {
                "class": 'form-control ' + this.param.layoutTable.colCount,
                type: 'number',
                min: 1,
                step: 1
            }),
            showTypeSelect = $('<select />', {"class": 'form-control ' + this.param.layoutTable.showType}).append(
                $('<option />', {value: 'SCROLL', text: 'scroll'})
            ),
            labelOrientationSelect = $('<select />', {"class": 'form-control ' + this.param.layoutTable.labelOrientation}).append(
                $('<option />', {value: 'TOP', text: 'top'}),
                $('<option />', {value: 'LEFT', text: 'left'})
            ),
            inTableData = [
                ['Count', columnCountInput],
                ['Show type', showTypeSelect],
                ['Label orientation', labelOrientationSelect]
            ];

        if (!this.isAliasFramework) {
            showTypeSelect.append($('<option />', {value: 'PAGING', text: 'paging'}));
        } else {
            var fieldList = this.getMainFieldsList(),
                keyPartSelect = $('<select />', {"class": 'form-control ' + this.param.layoutTable.AFKeyPart}),
                columnIsUseTenant = $('<input />', {
                    "class": this.param.layoutTable.AFisUseTenant,
                    type: 'checkbox'
                }),
                AddRowButton = $('<button />', {
                    "class": 'btn btn-sm btn-success ' + this.param.layoutTable.AFAddRow,
                    "text": 'Add key part'
                }),
                DeleteRowButton = $('<button />', {
                    "class": 'btn btn-sm btn-danger ' + this.param.layoutTable.AFDeleteRow,
                    "text": 'Delete key part'
                }),
                aliasFrameworkTableConfig = [['Use tenant', columnIsUseTenant, '']],
                aliasFrameworkTable;

            $.each(fieldList, function (i, item) {
                keyPartSelect = screenCreator.appendToSelect(keyPartSelect, item, item);
            });

            columnIsUseTenant.prop('checked', stack['layout_table'] && stack['layout_table']['alias_framework'] && stack['layout_table']['alias_framework']['use_tenant']);
            if (stack['layout_table'] && stack['layout_table']['alias_framework'] && stack['layout_table']['alias_framework']['key_part']) {
                $.each(stack['layout_table']['alias_framework']['key_part'], function (i, value) {
                    aliasFrameworkTableConfig.push(['Key part', keyPartSelect.clone().val(value), DeleteRowButton.clone()]);
                });
            } else {
                aliasFrameworkTableConfig.push(['Key part', keyPartSelect, DeleteRowButton]);
            }

            aliasFrameworkTableConfig.push(['', AddRowButton, '']);
            aliasFrameworkTable = this.getTable(aliasFrameworkTableConfig, null, true);
        }

        if (stack['layout_table']) {
            columnCountInput.val(stack['layout_table']['count']);
            showTypeSelect.val(stack['layout_table']['show_type']);
            labelOrientationSelect.val(stack['layout_table']['label_orientation']);
        }

        var inTable = this.getTable(inTableData, null, true),
            outTable = this.getPrepareTable(params, stack),
            paramTypeClass = '.' + this.param.outTable.paramType;

        $(inSelector).empty();
        if (outTable) {
            if (this.isAliasFramework && aliasFrameworkTable) {
                $(inSelector).append('<label class="control-label">Alias framework PK configuration</label>');
                $(inSelector).append(aliasFrameworkTable);
            }

            outTable = this.appendAddRowButton(outTable);
            outTable = this.appendFindColumnToTable(outTable);

            $(inSelector).append(inTable);
            var responsiveTable = $('<div />', {class: 'table-responsive'}).append(outTable);
            $(inSelector).append(responsiveTable);

            $(paramTypeClass).trigger('change');
        } else {
            this.setInfo(this.param.configureBlock, 'info', 'Has no params!');
            return null;
        }
    };

    this.appendAddRowButton = function (selector) {
        var addRowButton = $('<button />', {text: 'Add row', class: 'btn btn-warning ' + this.param.layoutTable.addRowButton});

        selector.append('<tfoot><tr><td colspan="5"></td></tr></tfoot>');
        selector.find('tfoot tr td').append(addRowButton);

        return selector;
    };

    this.getMainFieldsList = function () {
        var fieldList = [];
        $.each(fieldsConstructor.fieldsData, function (i, item) {
            if (item['data']) {
                $.each(item['data'], function (j, field) {
                    $.each(field, function (k, property) {
                        if (property['name'] == 'data_field') {
                            fieldList.push(property['value']);
                            return false;
                        }
                    });
                });
            }
        });

        return fieldList;
    };

    /**
     * Added standard column to table
     * @param {Array} params
     * @param {creatorApp.templateStack} stack
     * @param {?Array} [config] - Added columns (['checkbox', 'label'])
     * @return {object}
     */
    this.getPrepareTable = function (params, stack, config) {
        var g = this,
            attrProperty = ['alias_field'],
            outTable, additionalRow = [];

        $.each(stack['layout_configuration']['params'], function (i, item) {
            var flag = false;
            $.each(params, function (j, param) {
                if (flag = item == param['alias_field']) {
                    return false;
                }
            });

            if (!flag) {
                additionalRow.push(item);
                params.push({
                    alias_field: item,
                });
            }
        });
        outTable = this.getTable(params, attrProperty);

        if (!config || $.inArray('label', config) >= 0) {
            outTable = this.addColumnToTable(outTable, {
                th: 'column_label',
                index: 2
            }, function (tr) {
                var trData = $(tr).attr('data-' + attrProperty),
                    wrapper = $('<div />', {
                        'class': 'input-group',
                        'data-target': 'internationalization-tooltip'
                    }),
                    input = $('<input />', {
                        'type': 'text',
                        'name': trData,
                        'class': 'form-control ' + g.param.outTable.label,
                    }),
                    button = $('<span />', {
                        'class': 'input-group-btn'
                    }).append($('<button />', {
                        'class': 'btn btn-default',
                        'data-toggle':  'modal',
                        'data-target': '#internationalization-modal',
                        'data-internationalization': '.' + g.param.outTable.label + '[name="' + trData + '"]',
                        'text': 'Internationalization'
                    })),
                    internationalizationValue = null;

                if (stack['layout_configuration']['labels_internationalization'] && trData in stack['layout_configuration']['labels_internationalization']) {
                    internationalizationValue = stack['layout_configuration']['labels_internationalization'][trData];
                }

                input.data('internationalization', internationalizationValue);
                if (stack['layout_configuration']['labels'] && trData in stack['layout_configuration']['labels']) {
                    input.val(stack['layout_configuration']['labels'][trData]);
                }

                return wrapper.append(input).append(button).popoverInternationalization();
            });
        }

        if (!config || $.inArray('checkbox', config) >= 0) {
            outTable = this.addColumnToTable(outTable, {index: -1}, function (tr) {
                var trData = $(tr).attr('data-' + attrProperty),
                    input = $('<input />', {
                        type: 'checkbox',
                        name: trData,
                        value: trData,
                        "class": g.param.outTable.checkbox
                    }),
                    deleteButton = $('<span />', {class: 'glyphicon glyphicon-remove ' + g.param.outTable.removeButton});

                if ($.inArray(trData, additionalRow) >= 0) {
                    input.hide();
                    input = $('<div />').append(input).append(deleteButton);
                }

                if ($.inArray(trData, stack['layout_configuration']['params']) >= 0) {
                    input.prop('checked', true);
                } else {
                    $(tr).find('input:not(:checkbox), select, button').data('internationalization', null).val('').prop('disabled', true);
                }

                return input;
            });
        }

        return outTable;
    };

    /**
     * Get select values
     * @param {object} params
     * @return {object}
     */
    this.getSelectByParams = function (params) {
        var g = this,
            select = $('<select />');

        $.each(params, function (index, item) {
            if ($.isArray(item) || typeof item === "object" || item == null) {
                select = g.appendToSelect(select, index);

            } else {
                select = g.appendToSelect(select, item);
            }
        });

        return select;
    };

    this.renderDocumentTypeData = function (inSelector, params, stack) {
        var g = this,
            attrProperty = ['alias_field'],
            table = this.getPrepareTable(params, stack, []),
            selector = $(inSelector);

        var flag = false;
        table = this.addColumnToTable(table, {index: -1}, function (tr) {
            var trData = $(tr).attr('data-' + attrProperty),
                input = $('<input />', {
                    type: 'checkbox',
                    name: trData,
                    value: trData,
                    "class": g.param.outTable.checkbox
                });

            if ($.inArray(trData, stack['layout_configuration']['params']) >= 0) {
                input.prop('checked', true);
                flag = true;
            } else $(tr).find('input:not(:checkbox), select, button').data('internationalization', null).val('').prop('disabled', true);

            return input;
        });

        if (flag) table.find('input[type="checkbox"]:not(:checked)').prop('disabled', true);

        table.addClass(g.param.outTable.className);
        selector.empty();

        if (table) {
            table = this.appendFindColumnToTable(table);
            selector.append(table);
        } else {
            this.setInfo(this.param.configureBlock, 'info', 'Has no params!');
            return null;
        }
    };

    /**
     * Render data for selected type
     * @param {Array} data
     * @param {creatorApp.templateStack} stack
     */
    this.renderDataByType = function (data, stack) {
        var inSelector = this.param.configureBlock;

        switch (stack['layout_type']) {
            case SECTION_TYPE_EDIT:
                $(inSelector).html('');
                break;
            case SECTION_TYPE_GRID:
                this.renderTableTypeData(inSelector, data, stack);
                break;
            case SECTION_TYPE_CHART_LINE:
            case SECTION_TYPE_CHART_BAR_HORIZONTAL:
            case SECTION_TYPE_CHART_BAR_VERTICAL:
            case SECTION_TYPE_CHART_DOUGHNUT:
            case SECTION_TYPE_CHART_PIE:
                this.renderChartTypeData(inSelector, data, stack);
                break;
            case SECTION_TYPE_DOCUMENT:
                this.renderDocumentTypeData(inSelector, data, stack);
                break;
        }
    };

    /**
     * Render sections settings modal
     * @param {string} library
     * @param {creatorApp.templateStack} stack
     */
    this.initConfig = function (library, stack) {
        var g = this;
        if (library && stack['data_source_get']) {
            g.getFunctionParams(library, stack['data_source_get'], function (data) {
                g.renderDataByType(data, stack);
            });
        } else {
            $(g.param.configureBlock).html('');
        }
    };

    /**
     * Added fields section settings to tab template
     */
    this.addFieldsToTemplate = function () {
        var g = this;

        $.each(g.templateLayout, function (i, item) {
            g.templateLayout[i]['layout_fields'] = fieldsConstructor.getTemplate(item.row_num, item.col_num);
        });
    };

    this.validateSections = function () {
        var g = this,
            isValid = true;

        $(g.param.panelBlock).each(function () {
            var row = $(this).attr('data-row'),
                col = $(this).attr('data-col');

            if (!g.getTemplate(row, col)) {
                isValid = false;
                return false;
            }
        });

        return isValid;
    };

    this.updateSectionsInfoForDocument = function () {
        var g = this,
            bodyClass = '.panel-body';

        $.each(screenCreator.templateLayout, function (index, val) {
            if (val['layout_type'] == SECTION_TYPE_DOCUMENT) {
                var panelElement = g.param.panelBlock + '[data-row="' + val['row_num'] + '"][data-col="' + val['col_num'] + '"]',
                    ul = $('<ul />');

                $.each(val['layout_configuration']['params'], function (i, label) {
                    var b = $('<b />', {html: 'Document field: '}),
                        li = $('<li />').append(b);

                    li.append(label);
                    ul.append(li);
                });

                $(panelElement).find(bodyClass).html(ul);
            }
        });
    };

    this.updateSectionsInfoForChart = function () {
        var g = this,
            bodyClass = '.panel-body';

        $.each(screenCreator.templateLayout, function (index, val) {
            if (ALL_CHARTS.indexOf(val['layout_type']) != -1) {
                var panelElement = g.param.panelBlock + '[data-row="' + val['row_num'] + '"][data-col="' + val['col_num'] + '"]',
                    ul = $('<ul />');

                if (PIE_CHARTS.indexOf(val['layout_type']) != -1) {
                    $.each(val['layout_configuration']['labels'], function (param, label) {
                        var b = $('<b />', {html: label + ': '}),
                            li = $('<li />').append(b);

                        li.append(val['layout_configuration']['format_type'][param]);
                        ul.append(li);
                    });
                } else if (LINE_CHARTS.indexOf(val['layout_type']) != -1){
                    $.each(val['layout_configuration']['params'], function (side) {
                        $.each(val['layout_configuration']['params'][side], function (i, param) {
                            var paramLabel = (val['layout_configuration']['labels'][param]) ? val['layout_configuration']['labels'][param] : param,
                                b = $('<b />', {html: side + '.' + paramLabel + ': '}),
                                li = $('<li />').append(b);

                            li.append(val['layout_configuration']['format_type'][param]);
                            ul.append(li);
                        });
                    });
                }

                $(panelElement).find(bodyClass).html(ul);
            }
        });
    };

    /**
     * Main update sections method, after saving settings
     */
    this.updateSectionsInfo = function () {
        var g = this,
            headClass = '.panel-heading h3',
            bodyClass = '.panel-body';

        $(bodyClass).html('');
        $(g.param.settingBtn).show();

        $.each(screenCreator.templateLayout, function (index, val) {
            var panelElement = g.param.panelBlock + '[data-row="' + val['row_num'] + '"][data-col="' + val['col_num'] + '"]';

            if (!val['layout_label']) val['layout_label'] = 'Unnamed';
            $(panelElement).find(headClass).html(val['layout_label']);
            $(panelElement).find(fieldsConstructor.params.button).hide();
            $(panelElement).find(fieldsConstructor.params.labelFieldConfig).hide();
            $(panelElement).find(fieldsConstructor.params.buttonFieldConfig).hide();
        });

        this.updateSectionsInfoForDocument();
        this.updateSectionsInfoForChart();
        fieldsConstructor.updateSectionsInfo();
        tableConstructor.updateSectionsInfo();
    };

    /**
     * Main save template method
     * @param {creatorApp.templateStack} stack
     * @return {boolean}
     */
    this.saveTemplate = function (stack) {
        var isNotValid = false;

        screenCreator.setInfo(this.param.configureBlock, false);

        if (!stack['row_num'] || !stack['col_num'] || !stack['data_source_get']) {
            alert("Undefined error!");
            return false;
        }
        if (stack['layout_type'] == SECTION_TYPE_GRID) {
            if (!stack['layout_table']['count'] || !stack['layout_table']['show_type'] || !stack['layout_table']['label_orientation']) {
                alert("Table config inputs is required!");
                return false;
            }
        }

        try {
            $.each(stack['layout_configuration']['params'], function (index, item) {
                if (stack['layout_configuration']['labels'][item] == "") {
                    isNotValid = true;
                    alert("All checked params must contain a label.");
                    return false;
                }

                if (stack['layout_type'] == SECTION_TYPE_CHART_PIE) {
                    if (stack['layout_configuration']['format_type'][item] == "") {
                        isNotValid = true;
                        alert("All checked params must contain a format_type.");
                        return false;
                    }
                }
            });
        } catch (e) {
            isNotValid = true;
        }
        if (isNotValid) return false;

        var g = this,
            flag = false;

        switch(stack['layout_type']) {
            case SECTION_TYPE_EDIT:
                stack['layout_table'] = $.extend(true, {}, g.templateStack.layout_table);
                stack['layout_configuration'] = $.extend(true, {}, g.templateStack.layout_configuration);
                break;
            case SECTION_TYPE_GRID:
                stack['layout_fields'] = $.extend(true, {}, g.templateStack.layout_fields);
                break;
            case SECTION_TYPE_CHART_PIE:
            case SECTION_TYPE_CHART_LINE:
            case SECTION_TYPE_DOCUMENT:
                stack['layout_fields'] = $.extend(true, {}, g.templateStack.layout_fields);
                stack['layout_table'] = $.extend(true, {}, g.templateStack.layout_table);
                stack['layout_formatting'] = $.extend(true, {}, g.templateStack.layout_formatting);
                break;
        }

        $.each(g.templateLayout, function (index, value) {
            if (value.row_num == stack['row_num'] && value.col_num == stack['col_num']) {
                g.templateLayout[index] = stack;
                flag = true;
            }
        });

        if (!flag) g.templateLayout.push(stack);

        this.setInfo(this.param.configureBlock, 'success', 'Template has been saved');
        this.updateSectionsInfo();
    };

    /**
     * Getting settings for section by row and column of section
     * @param {string} row
     * @param {string} col
     * @return {boolean|Object}
     */
    this.getTemplate = function (row, col) {
        var g = this,
            result = false;

        $.each(g.templateLayout, function (index, value) {
            if (value.row_num == row && value.col_num == col) {
                result = value;
                return false;
            }
        });

        return result;
    };

    this.getFormatting = function (row, col) {
        var g = this,
            result = {};

        $.each(g.templateLayout, function (index, value) {
            if (value.row_num == row && value.col_num == col && value.layout_formatting) {
                $.each(value.layout_formatting, function(i, item) {
                    result[item.name] = item.value;
                });
                return false;
            }
        });

        return result;
    };

    /**
     * Set value for radio button group
     * @param {string} element - Selector of object
     * @param {string|boolean} [val]
     */
    this.radioButtonSetValue = function (element, val) {
        $(element).each(function () {
            if ($(this).val() == val) $(this).prop('checked', true);
            else $(this).prop('checked', false);
        });
    };

    /**
     * Getting library function list
     * @param {string} libName
     * @param {string} [direction] - Type of function (GETTER, SETTER, MULTI-SEARCH)
     * @param {getLibListCallback} callback
     * @param {string|null} [funcType]
     * @param {boolean} [noCache]
     * @return {boolean}
     */
    this.getLibFunctionList = function (libName, direction, callback, funcType, noCache) {
        var g = this,
            cacheData = sessionStorage.getItem(libName + '__func_list' + (direction ? '_' + direction : ''));

        if (cacheData && !noCache) {
            cacheData = $.parseJSON(cacheData);
            return (typeof callback == "function") ? callback(cacheData) : false;
        }

        if (!funcType) funcType = null;

        $.ajax({
            type: "POST",
            url: g.param.library.getFuncUrl,
            data: {library: libName, direction: direction, type: funcType}
        }).done(function (data) {
            if (typeof callback == "function") callback(data);

            if (data && !noCache) {
                sessionStorage.setItem(libName + '__func_list' + (direction ? '_' + direction : ''), JSON.stringify(data));
            }
        });
    };
    /**
     * @callback getLibListCallback
     * @param {Object} data
     */

    /**
     * Getting parameters of library function
     * @param {string} libName
     * @param {string} functionName
     * @param {getFuncParamCallback} callback
     * @return {*}
     */
    this.getFunctionParams = function (libName, functionName, callback) {
        var g = this,
            cacheData = sessionStorage.getItem(libName + '__' + functionName);

        if (cacheData) {
            cacheData = $.parseJSON(cacheData);
            return callback(cacheData);
        }

        g.displayLoader(this.param.configureBlock);
        $.ajax({
            type: "POST",
            url: this.param.library.getParamsUrl,
            data: {library: libName, function: functionName}
        }).done(function (data) {
            if (data) {
                callback(data);
                sessionStorage.setItem(libName + '__' + functionName, JSON.stringify(data));
            } else {
                g.resetModal();
            }
            g.displayLoader(g.param.configureBlock, true);
        });
    };
    /**
     * @callback getFuncParamCallback
     * @param {Array} data
     */

    /**
     * Getting library function extensions list
     * @param {string} libName
     * @param {string} funcName
     * @param {getExtensionsCallback} callback
     * @return {boolean}
     */
    this.getLibFunctionExtensions = function (libName, funcName, disableCache, callback) {
        var g = this,
            cacheData = sessionStorage.getItem(libName + '__' + funcName + '__extensions_list');

        if (cacheData && !disableCache) {
            cacheData = $.parseJSON(cacheData);
            return (typeof callback == "function") ? callback(cacheData) : false;
        }

        $.ajax({
            type: "POST",
            url: g.param.library.getFuncExtensionUrl,
            data: {library: libName, funcName: funcName}
        }).done(function (data) {
            if (typeof callback == "function") callback(data);

            if (data) {
                sessionStorage.setItem(libName + '__' + funcName + '__extensions_list', JSON.stringify(data));
            }
        });
    };
    /**
     * @callback getExtensionsCallback
     * @param {Object} data
     */

    /**
     * Remove or Show only that field of object
     * @param {Array} data
     * @param {string} type - Index of data items for searching
     * @param {Array} objectValue - Values of data items for searching
     * @param {boolean} [isRemove] - Set TRUE if you want to delete values, else returned the specified values
     * @return {Array}
     */
    this.restructureParams = function (data, type, objectValue, isRemove) {
        var result = [];

        $.each(data, function (index, item) {
            var matchString = item[type].toLowerCase(),
                matches = -1;

            $.each(objectValue, function (i, item) {
                item = item.toLowerCase();
                matches = matchString.indexOf(item);

                return matches == -1;
            });

            if ((isRemove && matches < 0) || (!isRemove && matches >= 0)) {
                result.push(item);
            }
        });

        return result;
    };

    this.filterPropertyParams = function (data, objectValue) {
        var result = [];
        $.each(data, function (index, item) {
            var property = {};
            $.each(objectValue, function () {
                property[this] = item[this];
            });
            result.push(property);
        });

        return result;
    };

    /**
     * Getting values of specified selector
     * @param {string} inputClass - Selector
     * @param {boolean} [isAssoc] - Set TRUE if you want to returned object
     * @return {Array}
     */
    this.getValues = function (inputClass, isAssoc) {
        var form = $(inputClass).serializeArray(),
            result = [];

        if (isAssoc) result = {};
        $.each(form, function (index, item) {
            if (isAssoc) result[item.name] = item.value;
            else result.push(item.value);
        });

        return result;
    };

    this.getValuesInternationalization = function (inputClass) {
        var form = $(inputClass).serializeArrayWithInternationalization(),
            result = {};

        $.each(form, function (index, item) {
            if (item['internationalization']) {
                result[item.name] = item['internationalization'];
            }
        });

        return result;
    };

    this.resetModal = function (inElement) {
        $(inElement).html('');
        $(this.param.library.functionSelect).val('');
        $(this.param.funcBlockWrapper).hide();
        $(this.param.submitFormBtn).hide();
        $(this.param.layoutLabel.input).val('').data('internationalization', null);

        this.setInfo(this.param.configureBlock, false);
        this.radioButtonSetValue(this.param.typeBtn, false);
    };

    /**
     * Display or hide loader image method
     * @param {string} inElement - Selector
     * @param {boolean} [isRemove] - Set TRUE for deleted loader image
     */
    this.displayLoader = function (inElement, isRemove) {
        if (isRemove) {
            $(inElement).find('.loader').remove();
        } else {
            $(inElement).append('<div class="loader"></div>');
        }
    };

    /**
     * Append info alert in modal.
     * @param {string} modalBody - Selector
     * @param {string|boolean} [type] - Type of bootstrap alert class
     * @param {string} [text]
     * @param {boolean} [isHideBody] - Set TRUE if you want remove body of modal
     */
    this.setInfo = function (modalBody, type, text, isHideBody) {
        $(modalBody).children().not(fieldsConstructor.params.additionalBlock.wrapper).show();
        $(modalBody).find('.alert').remove();

        if (type && text) {
            var alertObject = $('<div/>', {"class": 'alert alert-' + type, text: text});

            if (isHideBody) {
                $(modalBody).children().hide();
            }
            alertObject.appendTo($(modalBody));
        }
    };

    /**
     * Append options to select. Return select input type object
     * @param {object} select - Selector or object of select
     * @param {string|bool} [value]
     * @param {string} [text]
     * @return {object}
     */
    this.appendToSelect = function (select, value, text) {
        if (typeof select !== 'object') select = $(select);

        if (!text) text = value;
        select.append($('<option />', {
            value: value,
            text: text
        }));

        if (select.find('option[value=""]').length === 0) {
            select.prepend($('<option />', {
                value: '',
                text: '-- Select --',
                selected: true
            }));
        }

        return select;
    };

};

/**
 * Search settings in first step
 * @class firstStepApp
 */
var firstStepApp = function () {
    //Init after load page
    this.load = function () {
        var me = this;
        $(document).ready(function () {
            me.bindLoadEvents();
            me.init();
        });
    };

    this.param = {
        searchFunctionTemplate: '.search-function-config',
        nextStepBtn: '.next-step-btn',
        saveButtonElement: '#setting-library-modal .btn-save-settings',
        customPk: {
            table: 'custom-pk-table',
            MSTable: 'custom-ms-pk-table',
            input: '.custom-pk-table input[type="checkbox"]',
            MSInput: '.custom-ms-pk-table input[type="checkbox"]'
        },
        inParams: {
            table: 'inparam-table',
            MSTable: 'inparam-ms-table',
            input: '.inparam-table input[type="checkbox"]',
            MSInput: '.inparam-ms-table input'
        },
        configureBlock: '#setting-library-modal .render-configure',
        libraryFunction: {
            input: '.library-first-step',
            functionInput: '.search-function-name',
            block: '.field-tabform-functions',
            settings: '.library-first-step-settings'
        },
        searchFunction: {
            libraryInput: '.library-fs-modal-name',
            FunctionInput: '.library-fs-function-name',
            labelInput: '.search-function-label'
        },
        extensionButton: '.extension-settings-button',
        extensionModalBody: '#extensions-modal .modal-body',
        extensionSaveButton: '#extensions-modal .btn-save-extensions',
        extensionInput: '.extensions-config',
        jsTree: 'jstree',
        executeLibraryInput: '.execute-library-input',
        executeFunctionInput: '.execute-function-input',
        funcExtensionsCustom: 'func_extensions_custom',
        executeCustomInput: '.execute-custom-input',
        searchConfig: {
            modal: '#search-configuration-modal .modal-body',
            button: '.search-configuration-button',
            querySelect: '#search-block-select',
            saveButton: '#btn-save-search-configuration',
            template: '.search-custom-query'
        },
        searchConfigRadio: '.search-configuration-radio',
        useAliasFramework: '.is-use-alias-framework',
        aliasFrameworkFunctionsBlock: '.alias-framework-functions',
    };

    this.templateFunctionName = null;
    this.templateFunctionConfig = null;
    this.templateSearchCustomQuery = null;
    this.customQueryParams = null;

    this.isUseAliasFramework = true;

    const IS_SIMPLE_SEARCH = 'simple';
    const IS_CUSTOM_SEARCH = 'custom';

    this.init = function () {
        var g = this,
            config = $(g.param.searchFunctionTemplate).val(),
            libName = $(g.param.libraryFunction.input).val(),
            configCustomQuery = $(g.param.searchConfig.template).val();

        if (!libName) {
            $(g.param.extensionButton).prop('disabled', true);
            $(g.param.searchConfigRadio).prop('disabled', true);
        } else {
            this.getLibFunctionList(libName, 'MULTI-SEARCH');
            this.updateAliasFrameworkFunctions(libName);
        }
        if (config && (g.templateFunctionConfig = $.parseJSON(config))) {
            g.templateFunctionName = g.templateFunctionConfig.data_source_get;

            if (!g.isUseAliasFramework) {
                $.each(g.templateFunctionConfig.func_inparam_configuration, function (i, item) {
                    var subIndex1 = item.lastIndexOf('.'),
                        subIndex2 = item.lastIndexOf(':');
                    if (subIndex1 >= 0) g.templateFunctionConfig.func_inparam_configuration[i] = item.substr(subIndex1 + 1, item.length);
                    else if (subIndex2 >= 0) g.templateFunctionConfig.func_inparam_configuration[i] = item.substr(subIndex2 + 1, item.length);
                });

                $.each(g.templateFunctionConfig.pk_configuration, function (i, item) {
                    var subIndex1 = item.lastIndexOf('.'),
                        subIndex2 = item.lastIndexOf(':');
                    if (subIndex1 >= 0) g.templateFunctionConfig.pk_configuration[i] = item.substr(subIndex1 + 1, item.length);
                    else if (subIndex2 >= 0) g.templateFunctionConfig.pk_configuration[i] = item.substr(subIndex2 + 1, item.length);
                });
            }

            g.getFunctionParams(libName, g.templateFunctionName, g.templateFunctionConfig.func_inparam_configuration, g.templateFunctionConfig.pk_configuration);
            $(g.param.searchConfigRadio).filter('[value="' + IS_SIMPLE_SEARCH + '"]').prop('checked', true);
            $(g.param.libraryFunction.functionInput).prop('disabled', false);
        }

        if (configCustomQuery && (g.templateSearchCustomQuery = $.parseJSON(configCustomQuery))) {
            $(g.param.searchConfigRadio).filter('[value="' + IS_CUSTOM_SEARCH + '"]').click();
        } else {
            $(g.param.searchConfig.querySelect).val('').prop('disabled', true).trigger('change');
        }

        $(g.param.aliasFrameworkFunctionsBlock).find('select').prop('disabled', !g.isUseAliasFramework);
        if (g.isUseAliasFramework) {
            $(g.param.aliasFrameworkFunctionsBlock).removeClass('hidden')
        } else {
            $(g.param.aliasFrameworkFunctionsBlock).addClass('hidden')
        }

        var executeLibrary = $(fieldsConstructor.params.executeLibraryInput).val();
        fieldsConstructor.initExecuteFunctions(executeLibrary);
    };

    this.updateAliasFrameworkFunctions = function (library) {
        var g = this;

        screenCreator.getLibFunctionList(library, 'SETTER', function (data) {
            var block = $(g.param.aliasFrameworkFunctionsBlock),
                inputs = {
                    'update': block.find('.alias-framework-func-update'),
                    'delete': block.find('.alias-framework-func-delete'),
                    'insert': block.find('.alias-framework-func-insert')
                };

            $.each(inputs, function (funcType, select) {
                select.html('');
                if (data) {
                    $.each(data, function (i, item) {
                        screenCreator.appendToSelect(select, item['func_name'], item['func_name'] + ((item['func_descr']) ? ': ' + item['func_descr'] : ''));
                    });

                    select.val(select.attr('data-value'));
                }
            });
        });
    };

    //bringing json to the view to display the JsTree
    this.traverseJson = function(o, isChild, pathArray, pathCount, count) {
        var i;
        var name;
        var res = [];
        var isEmptyObject = false;
        if (pathArray.length == 0) {
            pathCount = null;
        }
        for (var k in o) {
            var jsonObj = {};
            jsonObj.state = {};
            i = o[k];
            if (typeof i == 'string') {
                isChild = false;
                jsonObj.text = i;
                if (pathArray.indexOf(i) != -1) {
                    pathCount--;
                } if (pathCount - 1 == 0) {
                    jsonObj.state = {};
                    jsonObj.state.opened = true;
                    jsonObj.state.selected = true;
                    pathCount = pathArray.length;
                }
            } else {
                var obj = Object.values(i);
                name = Object.keys(i)[0];
                if (obj.length > 0) {
                    if (pathArray.indexOf(name) != -1) {
                        pathCount--;
                    } if (pathCount - 1 == 0) {
                        jsonObj.state.opened = true;
                        jsonObj.state.selected = true;
                        pathCount = pathArray.length;
                    }
                    jsonObj.text = name;
                    var value = obj[0];
                    var childrens = [];
                    for (var j in value) {
                        if (typeof value[j] === 'object') {
                            var ob = [value[j]];

                            childrens.push(this.traverseJson(ob, true, pathArray, pathCount, count));
                            value = Object.values(i)[0];
                        }  else if (typeof value[j] !== 'object' && typeof value[j] !== 'function') {
                            var settings = {};
                            if (pathArray.indexOf(value[j].toString()) != -1) {
                                pathCount--;
                            } if (pathCount - 1 == 0) {
                                settings.state = {};
                                settings.state.opened = true;
                                settings.state.selected = true;
                                pathCount =  pathArray.length;
                            }
                            settings.text = value[j];
                            childrens.push(settings);
                        }
                    }
                    jsonObj.children = childrens;
                } else {
                    isEmptyObject = true;
                }
            }
            if (!isEmptyObject) {
                if (!isChild) {
                    res.push(jsonObj);
                } else {
                    return jsonObj;
                }
            } else {
                isEmptyObject = false;
            }

        }
        return res;
    };

    this.jsonToConfig = function (config) {
        var arrayResult = [];
        $.each(config, function(i,item) {
            var pathArray = [];
            var result = [];
            if (item) {
                var key = i;
                while (true) {
                    if  (typeof(item) == 'object') {
                        pathArray.push(key);
                        key = Object.keys(item)[0];
                        item = item[key];
                    } else {
                        pathArray.push(key);
                        pathArray.push(item);
                        break;
                    }
                }
            }
            $.each(pathArray, function(i, item) {
                result.push(item);
            });
            arrayResult.push(result);
        });
        return arrayResult;
    };

    this.registerCustomQueryParams = function (object) {
        this.customQueryParams = object;
    };

    //Method with Events
    this.bindLoadEvents = function () {
        var g = this,
            inParam = [],
            customPk = [],
            label, libName, functionName;
        $(document)
            //Selected library
            .on('change', g.param.libraryFunction.input, function () {
                var value = $(this).val();

                $(g.param.extensionButton).prop('disabled', !value);
                if (value) {
                    $(g.param.searchConfigRadio).prop('disabled', false);
                    $(g.param.searchConfigRadio).filter('[value="' + IS_SIMPLE_SEARCH + '"]').click();
                    g.getLibFunctionList(value, 'MULTI-SEARCH');
                    g.updateAliasFrameworkFunctions(value);
                } else {
                    $(g.param.searchConfigRadio).prop('checked', false);
                    $(g.param.searchConfigRadio).prop('disabled', true);
                    $(g.param.searchConfig.querySelect).val('').prop('disabled', true).trigger('change');
                    $(g.param.libraryFunction.functionInput).val('').prop('disabled', true).trigger('change');
                }

                $(g.param.extensionInput).val('null');
            })
            //Click on search settings button
            .on('click', g.param.libraryFunction.settings, function () {
                libName = $(g.param.libraryFunction.input).val();
                functionName = $(g.param.libraryFunction.functionInput).val();

                $(g.param.searchFunction.libraryInput).val(libName);
                $(g.param.searchFunction.FunctionInput).val(functionName);

                if (g.templateFunctionName != functionName) {
                    inParam = [];
                    customPk = [];
                    label = '';
                } else if (g.templateFunctionConfig) {
                    inParam = g.templateFunctionConfig.func_inparam_configuration;
                    customPk = g.templateFunctionConfig.pk_configuration;
                    label = g.templateFunctionConfig.field_label;
                }
                $(g.param.searchFunction.labelInput).val(label);

                g.getFunctionParams(libName, functionName, inParam, customPk);
            })
            //Click on save search settings button
            .on('click', g.param.saveButtonElement, function () {
                inParam = screenCreator.getValues(g.param.inParams.input);
                customPk = screenCreator.getValues(g.param.customPk.input);
                label = $(g.param.searchFunction.labelInput).val();

                g.saveTemplate(functionName, inParam, customPk, label);
            })
            //Checked checkbox in params table of section settings modal
            .on('change', g.param.inParams.input, function () {
                if ($(this).prop('checked')) {
                    $(g.param.inParams.input).not(this).prop('disabled', true);
                } else {
                    $(g.param.inParams.input).prop('disabled', false);
                }
            })
            .on('click', g.param.extensionButton, function (e) {
                $(g.param.extensionModalBody + " .alert").hide();
                $.each($('.jstree'), function (i, item) {
                    var jsTreeId = item.id;
                    $("#" + jsTreeId).empty();
                });
                $(g.param.extensionModalBody).append("<div class='loader'></div>");
                $('#extensions-modal').data('extensions', $(this).data('extensions'));
                var t = $(this),
                    extensionClass = '.'+$('#extensions-modal').data('extensions');

                if (t.parent().parent().find(g.param.executeCustomInput).val() != '""' || !$(e.target).hasClass('execute-btn')) {
                    if (!$(extensionClass).val()) {
                        $(extensionClass).val('""');
                    }
                    var config = JSON.parse($(extensionClass).val()),
                        modalBody = $(g.param.extensionModalBody),
                        dataLength = 0,
                        currentLoadedExtensions = 0,
                        currentFuncType = t.data('func-type'),
                        extensionMethod = t.data('extension-method'),
                        requestFuncType;

                    if (currentFuncType == 'Search') {
                        requestFuncType = 'MULTI-SEARCH';
                    } else if (currentFuncType == 'GetList') {
                        requestFuncType = 'GETTER';
                    } else {
                        requestFuncType = 'SETTER';
                    }

                    if(t.data('type') === 'extension') {
                        libName = t.parent().parent().find(g.param.executeLibraryInput).val().replace(/\"/g, '');
                        var funcName = t.parent().parent().find(g.param.executeFunctionInput).val().replace(/\"/g, '');
                        var customName = t.parent().parent().find(g.param.executeCustomInput).val().replace(/\"/g, '');
                        modalBody.html($('<div />', {class: 'loader', style: 'margin-bottom: 10px'}));
                        dataLength = 1;
                        var obj = {};
                        obj['func_name'] = funcName;
                        g.buildExtensionTree([obj], libName, customName, extensionMethod, modalBody, g, config, currentLoadedExtensions, true);
                    } else {
                        libName = t.parent().parent().find(g.param.libraryFunction.input).val();
                        screenCreator.getLibFunctionList(libName, requestFuncType, function (data) {
                            if (data) {
                                g.buildExtensionTree(data, libName, customName, extensionMethod, modalBody, g, config, currentLoadedExtensions, false);
                            }
                        }, currentFuncType, true);
                    }
                } else {
                    screenCreator.displayLoader(g.param.extensionModalBody, true);
                    screenCreator.setInfo(g.param.extensionModalBody, 'danger', 'Can\'t find data source functions for this library');
                }
            })
            .on('click', g.param.extensionSaveButton, function () {
                var resJson = g.configToJson();
                if (resJson) {
                    var extensions = $('#extensions-modal').data('extensions');
                    $('.' + extensions).val(JSON.stringify(resJson));
                }
                screenCreator.setInfo(g.param.extensionModalBody, 'success', 'Template has been saved');
            })
            //Click on search settings button
            .on('click', g.param.searchConfig.button, function () {
                var fakePK = $(g.param.searchConfig.querySelect).val(),
                    params = g.customQueryParams[fakePK],
                    table = g.getQueryParamsTable(params);

                $(g.param.searchConfig.modal).html('');
                if (g.isUseAliasFramework) {
                    var PKTable = g.getQueryPKsTable(params, ['alias_field']);
                    var multiSearchPKs = [];
                    if (g.templateSearchCustomQuery && g.templateSearchCustomQuery['alias_query_pk']) {
                        multiSearchPKs = g.templateSearchCustomQuery['alias_query_pk'];
                    }

                    if (PKTable) {
                        PKTable = screenCreator.addColumnToTable(PKTable, {index: 0}, function (tr) {
                            var trData = $(tr).attr('data-alias_field'),
                                input = $('<input />', {type: 'checkbox', name: trData, value: trData});
                            if ($.inArray(trData, multiSearchPKs) >= 0) input.prop('checked', true);
                            return input;
                        });

                        PKTable.addClass(g.param.customPk.MSTable);
                        // // $(this.param.configureBlock).html('');
                        //
                        $(g.param.searchConfig.modal).append($('<h3 />', {text: 'PKs for alias framework'}));
                        $(g.param.searchConfig.modal).append(PKTable);
                    } else {
                        screenCreator.setInfo(g.param.searchConfig.modal, 'warning', "PK's is not configured in the custom query");
                    }
                }

                $(g.param.searchConfig.modal).append(table);

                if (!table) {
                    screenCreator.setInfo(g.param.searchConfig.modal, 'warning', 'Has no search params');
                } else {
                    table.addClass(g.param.inParams.MSTable);
                }
            })
            .on('change', g.param.searchConfig.querySelect, function () {
                var value = $(this).val(),
                    selectDisabled = $(this).prop('disabled'),
                    buttonDisabled = (!value) || selectDisabled;
                $(g.param.searchConfig.button).prop('disabled', buttonDisabled);
                $(this).prop('required', !selectDisabled);
            })
            .on('click', g.param.searchConfig.saveButton, function () {
                var pk = $(g.param.searchConfig.querySelect).val(),
                    customPk = screenCreator.getValues(g.param.customPk.MSInput),
                    inParam = $(g.param.searchConfig.modal).find(g.param.inParams.MSInput).serializeArray();


                g.saveCustomQueryTemplate(pk, customPk, inParam);
            })
            .on('change',  g.param.libraryFunction.functionInput, function () {
                var value = $(this).val(),
                    selectDisabled = $(this).prop('disabled'),
                    buttonDisabled = (!value) || $(this).prop('disabled');
                $(g.param.libraryFunction.settings).prop('disabled', buttonDisabled);
                $(this).prop('required', !selectDisabled);
            })
            .on('change', g.param.searchConfigRadio, function () {
                var value = $(this).val();
                if (value == IS_CUSTOM_SEARCH) {
                    $(g.param.libraryFunction.functionInput).prop('disabled', true).trigger('change');
                    $(g.param.searchConfig.querySelect).prop('disabled', false).trigger('change');
                } else if (value == IS_SIMPLE_SEARCH) {
                    $(g.param.libraryFunction.functionInput).prop('disabled', false).trigger('change');
                    $(g.param.searchConfig.querySelect).prop('disabled', true).trigger('change');
                }
            })
            .on('change', g.param.useAliasFramework, function () {
                $(g.param.aliasFrameworkFunctionsBlock).find('select').prop('disabled', !g.isUseAliasFramework);

                if (!g.isUseAliasFramework) {
                    g.templateFunctionConfig['pk_configuration'] = null;
                    $(g.param.searchFunctionTemplate).val(JSON.stringify(g.templateFunctionConfig));
                    $(g.param.aliasFrameworkFunctionsBlock).addClass('hidden');
                } else {
                    $(g.param.aliasFrameworkFunctionsBlock).removeClass('hidden')
                }
            });
    };

    this.getQueryParamsTable = function (params) {
        var table = $('<table />', {class: 'table table-bordered'}),
            match = params['query_params'].split(', '),
            tr = $('<tr />'),
            thead = $('<thead />').html(tr),
            tbody = $('<tbody />').html(tr),
            me = this;

        tr.append($('<th />', {text: 'Param'}));
        tr.append($('<th />', {text: 'Label'}));
        thead.html(tr);

        if (match && match[0]) {
            $.each(match, function (i, item) {
                var tr = $('<tr />'),
                    input = $('<input />', {type: 'text', value: null, name: item, class: 'form-control'});

                tr.append($('<td />', {text: item}));
                tr.append($('<td />').append(input));

                tbody.append(tr);
            });

            if (me.templateSearchCustomQuery && me.templateSearchCustomQuery['query_pk'] == params['pk']) {
                $.each(me.templateSearchCustomQuery['query_params'], function (i, item) {
                    tbody.find('*[name="' + item.name + '"]').val(item.value);
                });
            }

            return table.append(thead).append(tbody);
        }

        return null;
    };

    this.getQueryPKsTable = function (params, paramsTrNamed) {
        var table = $('<table />', {class: 'table table-bordered'}),
            match = params['query_pks'].replace(' ', '').split(','),
            tr = $('<tr />'),
            thead = $('<thead />').html(tr),
            tbody = $('<tbody />').html(tr),
            me = this;

        tr.append($('<th />', {text: 'PK'}));
        thead.html(tr);

        if (match && match[0]) {
            $.each(match, function (i, item) {
                var tr = $('<tr />');
                tr.append($('<td />', {text: item}));
                if (paramsTrNamed) {
                    $.each(paramsTrNamed, function () {
                        tr.attr('data-' + this, item);
                    });
                }
                tbody.append(tr);
            });

            // if (me.templateSearchCustomQuery && me.templateSearchCustomQuery['query_pk'] == params['pk']) {
            //     $.each(me.templateSearchCustomQuery['query_params'], function (i, item) {
            //         tbody.find('*[name="' + item.name + '"]').val(item.value);
            //     });
            // }

            return table.append(thead).append(tbody);
        }

        return null;
    };

    this.buildExtensionTree = function (data, libName, customName, extensionMethod, modalBody, g, config, currentLoadedExtensions, isExecute) {
        if (data) {
            modalBody.html($('<div />', {class: 'loader', style: 'margin-bottom: 10px'}));
            var dataLength = Object.keys(data).length;
            var result = [];
            var count = 0;
            var num = 0;
            $.each(data, function (i, item) {
                if (isExecute) {
                    var libFunc = customName.split(';');

                    libName = libFunc[0];
                    item['func_name'] = customName.split(';')[1];
                }
                screenCreator.getLibFunctionExtensions(libName, item['func_name'], true, function (extensions) {
                    if (extensionMethod == 'pre' || extensionMethod == 'post') {
                        extensions = extensions['func_extensions_' + extensionMethod];
                    } else {
                        extensions = extensions[g.param.funcExtensionsCustom];
                        $.each(extensions, function (i, item) {
                            if (Object.keys(item)[0] === customName) {
                                extensions = item[customName]['func_extensions_' + extensionMethod];
                            }
                        });
                    }
                    //extensions = extensions[g.param.funcExtensionsCustom][customName]['func_extensions_' + extensionMethod];
                    if (typeof extensions == 'object' && extensions.length > 0) {
                        var pathArrayCount = 0;
                        var pathArray = g.jsonToConfig(config);
                        if (pathArray.length > 0) {
                            pathArrayCount = pathArray[0].length;
                            pathArray = pathArray[0];
                        }
                        var childs = g.traverseJson(extensions, false, pathArray, pathArrayCount, num++);
                        result.push({'text': item['func_name'], 'children': childs, 'state': {'disabled': true}});
                    } else {
                        currentLoadedExtensions++;
                        if (currentLoadedExtensions == dataLength) {
                            screenCreator.setInfo(g.param.extensionModalBody, 'danger', 'Can\'t find extensions for any data source', true);
                        }
                    }
                    if (++count == dataLength && result.length > 0) {
                        $.each(result, function (j, res) {
                            var jsTree = "<div id=" + g.param.jsTree + "_" + j + "></div>";
                            modalBody.append(jsTree);
                            $("#" + g.param.jsTree + "_" + + j).jstree({
                                "core" : {
                                    "multiple" : false,
                                    "animation" : 250,
                                    "data" : res,
                                    "themes":{
                                        "icons":false
                                    }
                                }
                            });

                        });

                        modalBody.find('.loader').remove();
                        modalBody.find('.alert').remove();
                    }
                });
            });
        } else {
            screenCreator.setInfo(g.param.extensionModalBody, 'danger', 'Can\'t find data source functions for this library');
        }
    };

    this.configToJson = function () {
        var arrayJson = [];
        var result = {};
        $.each($('.jstree'), function (i, item) {
            var jsTreeId = item.id;
            if (!$("#" + jsTreeId).jstree("get_selected", true).length) {
                return {};
            }
            var path = $("#" + jsTreeId).jstree().get_path($("#" + jsTreeId).jstree("get_selected", true)[0], '{/}').split('{/}');
            var resJson = {};
            for (var j = path.length; j > 1; j--) {
                if (path[j - 2] && !Object.keys(resJson).length) {
                    resJson[path[j - 2]] = path[j - 1];
                } else {
                    var temp = resJson;
                    resJson = {};
                    resJson[path[j - 2]] = temp;
                }
            }
            arrayJson.push(resJson);
        });
        $.each(arrayJson, function (i, item) {
            result[Object.keys(item)[0]] = Object.values(item)[0];
        });
        return result;
    };

    /**
     * Getting library functions list. Used of the same name method in creatorApp
     * @param {string} libName
     * @param {string} [direction]
     */
    this.getLibFunctionList = function (libName, direction) {
        var g = this;

        direction = (direction) ? direction : false;
        $(g.param.libraryFunction.block + ' .input-group').hide();
        screenCreator.displayLoader(g.param.libraryFunction.block + ' label');

        screenCreator.getLibFunctionList(libName, direction, function (data) {
            if (data && Object.keys(data).length > 0) {
                $(g.param.libraryFunction.block + ' .input-group').prop('disabled', false);
                $(g.param.libraryFunction.functionInput).html($('<option>', {value: '', text: '-- Select --'}));
                $.each(data, function (i, item) {
                    if (item['func_name'].search(/sub/i) < 0) {
                        $(g.param.libraryFunction.functionInput).append($('<option>', {
                            value: item['func_name'],
                            text: (item['func_descr']) ? item['func_name'] + ': ' + item['func_descr'] : item['func_name']
                        }));
                    }
                });
                $(g.param.libraryFunction.functionInput).val(g.templateFunctionName).trigger('change');
            }

            screenCreator.displayLoader(g.param.libraryFunction.block + ' label', true);
            $(g.param.libraryFunction.block + ' .input-group').show();
        });
    };

    /**
     * Save settings for search. Added template object to hide input in create/update form
     * @param {string} functionName
     * @param {Array} inParam - First table checked values
     * @param {Array} customPk - Primary keys for ALIAS FRAMEWORK
     * @param {string} label - Label of search
     */
    this.saveTemplate = function (functionName, inParam, customPk, label) {
        var g = this;

        screenCreator.setInfo(g.param.configureBlock, false);

        if (!functionName || inParam.length == 0) {
            alert("Function name can't be empty and one or more rows must be checked in the tables");
            return false;
        }

        g.templateFunctionConfig = {
            "func_inparam_configuration": inParam,
            "pk_configuration": null,
            "data_source_get": functionName,
            "field_label": label
        };
        if (g.isUseAliasFramework) {
            g.templateFunctionConfig['pk_configuration'] = customPk;
        }

        g.templateFunctionName = functionName;

        $(g.param.searchFunctionTemplate).val(JSON.stringify(g.templateFunctionConfig));

        screenCreator.setInfo(g.param.configureBlock, 'success', 'Template has been saved');
    };

    this.saveCustomQueryTemplate = function (pk, customPk, inParam) {
        screenCreator.setInfo(this.param.searchConfig.modal, false);
        this.templateSearchCustomQuery = {
            "query_params": inParam,
            "query_pk": pk,
            "alias_query_pk": customPk
        };

        $(this.param.searchConfig.template).val(JSON.stringify(this.templateSearchCustomQuery));
        screenCreator.setInfo(this.param.searchConfig.modal, 'success', 'Template has been saved');
    };

    /**
     * Getting parameters of library function. After getting successfully result, run callback function
     * @param {string} libName
     * @param {string} functionName
     * @param {Array} withInParams - First table checked values
     * @param {Array} customPk - Primary keys for ALIAS FRAMEWORK
     */
    this.getFunctionParams = function (libName, functionName, withInParams, customPk) {
        var g = this,
            cacheData = sessionStorage.getItem(libName + '__' + functionName);

        g.resetModal(g.param.configureBlock);

        if (cacheData) {
            cacheData = $.parseJSON(cacheData);
            return g.renderConfigTables(cacheData, withInParams, customPk);
        }

        screenCreator.displayLoader(this.param.configureBlock);
        $.ajax({
            type: "POST",
            url: screenCreator.param.library.getParamsUrl,
            data: {library: libName, function: functionName}
        }).done(function (data) {
            if (data) {
                g.renderConfigTables(data, withInParams, customPk);
                sessionStorage.setItem(libName + '__' + functionName, JSON.stringify(data));
            }
            screenCreator.displayLoader(g.param.configureBlock, true);
        });
    };

    /**
     * Append to configure block, table with specified parameters
     * @param {Array} params
     * @param {Array} withInParams - First table checked values
     * @param {Array} customPk - Primary keys for ALIAS FRAMEWORK
     */
    this.renderConfigTables = function (params, withInParams, customPk) {
        var g = this,
            inTable = screenCreator.getTable(params, ['alias_field']),
            PKTable = screenCreator.getTable(params, ['alias_field']);

        inTable = screenCreator.addColumnToTable(inTable, {index: 0}, function (tr) {
            var trData = $(tr).attr('data-alias_field'),
                input = $('<input />', {type: 'checkbox', name: trData, value: trData});

            if ($.inArray(trData, withInParams) >= 0) input.prop('checked', true);
            return input;
        });

        if (g.isUseAliasFramework) {
            PKTable = screenCreator.addColumnToTable(PKTable, {index: 0}, function (tr) {
                var trData = $(tr).attr('data-alias_field'),
                    input = $('<input />', {type: 'checkbox', name: trData, value: trData});

                if ($.inArray(trData, customPk) >= 0) input.prop('checked', true);
                return input;
            });
        }

        if (withInParams.length > 0) {
            inTable.find('input[type="checkbox"]').not(':checked').prop('disabled', true);
        }

        inTable.addClass(g.param.inParams.table);

        if (g.isUseAliasFramework) {
            PKTable.addClass(g.param.customPk.table);
        }

        $(this.param.configureBlock).html('');
        if (g.isUseAliasFramework) {
            $(this.param.configureBlock).append($('<h3 />', {text: 'PKs for alias framework'}));
            $(this.param.configureBlock).append(PKTable);
        }
        $(this.param.configureBlock).append($('<h3 />', {text: 'Parameter for search'}));
        $(this.param.configureBlock).append(inTable);
    };

    this.resetModal = function (inElement) {
        $(inElement).html('');
    };
};

/**
 * Fields settings class
 * @class fieldsConstructorApp
 */
var fieldsConstructorApp = function () {
    //Init after page load
    this.load = function () {
        var g = this;
        $(document).ready(function () {
            g.bindLoadEvents();
        });
    };

    this.params = {
        modalBody: '#fields-modal .modal-body',
        button: '.fields-constructor-btn',
        saveFieldsBtn: '.btn-save-fields',
        pullUpBtn: '.pull-up-btn-block',
        form: '.fields-constructor-form',
        type: '#field-type',
        length: '#field-length',
        label: '#field-label',
        tooltip: '#field-tooltip',
        labelOrientation: '#field-label-orientation',
        linkColumn: '#field-link-column',
        numRows: '#field-num-rows',
        keyField: '#field-key-field',
        listName: '#field-list-name',
        dataField: '#field-data-field',
        editType: '#field-edit-type',
        formatType: '#field-format-type',
        restrictCode: '#field-restrict-code',
        copyableInput: '#field-copyable-field',
        identifierInput: '#button-identifier',
        fieldSettingsBtn: {
            config: 'field-config-btn',
            remove: 'field-remove-btn'
        },
        commonJSTemplate: {
            remove: 'cjs-section-remove-btn'
        },
        pullup: {
            saveBtn: '.btn-save-pullup',
            table: '.table-pullup',
            radio: '.table-pullup input[name="radioButtonSelection"]'
        },
        fieldWidth: {
            type: '#field-width-type',
            value: '#field-width-value'
        },
        formatting: '#formatting-modal',
        additionalBlock: {
            wrapper: '.additional-fields-row',
            group: '.fields-modal-group-block',
            groupDataList: '.fields-modal-group-block #groups_list',
            documentGroup: '.fields-modal-document-group-block',
            documentFamily: '#field-document-family',
            documentCategory: '.field-document-category',
            inlineSearch: '.fields-modal-custom-query',
            inlineSearchName: '.field-custom-query-pk',
            buttonExecute: '.fields-modal-type-button-execute',
            dropDown: '.field-dropdown-values',
            dropDownValues: '#dropdown-values-input',
            dynamicInputs: 'dynamic-inputs'
        },
        fieldWrapper: {
            row: 'fields-row-wrapper',
            main: 'field-wrapper',
            mainInner: 'field-wrapper-inner',
            icon: 'field-resize-icon'
        },
        modalJavaScript: '#js-edit-modal',
        jsFormatTable: '.js-edit-table',
        executeLibrary: '#execute_library',
        executeFunction: '#execute-function',
        executeCustom: '#execute-custom',
        executeCustomInput: '.execute-custom-input',
        executeBlock: '.execute-block',
        executeSave: '.execute-save',
        executeClose: '.execute-close',
        modalExecuteBody: '#execute-function-modal .modal-body',
        executeLibraryInput: '.execute-library-input',
        executeFunctionInput: '.execute-function-input',
        extensionFunctionBtn: '.extension-function-btn',
        extensionFunctionBlock: '.extension-function-block',
        screenLib: '#screenform-screen_lib',
        extensionsExecutePre: '.extensions-config-execute-pre',
        extensionsExecutePost: '.extensions-config-execute-post',
        executeFunctionModal: '#execute-function-modal .modal-body',
        externalLinkBlock: '.external-link-block',
        menuLinkField: '.field-link-menu',
        groupScreenLinkField: '.field-group-screen-link',
        screenLinkField: '.field-screen-link',
        settingsLinkField: '.field-settings-link',
        externalLinkError: 'external-link-error',
        fieldsModalTypeLink: '.fields-modal-type-link',
        typeLink: '.type-link',
        idFieldSpan: '#id-field span',
        labelFieldConfig: '.fields-label-config',
        buttonFieldConfig: '.fields-button-config',
        tableModalJavaScript: '#js-edit-table',
        onChangeTab: '.js-custom-onchange-tab',
        jsEditBtn: '.js-edit-btn',
        configureBlock: {
            main: '.field-configure-block',
            label: '.field-label-block',
            button: '.field-button-block',
            dependentField: '.dependent-field-configure-block'
        },
        fieldLabelDimension: 'field-label-center',
        fieldLabelWrapper: '.field-label-wrapper',
        fieldButtonAction: '#button-action',
        position: {
            widthInput: '#field-block-width',
            heightInput: '#field-block-height',
            rowInput: '#field-block-row',
            colInput: '#field-block-col',
        },
        fieldLeftLabelOrientation: 'field-left-label-orientation',
        labelWidthInput: '#field-label-width'
    };

    this.init = function () {
        var g = this;

        $.each(screenCreator.templateLayout, function (i, item) {
            if (item.layout_fields && item.layout_fields.length > 0) {
                $.each(item.layout_fields, function (j, value) {
                    g.saveTemplate(item.row_num, item.col_num, value);
                });
            }
        });

        var selectType = screenCreator.getSelectByParams(screenCreator.paramTypes),
            library = $(screenCreator.param.library.select).val();

        $(this.params.type).html(selectType.html());
        this.initExecuteFunctions(library);
    };

    this.initExecuteFunctions = function (library) {
        if (library) {
            this.addFunctionSelect(library.replace(/\"/g, ''), "GETTER", this.params.executeFunction);
            $(this.params.executeFunction).prop('disabled', false);
        }
    };

    //Check right for key fields
    this.initKeyField = function (row, col) {
        if (row != 0 && col != 0) $(this.params.keyField).prop('disabled', true);
        else $(this.params.keyField).prop('disabled', false);
    };

    this.fieldsData = [];
    this.functionParamsObject = null;
    this.buttonSubmitText = {
        update: 'Update',
        add: 'Add'
    };

    this.clearOption = function (deleteAllSelectors, selectID) {
        var selects;

        if (deleteAllSelectors) {
            selects = $(this.params.externalLinkBlock + " select").filter(function(){
                return $(this).data('serial-number') > 0;
            });
            $(this.params.externalLinkBlock).hide();
        } else {
            selects = $(this.params.externalLinkBlock + " select").filter(function(){
                return $(this).data('serial-number') > $(selectID).data('serial-number');
            });
        }

        $.each(selects, function (i, select){
            $(select)
                .find('option')
                .remove()
                .end()
                .append('<option value="">-- Select --</option>')
                .val('')
                .prop('disabled', true);
        });
    };

    //Method with events
    this.bindLoadEvents = function () {
        var g = this,
            row, col, lastConfigFieldObject

        $(document)
            //Click on add new field button
            .on('click', g.params.button, function () {
                $(g.params.configureBlock.button).hide().find('input, select').prop('disabled', true);
                $(g.params.configureBlock.label).hide();
                $(g.params.configureBlock.main).show();

                row = $(this).parents('.section-panel').attr('data-row');
                col = $(this).parents('.section-panel').attr('data-col');

                g.initKeyField(row, col);
                g.resetModal();
                $(g.params.saveFieldsBtn).text(g.buttonSubmitText.add);

                var template = screenCreator.getTemplate(row, col);
                if (!template.data_source_get) {
                    screenCreator.setInfo(g.params.modalBody, 'danger', 'First, configure', true);
                    $(g.params.saveFieldsBtn).hide();
                } else if (template.layout_type != "LIST") {
                    screenCreator.setInfo(g.params.modalBody, 'danger', 'Type of this section is not LIST', true);
                    $(g.params.saveFieldsBtn).hide();
                } else {
                    g.setDataFieldInfo($(screenCreator.param.library.select).val(), template.data_source_get);
                    $(g.params.saveFieldsBtn).show();
                }

                if (g.getTemplate(row, col)) $(g.params.linkColumn).prop('disabled', false);
                else $(g.params.linkColumn).prop('disabled', true);
            })
            //Click on save settings of field button
            .on('submit', g.params.form, function (event) {
                event.preventDefault();
                $('.spectrum-source').prop('disabled', true); //Don't include additional color type field to serialize

                g.transformFormsInJSTemplates();

                $("textarea[id^='js_edit']").each(function(i,j){
                    if (!isBase64($(j).val())) {
                        $(j).val(btoa(unescape(encodeURIComponent($(j).val()))));
                    }
                });
                
                var me = $(g.params.form),
                    serialize;

                if ($(g.params.saveFieldsBtn).text() == g.buttonSubmitText.add) {
                    var panelElement = $(screenCreator.param.panelBlock + '[data-row="' + row + '"][data-col="' + col + '"]'),
                        gridStackHeight = panelElement.find('.grid-stack').data('gsCurrentHeight');

                    $(g.params.form).find(g.params.position.rowInput).val(gridStackHeight);
                    serialize = $(me).serializeArrayWithInternationalization();

                    g.saveTemplate(row, col, serialize, me.find(g.params.type).val() == FIELD_TYPE_HIDDEN);
                    g.resetModal();

                    if (me.find(g.params.type).val() == FIELD_TYPE_LABEL) {
                        $(g.params.labelFieldConfig).trigger('click', true);
                    }
                    if (me.find(g.params.type).val() == FIELD_TYPE_BUTTON) {
                        $(g.params.buttonFieldConfig).trigger('click', true);
                    }

                    screenCreator.setInfo(g.params.modalBody, 'success', 'Field has been added');
                } else if ($(g.params.saveFieldsBtn).text() == g.buttonSubmitText.update) {
                    serialize = $(me).serializeArrayWithInternationalization();
                    g.findTemplateFieldByParentObject(lastConfigFieldObject, function (fieldDataIndex, fieldIndex) {
                        g.fieldsData[fieldDataIndex]['data'][fieldIndex] = serialize;
                        if (me.find(g.params.type).val() == FIELD_TYPE_HIDDEN) {
                            g.fieldsData[fieldDataIndex]['data'].move(fieldIndex, 0);
                        }
                    });
                    screenCreator.setInfo(g.params.modalBody, 'success', 'Field has been updated');
                }
                $('.spectrum-source').prop('disabled', false); //Don't include additional color type field to serialize
                $(g.params.formatting).find('.close').trigger('click');
                $(g.params.modalJavaScript).find('.close').trigger('click');
            })
            .on('change', g.params.form + ' input', function () {
                var name = $(this).attr('name'),
                    value = $(this).val();

                $(g.params.form).find('input[name="' + name + '"]').val(value);
            })
            .on('hidden.bs.modal', '#fields-modal, #setting-modal', function () {
                g.updateSectionsInfo();
            })
            //Selected type of field
            .on('change', g.params.type, function () {
                g.formatTypeChange($(this).val());
                $('.' + g.params.additionalBlock.dynamicInputs).remove();
            })
            //Selected row count of field with type select
            .on('change', g.params.numRows, function () {
                var value = $(this).val();

                if (value == 1) {
                    $(g.params.labelOrientation).find('option[value="LEFT"]').prop('disabled', true);
                    $(g.params.labelOrientation).find('option[value="TOP"]').prop('selected', true);
                } else {
                    $(g.params.labelOrientation).find('option').prop('disabled', false);
                }
            })
            //Selected params for field
            .on('change', g.params.dataField, function (e, param, notSetValue) {
                var value = $(this).val(),
                    paramType = '',
                    typeField = $(g.params.type);

                $.each(g.functionParamsObject, function (index, item) {
                    if (item['alias_field'] == value) {
                        paramType = item['field_type'];
                        return false;
                    }
                });

                if (value && lastConfigFieldObject) {
                    g.findTemplateFieldByParentObject(lastConfigFieldObject, function (fieldDataIndex, fieldIndex, row, col) {
                        var funcName = screenCreator.getTemplate(row, col).data_source_get,
                            htmlID = funcName.replace(/\.|,|:|;/gi, '-') + '--' + value.replace(/\.|,|:|;/gi, '-');

                        $(g.params.idFieldSpan).html(htmlID);
                    });
                }

                switch (paramType) {
                    case 'integer':
                    case 'double':
                        if (!notSetValue) typeField.val(FIELD_TYPE_NUMERIC);
                        $(g.params.type).find('option').prop('disabled', true);
                        $(g.params.type).find('option[value="' + FIELD_TYPE_NUMERIC + '"]').prop('disabled', false);
                        $(g.params.type).find('option[value="' + FIELD_TYPE_CHECKBOX + '"]').prop('disabled', false);
                        $(g.params.type).find('option[value="' + FIELD_TYPE_RADIO + '"]').prop('disabled', false);
                        $(g.params.type).find('option[value="' + FIELD_TYPE_HIDDEN + '"]').prop('disabled', false);
                        $(g.params.type).find('option[value="' + FIELD_TYPE_INLINE_SEARCH + '"]').prop('disabled', false);
                        $(g.params.type).find('option[value="' + FIELD_TYPE_LIST + '"]').prop('disabled', false);
                        $(g.params.type).find('option[value="' + FIELD_TYPE_MULTI_SELECT + '"]').prop('disabled', false);
                        break;
                    case 'list':
                        if (!notSetValue) typeField.val(FIELD_TYPE_LIST);
                        $(g.params.type).find('option').prop('disabled', true);
                        $(g.params.type).find('option[value="' + FIELD_TYPE_LIST + '"]').prop('disabled', false);
                        $(g.params.type).find('option[value="' + FIELD_TYPE_MULTI_SELECT + '"]').prop('disabled', false);
                        $(g.params.type).find('option[value="' + FIELD_TYPE_HIDDEN + '"]').prop('disabled', false);
                        $(g.params.type).find('option[value="' + FIELD_TYPE_DATALIST_RELATION + '"]').prop('disabled', false);
                        break;
                    case 'date':
                    case 'datetime':
                        if (!notSetValue) typeField.val(FIELD_TYPE_TEXT);
                        $(g.params.type).find('option').prop('disabled', true);
                        $(g.params.type).find('option[value="' + FIELD_TYPE_TEXT + '"]').prop('disabled', false);
                        $(g.params.type).find('option[value="' + FIELD_TYPE_HIDDEN + '"]').prop('disabled', false);
                        $(g.params.type).find('option[value="' + FIELD_TYPE_LIST + '"]').prop('disabled', false);
                        $(g.params.type).find('option[value="' + FIELD_TYPE_MULTI_SELECT + '"]').prop('disabled', false);
                        break;
                    default:
                        if (!notSetValue) typeField.val(FIELD_TYPE_TEXT);
                        $(g.params.type).find('option').prop('disabled', true);
                        $(g.params.type).find('option[value="' + FIELD_TYPE_TEXT + '"]').prop('disabled', false);
                        $(g.params.type).find('option[value="' + FIELD_TYPE_TEXTAREA + '"]').prop('disabled', false);
                        $(g.params.type).find('option[value="' + FIELD_TYPE_NUMERIC + '"]').prop('disabled', false);
                        $(g.params.type).find('option[value="' + FIELD_TYPE_CHECKBOX + '"]').prop('disabled', false);
                        $(g.params.type).find('option[value="' + FIELD_TYPE_RADIO + '"]').prop('disabled', false);
                        $(g.params.type).find('option[value="' + FIELD_TYPE_HIDDEN + '"]').prop('disabled', false);
                        $(g.params.type).find('option[value="' + FIELD_TYPE_LIST + '"]').prop('disabled', false);
                        $(g.params.type).find('option[value="' + FIELD_TYPE_MULTI_SELECT + '"]').prop('disabled', false);

                        $(g.params.type).find('option[value="' + FIELD_TYPE_DOCUMENT + '"]').prop('disabled', false); //TODO: change when api is returned values
                        $(g.params.type).find('option[value="' + FIELD_TYPE_INLINE_SEARCH + '"]').prop('disabled', false);
                        $(g.params.type).find('option[value="' + FIELD_TYPE_LINK + '"]').prop('disabled', false);
                        $(g.params.type).find('option[value="' + FIELD_TYPE_DATALIST + '"]').prop('disabled', false);
                        $(g.params.type).find('option[value="' + FIELD_TYPE_DATALIST_RELATION + '"]').prop('disabled', false);
                        $(g.params.type).find('option[value="' + FIELD_TYPE_WITH_DEPENDENT_FIELD + '"]').prop('disabled', false);
                        break;
                }
                g.formatTypeChange(typeField.val(), paramType, param);
            })
            .on('click', '.' + g.params.commonJSTemplate.remove, function (e) {
                e.preventDefault();
                $(this).parents('.field-wrapper').find('input').val('');
                $(this).parents('.field-wrapper').hide().removeClass('opened');
            })
            //Click on delete field button
            .on('click', '.' + g.params.fieldSettingsBtn.remove, function () {
                g.findTemplateFieldByParentObject(this, function (fieldDataIndex, fieldIndex) {
                    g.fieldsData[fieldDataIndex]['data'].splice(fieldIndex, 1);
                    g.updateSectionsInfo();
                });
            })
            //Click on setting field button
            .on('click', '.' + g.params.fieldSettingsBtn.config, function () {
                $(g.params.configureBlock.button).hide().find('input, select').prop('disabled', true);
                $(g.params.configureBlock.label).hide();
                $(g.params.configureBlock.main).show();

                lastConfigFieldObject = this;
                $(g.params.saveFieldsBtn).text(g.buttonSubmitText.update);

                g.resetModal();
                g.findTemplateFieldByParentObject(this, function (fieldDataIndex, fieldIndex, row, col) {
                    var libName = $(screenCreator.param.library.select).val(),
                        funcName = screenCreator.getTemplate(row, col).data_source_get;
                    $($.find('.CodeMirror')).each(function (i, obj) {
                        obj.CodeMirror.refresh();
                        obj.CodeMirror.setValue('');
                    });

                    g.setDataFieldInfo(libName, funcName, function () {
                        var menuSelectedElements = [],
                            type = false;

                        $.each(g.fieldsData[fieldDataIndex]['data'][fieldIndex], function (i, item) {
                            var findInput = $(g.params.form).find('*[name="' + item.name + '"]');
                            findInput.data('internationalization', (item['internationalization']) ? item['internationalization'] : null);

                            if (item.name == $(g.params.type).attr('name')) {
                                type = item.value;
                            }

                            if (findInput.hasClass('js-section-marker') && item.value) {
                                findInput.parents('.field-wrapper').show().addClass('opened');
                            }

                            if (item.name == $(g.params.formatType).attr('name')) {
                                $(g.params.type).trigger('change');
                            }
                            if (!screenCreator.isAliasFramework && item.name == $(g.params.dataField).attr('name')) {
                                var subIndex = item.value.lastIndexOf('.');
                                if (subIndex >= 0) item.value = item.value.substr(subIndex + 1, item.value.length);
                            }

                            if (findInput.attr('type') === 'checkbox') {
                                findInput.prop('checked', findInput.attr('value') == item.value);
                            } else if (findInput.prop("tagName") == 'TEXTAREA') {
                                var editor = findInput.parent().find('.CodeMirror')[0].CodeMirror;
                                if (item.value) {
                                    editor.clearHistory();
                                    editor.setValue(decodeURIComponent(escape(atob(item.value))));
                                }
                            } else if (item.name === 'field_link_menu') {
                                var elem =  $(g.params.modalBody).find('[data-external-link]');
                                for (var j = 0; j < elem.length; j++) {
                                    menuSelectedElements.push(g.fieldsData[fieldDataIndex]['data'][fieldIndex][i + j].value)
                                }
                            } else {
                                findInput.val(item.value);
                            }

                            if (item.name == $(g.params.additionalBlock.inlineSearchName).attr('name')) {
                                $(g.params.additionalBlock.inlineSearchName).trigger('change');
                            }
                        });

                        $(g.params.form).find('*[data-type="color"]').trigger('change');
                        $(g.params.numRows).trigger('change');
                        $(g.params.dataField).trigger('change', [true, true]);
                        $(g.params.fieldWidth.type).trigger('change');
                        screenCreator.radioButtonSetValue(g.params.pullup.radio, $(g.params.listName).val());

                        if (menuSelectedElements.length > 0) {
                            g.setDataFieldLink(menuSelectedElements);
                        }

                        if (type == FIELD_TYPE_LABEL) {
                            $(lastConfigFieldObject).parents(screenCreator.param.panelBlock + '[data-row="' + row + '"][data-col="' + col + '"]').find(g.params.labelFieldConfig).trigger('click', true);
                        }

                        if (type == FIELD_TYPE_BUTTON) {
                            $(lastConfigFieldObject).parents(screenCreator.param.panelBlock + '[data-row="' + row + '"][data-col="' + col + '"]').find(g.params.buttonFieldConfig).trigger('click', true);
                            $(g.params.fieldButtonAction).trigger('change');
                            g.fillCustomFunction(true);
                        }
                    });

                    g.initKeyField(row, col);
                });
            })
            .on('click', g.params.executeSave, function() {
                if (($(g.params.executeFunction).val() || $(g.params.executeFunction).val() === '') && ($(g.params.executeCustom).val() || $(g.params.executeCustom).val() === '' || $(g.params.executeCustom).val() === null)) {
                    if ($(g.params.executeCustom).val() === '' || $(g.params.executeCustom).val() === null) {
                        $(g.params.executeCustomInput).val('""');
                    } else {
                        $(g.params.executeCustomInput).val('"' + $(g.params.executeCustom).val() + '"')
                    }

                    if ($(g.params.executeFunction).val() === '') {
                        $(g.params.executeFunctionInput).val('""');
                    } else {
                        $(g.params.executeFunctionInput).val('"' + $(g.params.executeFunction).val() + '"');
                    }
                    screenCreator.setInfo(g.params.modalExecuteBody, 'success', 'Field has been updated');
                    if ($(g.params.executeFunction).val() === '' || $(g.params.executeCustom).val() === '') {
                        $(g.params.extensionsExecutePre).val('');
                        $(g.params.extensionsExecutePost).val('');
                    }

                    $(g.params.executeFunctionInput).change();
                    $(g.params.executeCustomInput).change();
                } else {
                    screenCreator.setInfo(g.params.modalExecuteBody, 'danger', 'Not all fields are filled');
                }
            })
            .on('click', g.params.executeClose, function() {
                if (!$(g.params.executeLibrary).val() && !$(g.params.executeFunction).val()) {
                    $(g.params.executeLibraryInput).val('');
                    $(g.params.executeFunctionInput).val('');
                }

            })
            .on('change', g.params.executeLibrary, function() {
                if ($(g.params.executeLibrary).val()){
                    g.addFunctionSelect($(g.params.executeLibrary).val(), "GETTER", g.params.executeFunction);
                    $(g.params.executeFunction).prop('disabled', false);
                } else {
                    $(g.params.executeFunction).prop('disabled', true);
                }
            })
            .on('change', g.params.executeFunction, function() {
                $(g.params.extensionsExecutePost).val('');
                $(g.params.extensionsExecutePre).val('');
                if ($(g.params.executeFunction).val()) {
                    $(g.params.executeFunctionInput).val($(g.params.executeFunction).val());
                    $(g.params.executeLibraryInput).val($(g.params.executeLibrary).val());
                    g.fillCustomFunction();
                } else {
                    $(g.params.executeCustom).val('');
                    $(g.params.executeCustom).html('');
                }
            })
            .on('change', g.params.executeCustom, function () {
                $(g.params.extensionsExecutePost).val('');
                $(g.params.extensionsExecutePre).val('');
            })
            .on('click', g.params.extensionFunctionBtn, function() {
                var value = $(this).parent().parent().find(g.params.executeFunctionInput).val();
                if (value) {
                    $(g.params.executeFunction + ' option[value=' + value + ']').prop('selected', true);
                }
                screenCreator.setInfo(g.params.executeFunctionModal, false);
            })
            .on('change', g.params.screenLib, function() {
                if ($(g.params.screenLib).val()) {
                    g.addFunctionSelect($(g.params.screenLib).val().replace(/\"/g, ''), "GETTER", g.params.executeFunction);
                    $(g.params.executeLibrary).html('');
                    $(g.params.executeLibrary).append($('<option />', {text: $(g.params.screenLib).val()}));
                    $(g.params.executeFunction).prop('disabled', false);
                }
            })
            .on('change', g.params.formatType, function () {
                g.clearOption(true);
                if ($(g.params.type).val() == FIELD_TYPE_LINK) {
                    if($(g.params.formatType).val() == FORMAT_TYPE_LINK_LOCAL) {
                        g.setDataFieldLink();
                    }
                }
            })
            .on('change', g.params.menuLinkField, function (e) {
                g.setDataFieldGroupScreen(null, e);
            })
            .on('change', g.params.groupScreenLinkField, function(e) {
                g.setDataFieldScreen(null, e);
            })
            .on('change', g.params.screenLinkField, function (e) {
                g.setDataFieldFunction(null, e);
            })
            //Save of settings
            .on('click', g.params.pullup.saveBtn, function () {
                var value = $(g.params.pullup.radio + ':checked').val();
                $(g.params.listName).val(value);
                $(tableConstructor.params.listName).val(value);
            })
            .on('change', g.params.fieldWidth.type, function () {
                var flag = $(this).val() === 'L' || !$(this).val();
                if (flag) $(g.params.fieldWidth.value).val('');
                if (!$(this).val()) $(g.params.fieldWidth.value).val('100%');

                $(g.params.fieldWidth.value).prop('disabled', flag);
            })
            .on('change', g.params.additionalBlock.documentFamily, function () {
                var categorySelector = $(g.params.additionalBlock.documentCategory),
                    value = $(this).val(),
                    categoryCompare = categorySelector.filter('[data-family="' + value + '"]');

                categorySelector.parent().find('label').hide();
                categorySelector.prop('disabled', true).hide();

                if (value && categoryCompare) {
                    categorySelector.parent().find('label').show();
                    categoryCompare.prop('disabled', false).show();
                }
            })
            .on('click', g.params.jsEditBtn, function () {
                if ($(this).data('target') == g.params.tableModalJavaScript) {
                    $(g.params.onChangeTab).hide();
                } else {
                    $(g.params.onChangeTab).show();
                }

            })
            .on('click', g.params.labelFieldConfig, function (e, button) {
                if (!button) {
                    $(this).parent().find(g.params.button).click();
                }

                $(g.params.configureBlock.main).hide().find('input, select').not('[type="hidden"]').prop('disabled', true);
                $(g.params.configureBlock.label).show().find('input, select').prop('disabled', false);

                $(g.params.type).find('option').prop('disabled', true).filter('[value="' + FIELD_TYPE_LABEL + '"]').prop('disabled', false);
                $(g.params.type).val(FIELD_TYPE_LABEL).prop('disabled', false);

                $(g.params.jsEditBtn).hide();
                $(g.params.idFieldSpan).hide();
            })
            .on('click', g.params.buttonFieldConfig, function (e, button) {
                var identifier = $(g.params.identifierInput);

                if (!button) {
                    $(this).parent().find(g.params.button).click();
                }

                $(g.params.configureBlock.main).hide().find('input, select').not('[type="hidden"]').prop('disabled', true);
                $(g.params.configureBlock.button).show().find('input, select').prop('disabled', false);

                $(g.params.type).find('option').prop('disabled', true).filter('[value="' + FIELD_TYPE_BUTTON + '"]').prop('disabled', false);
                $(g.params.type).val(FIELD_TYPE_BUTTON).prop('disabled', false);

                if (!identifier.val()) {
                    identifier.val(Math.random().toString(36).substr(2, 5));
                    $(g.params.idFieldSpan).html(identifier.val());
                }

                $(g.params.idFieldSpan).html(identifier.val());
            })
            .on('change', g.params.identifierInput, function () {
                var value = $(this).val();
                $(g.params.idFieldSpan).html(value).show();
            })
            .on('change', '.' + g.params.fieldWrapper.row, function(event, items) {
                var serializedData = _.map($(this).children('.grid-stack-item:visible'), function (el) {
                    el = $(el);
                    var node = el.data('_gridstack_node');
                    return {
                        block_col: node.x,
                        block_row: node.y,
                        block_width: node.width,
                        block_height: node.height
                    };
                }, this);

                g.updateBlockPosition(this, serializedData);
            })
            .on('change', g.params.fieldButtonAction, function () {
                var isExecuteAction = $(this).val() == 'execute',
                    executeGetFunc = $(g.params.executeFunctionInput),
                    executeCustom = $(g.params.executeCustomInput);

                if (isExecuteAction) {
                    $(g.params.additionalBlock.wrapper).show();
                    $(g.params.additionalBlock.buttonExecute).show();
                } else {
                    $(g.params.additionalBlock.wrapper).hide();
                    $(g.params.additionalBlock.buttonExecute).hide();
                }

                executeGetFunc.prop('disabled', !isExecuteAction).change();
                executeCustom.prop('disabled', !isExecuteAction).change();
            })
            .on('change', g.params.executeCustomInput + ', ' + g.params.executeFunctionInput, function () {
                var value = $(this).val(),
                    isIsset = value && value != '""';

                $(this).parent().parent().find(firstStepConfig.param.extensionButton).prop('disabled', !isIsset);
                $(this).parent().parent().find(firstStepConfig.param.extensionInput).prop('disabled', !isIsset);
            })
            .on('resizestop', '.' + this.params.fieldLeftLabelOrientation, function (event, ui) {
                g.updateLabelWidth(this, ui.size.width);
            })
            .on('change', g.params.additionalBlock.inlineSearchName, function () {
                var value = $(this).val(),
                    paramsSelect, fieldType;

                if ($(this).parents(tableConstructor.params.additionalBlock.wrapper).length > 0) {
                    fieldType = $(tableConstructor.params.paramType).val();
                    paramsSelect = $(tableConstructor.params.additionalBlock.wrapper).find('.field-custom-query-param');
                } else {
                    fieldType = $(g.params.type).val();
                    paramsSelect = $(g.params.additionalBlock.wrapper).find('.field-custom-query-param');
                }

                paramsSelect.prop('disabled', true).hide();
                $(this).parents(g.params.additionalBlock.inlineSearch).find('.' + g.params.additionalBlock.dynamicInputs).remove();

                if (fieldType == FIELD_TYPE_DATALIST_RELATION) {
                    paramsSelect.filter('[data-pk="' + value + '"]').find('option').each(function () {
                        var pk = $(this).val(),
                            col = $('<div />', {class: 'col-sm-6 form-group ' + g.params.additionalBlock.dynamicInputs}),
                            group = $('<div />', {class: 'input-group'});

                        group.append($('<div />', {class: 'input-group-addon', text: pk}));
                        group.append($('<input />', {placeholder: 'DEFAULT VALUE', class: 'form-control', name: 'datalist_relation_default[' + pk + ']'}));
                        group.append($('<input />', {placeholder: 'FIELD ID', class: 'form-control', name: 'datalist_relation_id[' + pk + ']'}));

                        col.append(group);
                        $(g.params.additionalBlock.inlineSearch).append(col);
                    });
                } else if (fieldType == FIELD_TYPE_INLINE_SEARCH) {
                    paramsSelect.filter('[data-pk="' + value + '"]').prop('disabled', false).show();
                }
            })
    };

    this.transformFormsInJSTemplates = function() {
        $(".js-generator-section").each(function(i,tab) {
            var fullCode = [];
            $(tab).find('.field-wrapper.opened').each(function(i,section){
                var currentModal = $('.jsc-template-modal.in');
                var codeTemplate = $(section).find('.js-code-template').text();
                var jsCodeSectionName = $(section).find('.js-code-template').data('name');
                var variablesTemplate = [];
                $(section).find('.js-generator-data').each(function(i,input){
                    if ($(input).hasClass('i18n-data')) {
                        var I18NMessages = [];

                        var modalType = $(section).find('.error-modal-type:checked').length ? 'confirm' : 'alert';
                        I18NMessages.push("'modal-type':'" + modalType + "'");

                        I18NMessages.push("'en-US':'" +  $(input).val() + "'");
                        $.each($(input).data('internationalization'), function(i, value) {
                            I18NMessages.push("'" + i + "':'" + value + "'");
                        });
                        if  (!$.isEmptyObject(I18NMessages)) {
                            variablesTemplate.push('var ' + $(input).data('name') + ' = {' + I18NMessages.join() + '};');
                        }
                    } else {
                        variablesTemplate.push('var ' + $(input).data('name') + ' = "' +   $(input).val() + '";');
                    }
                });

                if(variablesTemplate.length > 0) {
                    codeTemplate = variablesTemplate.join("\n") + "\n" + codeTemplate;
                }

                fullCode.push(codeTemplate);
            });

            if(fullCode.length > 0) {
                var currentModal = $('.jsc-template-modal.in');
                var jsCodeSectionName = $(tab).data('event');
                var areaWithCode = $(currentModal).find('.custom-javascript-tab .custom-js-stab textarea[name='+jsCodeSectionName+']');
                areaWithCode.val('').val(fullCode.join("\n"));
            }
        });
    };

    this.fillCustomFunction = function (isFirst, parentSelector) {
        var g = this;

        if (!parentSelector) {
            parentSelector = $(document);
        }

        var executeFunction = $(g.params.executeFunction).val();
        if (isFirst) {
            executeFunction = parentSelector.find(g.params.executeFunctionInput).val().replace(/\"/g, '');
        }
        if (parentSelector.find(g.params.executeFunctionInput).val()) {
            var customInput = parentSelector.find(g.params.executeCustomInput).val().replace(/\"/g, '');
            if (executeFunction) {
                $(g.params.executeCustom).prop('disabled', false);
                var libName = $(g.params.executeLibrary).val().replace(/\"/g, '');
                $(g.params.executeCustom).html('');
                $(g.params.executeCustom).hide();
                screenCreator.displayLoader($(g.params.executeCustom).parent(), false);
                screenCreator.getLibFunctionExtensions(libName, executeFunction, false, function(extension) {
                    $.each(extension.func_extensions_custom, function(i, item) {
                        var val = Object.keys(extension.func_extensions_custom[i])[0];
                        screenCreator.appendToSelect($(g.params.executeCustom), val, val);
                    });
                    if (customInput) {
                        $(g.params.executeCustom + ' option[value="' + customInput + '"]').prop('selected', true);
                    }
                    screenCreator.displayLoader($(g.params.executeCustom).parent(), true);
                    $(g.params.executeCustom).show();
                })
            }
        }
    };

    this.addFunctionSelect = function (libName, type, selectId) {
        var g = this;
        $(g.params.extensionFunctionBlock).hide();
        screenCreator.displayLoader($(g.params.extensionFunctionBlock).parent(), false);
        screenCreator.getLibFunctionList(libName, type, function (data) {
            if (data && Object.keys(data).length > 0) {
                $(selectId).html('');
                $.each(data, function (i, item) {
                    if (item['func_name'].search(/sub/i) < 0) {
                        screenCreator.appendToSelect($(selectId),item['func_name'],(item['func_descr']) ? item['func_name'] + ': ' + item['func_descr'] : item['func_name'])
                    }
                });
                g.fillCustomFunction(true);
            }
        $(g.params.extensionFunctionBlock).show();
        screenCreator.displayLoader($(g.params.extensionFunctionBlock).parent(), true);
        });
    };

    this.updateBlockPosition = function (object, serialize) {
        var g = this;
        g.findTemplateFieldByParentObject(object, function (fieldDataIndex) {
            $.each(g.fieldsData[fieldDataIndex]['data'], function (i, item) {
                var blockFlag = {
                    'block_height': false,
                    'block_width': false,
                    'block_row': false,
                    'block_col': false,
                };
                $.each(item, function (j, innerObject) {
                    $.each(serialize[i], function (key, value) {
                        if (innerObject.name === key) {
                            g.fieldsData[fieldDataIndex]['data'][i][j].value = value;
                            blockFlag[key] = true;
                        }
                    });
                });
                $.each(blockFlag, function (key, flag) {
                    if (!flag) {
                        g.fieldsData[fieldDataIndex]['data'][i].push({
                            name: key,
                            value: serialize[i][key]
                        });
                    }
                });
            });
        });
    };

    this.updateLabelWidth = function (object, width) {
        var g = this;
        g.findTemplateFieldByParentObject(object, function (fieldDataIndex, fieldIndex) {
            var flag = false;
            $.each(g.fieldsData[fieldDataIndex]['data'][fieldIndex], function (i, item) {
                if (item['name'] == 'label_width') {
                    g.fieldsData[fieldDataIndex]['data'][fieldIndex][i]['value'] = width;
                    flag = true;
                    return false;
                }
            });

            if (!flag) {
                g.fieldsData[fieldDataIndex]['data'][fieldIndex].push({
                    name: 'label_width',
                    value: width
                });
            }
        });
    };

    this.resetModal = function () {
        screenCreator.setInfo(this.params.modalBody, false);
        $(this.params.form).each(function () {
            this.reset();
            $(this).find('input').data('internationalization', null);
        });

        $(this.params.dataField).prop('disabled', false);
        $(this.params.type).prop('disabled', false);
        $(this.params.formatType).prop('disabled', false);
        $(this.params.labelOrientation).prop('disabled', false);
        $(this.params.fieldWidth.type).prop('disabled', false);
        $(this.params.editType).prop('disabled', false);
        $(this.params.copyableInput).prop('disabled', false);

        $(this.params.formatting).find('input, select').prop('disabled', false);
        $(this.params.jsEditBtn).show();
        $(this.params.idFieldSpan).show();

        $(this.params.type).trigger('change');
        $(this.params.numRows).trigger('change');
        $(this.params.dataField).trigger('change');
        $(this.params.fieldWidth.type).trigger('change');

        $(this.params.position.colInput).val(0);
        $(this.params.position.rowInput).val(0);
        $(this.params.position.heightInput).val(2);
        $(this.params.position.widthInput).val(12);
        $(this.params.labelWidthInput).val('');
    };

    /**
     * Used for searching parameters by specific field
     * @param object
     * @param callback
     * @returns {*}
     */
    this.findTemplateFieldByParentObject = function (object, callback) {
        var g = this;
        var parentObject = $(object).parents().map(function () {
            var col = $(this).attr('data-col'),
                row = $(this).attr('data-row');

            return (col && row) ? this : null;
        }).get(0);

        var row = $(parentObject).attr('data-row'),
            col = $(parentObject).attr('data-col'),
            classStack = '.' + object.className.split(' ').join('.'),
            index = $(parentObject).find(classStack).index(object),
            result = null;

        if (row && col) {
            $.each(g.fieldsData, function (i, item) {
                if (row == item.row_num && col == item.col_num) {
                    if (typeof callback == "function") callback(i, index, row, col);
                    result = item;
                    return false;
                }
            });

            return result;
        }

    };

    /**
     * Save config for field
     * @param {number} row
     * @param {number} col
     * @param {Object} data
     * @param {boolean} [isPrepend]
     */
    this.saveTemplate = function (row, col, data, isPrepend) {
        var g = this,
            flag = false,
            initData = {
                "row_num": row,
                "col_num": col,
                "data": [data]
            };

        $.each(g.fieldsData, function (index, value) {
            if (value.row_num == row && value.col_num == col) {
                if (isPrepend) {
                    g.fieldsData[index]['data'].unshift(data);
                } else {
                    g.fieldsData[index]['data'].push(data);
                }
                flag = true;
            }
        });

        if (!flag) g.fieldsData.push(initData);
    };

    /**
     * Getting fields settings by row and column of sections
     * @param {number} row
     * @param {number} col
     * @returns {boolean}
     */
    this.getTemplate = function (row, col) {
        var g = this,
            result = false;

        $.each(g.fieldsData, function (index, value) {
            if (value.row_num == row && value.col_num == col) {
                result = value.data;
                return false;
            }
        });

        return result;
    };

    /**
     * Update sections with EDIT type. Used after saving
     */
    this.updateSectionsInfo = function () {
        var g = this,
            bodyClass = '.panel-body';

        $.each(screenCreator.templateLayout, function (index, val) {
            if (val['layout_type'] == "LIST") {
                var panelElement = screenCreator.param.panelBlock + '[data-row="' + val['row_num'] + '"][data-col="' + val['col_num'] + '"]',
                    template = g.getTemplate(val['row_num'], val['col_num']),
                    formatting = screenCreator.getFormatting(val['row_num'], val['col_num']);

                if (template) {
                    g.updateSectionFields(template, $(panelElement).find(bodyClass), formatting, val['data_source_get']);
                }

                $(panelElement).find(g.params.button).show();
                $(panelElement).find(g.params.labelFieldConfig).show();
                $(panelElement).find(g.params.buttonFieldConfig).show();
            }
        });
    };

    /**
     * Update fields on section. Used in previous function
     * @param {Object} data
     * @param {object} block - Section object
     * @param {object} formatting - Section formatting
     * @param {string} dataSourceGet - Data source get string
     */
    this.updateSectionFields = function (data, block, formatting, dataSourceGet) {
        var g = this,
            count = data.length,
            innerWrapper = $('<div />', {class: g.params.fieldWrapper.row + ' grid-stack'});

        block.html('');
        if (count > 0) {
            $.each(data, function (i, item) {
                var itemData = {},
                    fieldsObject,
                    wrapperID = false;

                $.each(item, function(i, param) {
                    if (param.value) itemData[param.name] = param.value;
                });

                itemData = $.extend({}, formatting, itemData);

                if (itemData['identifier']) {
                    wrapperID = itemData['identifier'];
                } else if (dataSourceGet && itemData['data_field']) {
                    wrapperID = '#' + dataSourceGet.replace(/\.|,|:|;/gi, '-') + '--' + itemData['data_field'].replace(/\.|,|:|;/gi, '-');
                }

                if (fieldsObject = g.buildField(itemData, i)) {
                    if (itemData['field_type'] == FIELD_TYPE_HIDDEN) {
                        fieldsObject.addClass('dropdown-disabled');
                    } else if (itemData['field_type'] == FIELD_TYPE_LABEL) {
                        fieldsObject.find(g.params.fieldLabelWrapper).addClass(g.params.fieldLabelDimension);
                    }
                    fieldsObject.appendTo(innerWrapper);
                    if (wrapperID) {
                        fieldsObject.tooltip({
                            placement: 'auto',
                            title: wrapperID
                        });
                    }
                }
            });
            block.html(innerWrapper);
        }

        innerWrapper.gridstack({
            verticalMargin: 0,
            cellHeight: 30,
            alwaysShowResizeHandle: /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent),
            float: true
        });
    };

    /**
     * Event when format type was changed. Trigger related events
     * @param {string} type
     * @param {string} [paramType]
     * @param {boolean} [param]
     */
    this.formatTypeChange = function (type, paramType, param) {
        var select = '',
            me = this;

        if (!param) {
            if (type in screenCreator.paramTypes) select = screenCreator.getSelectByParams(screenCreator.paramTypes[type]).html();
            $(this.params.formatType).html(select);
            if (!select) screenCreator.appendToSelect(this.params.formatType, '', '-- Empty --');

            if (paramType) {
                switch (paramType) {
                    case 'date':
                        $(this.params.formatType).val(FORMAT_TYPE_DATE);
                        $(this.params.formatType).find('option').prop('disabled', true);
                        $(this.params.formatType).find('option[value="' + FORMAT_TYPE_DATE + '"]').prop('disabled', false);
                        break;
                    case 'datetime':
                        $(this.params.formatType).val(FORMAT_TYPE_DATE_TIME);
                        $(this.params.formatType).find('option').prop('disabled', true);
                        $(this.params.formatType).find('option[value="' + FORMAT_TYPE_DATE_TIME + '"]').prop('disabled', false);
                        break;
                }
            }
        }

        if (type == FIELD_TYPE_TEXTAREA) {
            $(this.params.numRows).prop('disabled', false);
        } else if (type == FIELD_TYPE_MULTI_SELECT) {
            $(this.params.numRows).prop('disabled', false);
            $(this.params.listName).prop('disabled', false);
            $(this.params.pullUpBtn).show().parent('div').addClass('input-group');
        } else if (type == FIELD_TYPE_LIST) {
            $(this.params.listName).prop('disabled', false);
            $(this.params.numRows).val('').prop('disabled', true);
            $(this.params.pullUpBtn).show().parent('div').addClass('input-group');
        } else if (type == FIELD_TYPE_DOCUMENT) {
            $(this.params.listName).prop('disabled', true);
            $(this.params.numRows).val('').prop('disabled', true);
        } else {
            $(this.params.numRows).val('').prop('disabled', true);
            $(this.params.listName).prop('disabled', true);
            $(this.params.pullUpBtn).hide().parents('div').removeClass('input-group');
        }

        $(this.params.additionalBlock.wrapper).hide();
        $(this.params.additionalBlock.group).hide().find('input, select').prop('disabled', true);

        $(this.params.fieldsModalTypeLink).hide();
        $(this.params.typeLink).prop('disabled', true);

        $(this.params.additionalBlock.documentGroup).hide();
        $(this.params.additionalBlock.documentFamily).prop('disabled', true).trigger('change');

        $(this.params.additionalBlock.inlineSearch).hide();
        $(this.params.additionalBlock.inlineSearchName).prop('disabled', true);

        $(this.params.additionalBlock.buttonExecute).hide();

        $(this.params.additionalBlock.dropDown).hide();
        $(this.params.additionalBlock.dropDownValues).prop('disabled', true);

        this.clearOption(true);

        $(this.params.labelOrientation).prop('disabled', false);
        $(this.params.label).prop('disabled', false);
        $(this.params.tooltip).prop('disabled', false);

        $(this.params.editType).prop('disabled', false);


        if ($(this.params.type).parents(this.params.configureBlock.main).hasClass('col-sm-3')) {
            $(this.params.configureBlock.dependentField).addClass('hidden');
            $(this.params.type).parents(this.params.configureBlock.main).removeClass('col-sm-3').addClass('col-sm-6');
        }

        if (type == FIELD_TYPE_LINK) {
            $(this.params.additionalBlock.wrapper).show();
            $(this.params.fieldsModalTypeLink).show();
            $(this.params.typeLink).prop('disabled', false);
        } else if (type == FIELD_TYPE_DATALIST) {
            $(this.params.additionalBlock.wrapper).show();
            $(this.params.additionalBlock.dropDown).show();
            $(this.params.additionalBlock.dropDownValues).prop('disabled', false);
        } else if (type == FIELD_TYPE_WITH_DEPENDENT_FIELD) {
            $(this.params.configureBlock.dependentField).removeClass('hidden');
            $(this.params.type).parents(this.params.configureBlock.main).removeClass('col-sm-6').addClass('col-sm-3');
        } else if (type == FIELD_TYPE_DOCUMENT) {
            $(this.params.additionalBlock.wrapper).show();
            $(this.params.additionalBlock.documentGroup).show();
            $(this.params.additionalBlock.documentFamily).prop('disabled', false).trigger('change');
        } else if ($.inArray(type, [FIELD_TYPE_INLINE_SEARCH, FIELD_TYPE_DATALIST_RELATION]) >= 0) {
            $(this.params.additionalBlock.wrapper).show();
            $(this.params.additionalBlock.inlineSearch).show();
            $(this.params.additionalBlock.inlineSearchName).prop('disabled', false);
            if (!param) {
                $(this.params.additionalBlock.inlineSearchName).trigger('change');
            }
        } else if (type == FIELD_TYPE_HIDDEN) {
            $(this.params.length).val('').prop('disabled', true);
            $(this.params.fieldWidth.type).val('').trigger('change').find('option[value="L"], option[value="V"]').prop('disabled', true);

            $(this.params.labelOrientation).prop('disabled', true);
            $(this.params.label).prop('disabled', true);

            $(this.params.tooltip).prop('disabled', true);
            $(this.params.editType).prop('disabled', true);
        } else if ($.inArray(type, [FIELD_TYPE_CHECKBOX, FIELD_TYPE_RADIO]) >= 0) {
            $(this.params.additionalBlock.wrapper).show();
            $(this.params.additionalBlock.group).show().find('input, select').prop('disabled', false);
            $('[data-group-name]').each(function () {
                var attribute = $(this).attr('data-group-name'),
                    dataList = $(me.params.additionalBlock.groupDataList);
                if (!dataList.find('option[value="' + attribute + '"]').length) dataList.append($('<option />', {
                    value: attribute,
                    text: attribute
                }));
            });
        }

        if ($.inArray(type, [FIELD_TYPE_LIST, FIELD_TYPE_MULTI_SELECT, FIELD_TYPE_DOCUMENT, FIELD_TYPE_DATALIST_RELATION, FIELD_TYPE_CHECKBOX, FIELD_TYPE_RADIO]) >= 0) {
            $(this.params.length).val('').prop('disabled', true);
            $(this.params.fieldWidth.type).find('option[value="L"]').prop('disabled', true);
            $(this.params.fieldWidth.type).find('option[value="V"]').prop('disabled', false).val('V').trigger('change');
        } else {
            $(this.params.length).prop('disabled', false);
            $(this.params.fieldWidth.type).find('option[value="L"]').prop('disabled', false);
            $(this.params.fieldWidth.type).find('option[value="V"]').prop('disabled', false);
        }
    };

    /**
     * Getting object of row with field.
     * @param {Object} data
     * @param {number} i - Index of field
     * @returns {object}
     */
    this.buildField = function (data, i) {
        var g = this,
            fieldObject;

        if (fieldObject = g.buildFieldType(data['field_type'])) {
            fieldObject.attr('maxlength', data['field_length']);
            fieldObject.prop('readonly', (data['edit_type'] == 'R'));
            fieldObject.prop('copyable', (data['copyable_field'] == 'Y'));
            fieldObject = g.buildFormatType(fieldObject, data['format_type']);

            screenCreator.setStyles(fieldObject, data, 'field');
        }

        if (data['field_type'] == FIELD_TYPE_MULTI_SELECT) {
            fieldObject.attr('size', data['num_rows']);
        }

        if (data['field_type'] == FIELD_TYPE_TEXTAREA) {
            fieldObject.attr('rows', data['num_rows']);
        }

        if ($.inArray(data['field_type'], [FIELD_TYPE_CHECKBOX, FIELD_TYPE_RADIO, FIELD_TYPE_DOCUMENT]) >= 0) {
            fieldObject.attr('data-group-name', data['field_group']);
        }

        if ($.inArray(data['field_type'], [FIELD_TYPE_BUTTON]) >= 0) {
            fieldObject.val(data['value']);
            fieldObject.css('width', '100%');
        }

        if (data['field_width_type']) {
            if (data['field_width_type'] === 'L') {
                fieldObject.attr('size', data['field_length']);
                fieldObject.css('width', 'auto');
            } else if (data['field_width_type'] === 'V' && data['field_width_value']) {
                fieldObject.css('width', data['field_width_value']);
            }
        }

        return g.buildFieldHelper(fieldObject, data, i);
    };

    /**
     * Getting row with inputs. Building rows bootstrap object with linked fields
     * @param {object} inputObject - Object with input
     * @param {object} fieldData - Object with field data
     * @param {number} i - Index of field
     * @returns {object}
     */
    this.buildFieldHelper = function (inputObject, fieldData, i) {
        var inputLayoutClass,
            labelObject,
            wrapperInner = $('<div />', {class: this.params.fieldWrapper.mainInner + ' grid-stack-item-content'}),
            wrapper = $('<div />', {
                class: this.params.fieldWrapper.main + ' grid-stack-item',
                'data-gs-id': i,
                'data-gs-y': (fieldData['block_row'] && fieldData['field_type'] != FIELD_TYPE_HIDDEN) ? fieldData['block_row'] : null,
                'data-gs-x': (fieldData['block_col']) ? fieldData['block_col'] : 0,
                'data-gs-width': (fieldData['block_width'] && fieldData['field_type'] != FIELD_TYPE_HIDDEN) ? fieldData['block_width'] : 12,
                'data-gs-height': (fieldData['block_height']) ? fieldData['block_height'] : 2,
                'data-gs-min-height': 2,
                'data-gs-no-resize': fieldData['field_type'] == FIELD_TYPE_HIDDEN,
                'data-gs-no-move': fieldData['field_type'] == FIELD_TYPE_HIDDEN
            });

        if (fieldData['field_label']) {
            labelObject = $('<label/>', {text: fieldData['field_label']});
            screenCreator.setStyles(labelObject, fieldData, 'label');

            if (fieldData['label_orientation'] === 'LEFT') {
                if (fieldData['label_width']) {
                    labelObject.css('width', fieldData['label_width']);
                }

                labelObject.addClass(this.params.fieldLeftLabelOrientation);
                labelObject.resizable({
                    resize: function(event, ui) {
                        ui.size.height = ui.originalSize.height;
                    }
                });
            }
        }

        if (fieldData['field_type'] == FIELD_TYPE_LABEL) {
            wrapperInner.addClass('field-label-wrapper');
            if (fieldData['label_text_align']) {
                var side;
                switch (fieldData['label_text_align']) {
                    case 'left':
                        side = 'flex-start';
                        break;
                    case 'right':
                        side = 'flex-end';
                        break;
                    default:
                        side = 'center';
                        break;
                }
                wrapperInner.css('justify-content', side);
            }
        } else {
            inputObject = $('<div />', {class: inputLayoutClass}).css('overflow', 'hidden').html(inputObject);
        }

        if (fieldData['field_group']) {
            wrapper.addClass('field-checked-group');
            wrapperInner.attr('style', 'background-color: ' + this.stringToColour(fieldData['field_group']) + '29');
        }

        wrapperInner.append(labelObject).append(inputObject);
        wrapper.append(wrapperInner);

        if (fieldData['field_type'] == FIELD_TYPE_HIDDEN) {
            wrapper.css('opacity', '0.5');
        }

        $('<button />', {
            'class': 'btn btn-default btn-xs ' + this.params.fieldSettingsBtn.config,
            'data-toggle': 'modal',
            'data-target': '#fields-modal',
            'html': "<span class='glyphicon glyphicon-cog'></span>"
        }).appendTo(wrapper);

        $('<button />', {
            'class': 'btn btn-warning btn-xs ' + this.params.fieldSettingsBtn.remove,
            'html': "<span class='glyphicon glyphicon-remove'></span>"
        }).appendTo(wrapper);

        return wrapper;
    };



    this.stringToColour = function(str) {
        var hash = 0, i;
        for (i = 0; i < str.length; i++) {
            hash = str.charCodeAt(i) + ((hash << 5) - hash);
        }
        var colour = '#';
        for (i = 0; i < 3; i++) {
            var value = (hash >> (i * 8)) & 0xFF;
            colour += ('00' + value.toString(16)).substr(-2);
        }
        return colour;
    };

    /**
     * Getting input object by selected format type
     * @param {string} value
     * @returns {object}
     */
    this.buildFieldType = function (value) {
        var result;
        switch (value) {
            case FIELD_TYPE_LIST:
                result = $('<select />', {class: 'form-control'}).append($('<option />', {text: '-- Select --'}));
                break;
            case FIELD_TYPE_MULTI_SELECT:
                result = $('<select />', {class: 'form-control', multiple: true}).append($('<option />', {text: '-- Multi-select --'}));
                break;
            case FIELD_TYPE_CHECKBOX:
                result = $('<input />', {type: 'checkbox'});
                break;
            case FIELD_TYPE_RADIO:
                result = $('<input />', {type: 'radio'});
                break;
            case FIELD_TYPE_DOCUMENT:
                result = $('<div />', {text: 'Upload file'});
                break;
            case FIELD_TYPE_BUTTON:
                result = $('<input />', {type: 'button', class: 'btn btn-primary'});
                break;
            case FIELD_TYPE_LABEL:
                result = null;
                break;
            case FIELD_TYPE_TEXTAREA:
                result = $('<textarea />', {class: 'form-control'});
                break;
            default:
                result = $('<input />', {"class": 'form-control'});
                break;
        }

        return result;
    };

    /**
     * Added parameters to input by format type
     * @param {object} object
     * @param {string} value
     * @returns {*}
     */
    this.buildFormatType = function (object, value) {
        switch (value) {
            case FORMAT_TYPE_CURRENCY:
                object.attr('type', 'number')
                    .attr('min', '1')
                    .attr('step', '0.01')
                    .attr('data-type', 'currency');
                break;

            case FORMAT_TYPE_EMAIL:
                object.attr('type', 'email');
                break;
            case FORMAT_TYPE_DATE:
                object.attr('type', 'date');
                break;
            case FORMAT_TYPE_DATE_TIME:
                object.attr('type', 'datetime-local');
                break;
        }

        return object;
    };

    /**
     * Getting params for data field. If result is successfully, run callback function
     * @param {string} libName
     * @param {string} functionName
     * @param {function} [callback]
     */
    this.setDataFieldInfo = function (libName, functionName, callback) {
        var g = this,
            cacheData = sessionStorage.getItem(libName + '__' + functionName);

        screenCreator.displayLoader($(g.params.dataField).parent(), true);

        if (cacheData) {
            cacheData = $.parseJSON(cacheData);
            return g.__setDataFieldInfo(cacheData, callback);
        }

        screenCreator.displayLoader($(g.params.dataField).parent());
        $(g.params.dataField).hide();
        $(g.params.dataField).siblings('.bootstrap-select').hide();
        $.ajax({
            type: "POST",
            url: screenCreator.param.library.getParamsUrl,
            data: {library: libName, function: functionName}
        }).done(function (data) {
            if (data) {
                g.__setDataFieldInfo(data, callback);
                sessionStorage.setItem(libName + '__' + functionName, JSON.stringify(data));

            } else {
                screenCreator.setInfo(g.params.modalBody, 'danger', 'Can\'t get info from server', true);
                $(g.params.saveFieldsBtn).hide();
            }
        });
    };

    this.setDataFieldLink = function (selectedElements) {
        var g = this,
            numLink = parseInt($(g.params.menuLinkField).data('external-link')),
            setValueMenuLink = function (data, selectedElements, numLink) {
                if (selectedElements) {
                    g.__setValueMenuLink(data, selectedElements[numLink]);
                    if (selectedElements[numLink]) {
                        g.setDataFieldGroupScreen(selectedElements[numLink], null, selectedElements);
                    }
                } else {
                    g.__setValueMenuLink(data);
                }
            };

        $(g.params.externalLinkBlock).show();
        screenCreator.displayLoader($(g.params.menuLinkField).parent());
        $(g.params.menuLinkField).hide();

        var cacheData = sessionStorage.getItem('cache_menuLink_' + g.params.menuLinkField);
        if (cacheData) {
            cacheData = $.parseJSON(cacheData);
            setValueMenuLink(cacheData, selectedElements, numLink);
        } else {
            if ($("g.params.formatType option").length == 0) {
                $.ajax({
                    type: "POST",
                    url: screenCreator.param.library.getLinkUrl
                }).done(function (data) {
                    if (data) {
                        setValueMenuLink(data, selectedElements, numLink);
                        sessionStorage.setItem('cache_menuLink_' + g.params.menuLinkField, JSON.stringify(data));
                    } else {
                        $(g.params.externalLinkBlock).hide();
                    }
                });
            }
        }
    };

    this.setDataFieldGroupScreen = function (select, event, selectedElements) {
        var g = this,
            numLink = parseInt($(g.params.groupScreenLinkField).data('external-link')),
            value,
            setValueGroupScreen = function (data, selectedElements, numLink) {
                if (selectedElements) {
                    g.__setValueGroupScreen(data, selectedElements[numLink]);
                    if (selectedElements[numLink+1]) {
                        g.setDataFieldScreen(selectedElements[numLink+1], null, selectedElements);
                    }
                } else {
                    g.__setValueGroupScreen(data)
                }
            };
        if (event) {
            g.clearOption(false, event.target);
            value = $(event.target).parents(g.params.externalLinkBlock).find(g.params.menuLinkField).val()
        }
        if (selectedElements) {
            value = selectedElements[numLink - 1];
        }
        if (value) {
            screenCreator.displayLoader($(g.params.groupScreenLinkField).parent());
            $('.' + g.params.externalLinkError).remove();
            $(g.params.groupScreenLinkField).hide();
            var cacheData = sessionStorage.getItem('cache_groupScreen_' + value);
            if (cacheData) {
                cacheData = $.parseJSON(cacheData);
                setValueGroupScreen(cacheData, selectedElements, numLink);
            } else {
                $.ajax({
                    type: "POST",
                    url: screenCreator.param.library.getLinkUrl,
                    data: {'menu_name' : value, 'get': 'GroupScreen'}
                }).done(function (data) {
                    if (data['data']) {
                        setValueGroupScreen(data, selectedElements, numLink);
                        sessionStorage.setItem('cache_groupScreen_' + value, JSON.stringify(data));
                    } else if(data['error']) {
                        screenCreator.displayLoader($(g.params.groupScreenLinkField).parent(), true);
                        $(g.params.groupScreenLinkField).show();
                    }
                }).fail(function() {
                    g.externalLinkError(g.params.groupScreenLinkField);
                });
            }
        }
    };

    this.setDataFieldScreen = function (select, event, selectedElements) {
        var g = this,
            numLink = parseInt($(g.params.screenLinkField).data('external-link')),
            value,
            setValueScreen = function (data, select, selectedElements, numLink) {
                if (selectedElements) {
                    g.__setValueScreen(data, select);
                    if (selectedElements[numLink+1]) {
                        g.setDataFieldFunction(selectedElements[numLink + 1], null, selectedElements);
                    }
                } else {
                    g.__setValueScreen(data);
                }
            };
        if (event) {
            g.clearOption(false, event.target);
            value = $(event.target).parents(g.params.externalLinkBlock).find(g.params.groupScreenLinkField).val();
        }

        if (selectedElements) {
            value = selectedElements[numLink - 1];
        }

        if (value) {
            screenCreator.displayLoader($(g.params.screenLinkField).parent());
            $('.' + g.params.externalLinkError).remove();
            $(g.params.screenLinkField).hide();
            var cacheData = sessionStorage.getItem('cache_screen_' + $(g.params.menuLinkField).val() + "_" + value);
            if (cacheData) {
                cacheData = $.parseJSON(cacheData);
                setValueScreen(cacheData, select, selectedElements, numLink);
            } else {
                $.ajax({
                    type: "POST",
                    url: screenCreator.param.library.getLinkUrl,
                    data: {'screen_name': value, 'get': 'Screen'}
                }).done(function (data) {
                    if (data) {
                        setValueScreen(data, select, selectedElements, numLink);
                        sessionStorage.setItem('cache_screen_' + $(g.params.menuLinkField).val() + "_" + value, JSON.stringify(data));
                    }
                }).fail(function() {
                    g.externalLinkError(g.params.screenLinkField);
                });
            }
        }
    };

    this.setDataFieldFunction = function (select, event, selectedElements) {
        var g = this,
            numLink = parseInt($(g.params.settingsLinkField).data('external-link')),
            value,
            isSelected = false;

        if (event) {
            g.clearOption(false, event.target);
            value = $(event.target).parents(g.params.externalLinkBlock).find(g.params.screenLinkField).val()
        }

        if (selectedElements) {
            value = selectedElements[numLink - 1];
            isSelected = true;
        }

        if (value) {
            screenCreator.displayLoader($(g.params.settingsLinkField).parent());
            $('.' + g.params.externalLinkError).remove();
            $(g.params.settingsLinkField).hide();
            var cacheData = sessionStorage.getItem('cache_function_' + $(g.params.menuLinkField).val() + "_" + $(g.params.groupScreenLinkField).val() + "_" +  value);

            if (cacheData) {
                cacheData = $.parseJSON(cacheData);
                if (isSelected) {
                    g.__setValueFunction(cacheData, select);
                } else {
                    g.__setValueFunction(cacheData);
                }
            } else {
                $.ajax({
                    type: "POST",
                    url: screenCreator.param.library.searchConfigParamUrl,
                    data: {screen_id: value}
                }).done(function (data) {
                    if (data) {
                        if (isSelected) {
                            g.__setValueFunction(data, select);
                        } else {
                            g.__setValueFunction(data);
                        }
                        sessionStorage.setItem('cache_function_' + $(g.params.menuLinkField).val() + "_" + $(g.params.groupScreenLinkField).val() + "_" +  value, JSON.stringify(data));
                    }
                }).fail(function() {
                    g.externalLinkError(g.params.settingsLinkField);
                });
            }
        }
    };

    this.externalLinkError = function (field) {
        screenCreator.displayLoader($(field).parent(), true);
        $(field).show().prop('disabled', false);
        $(field).html('').append($('<option />', {text: '-- Empty --'}));

    };

    this.__setValueMenuLink = function (data, select) {
        var g = this;
        $.each(data, function (i, item) {
            $(g.params.menuLinkField).append($('<option>', {
                value: item.menu_name,
                text: item.menu_name + ' - ' + item.menu_description,
                selected: select === item.menu_name ? select : null
            }))
        });
        screenCreator.displayLoader($(g.params.menuLinkField).parent(), true);
        $(g.params.menuLinkField).show();
        $(g.params.menuLinkField).prop('disabled', false);
    };

    this.__setValueGroupScreen = function (data, select) {
        var g = this;
        $.each(data['data'], function (i, item) {
            $(g.params.groupScreenLinkField).append($('<option>', {
                value: item.screen_name,
                text: item.screen_text,
                selected: select === item.screen_name ? select : null
            }));
        });
        screenCreator.displayLoader($(g.params.groupScreenLinkField).parent(), true);
        $(g.params.groupScreenLinkField).show();
        $(g.params.groupScreenLinkField).prop('disabled', false);
    };

    this.__setValueScreen = function (data, select) {
        var g = this;
        $.each(data, function (i, item) {
            $(g.params.screenLinkField).append($('<option>', {
                value: item.id,
                text: item.screen_tab_text + ' - ' + item.screen_desc,
                selected: select === item.id ? select : null
            }))
        });
        screenCreator.displayLoader($(g.params.screenLinkField).parent(), true);
        $(g.params.screenLinkField).show();
        $(g.params.screenLinkField).prop('disabled', false);
    };

    this.__setValueFunction = function (data, select) {
        var g = this;
        $.each(data, function (i, item) {
            $(g.params.settingsLinkField).append($('<option>', {
                value: item,
                text: item,
                selected: select === item ? select : null
            }))
        });
        screenCreator.displayLoader($(g.params.settingsLinkField).parent(), true);
        $(g.params.settingsLinkField).show();
        $(g.params.settingsLinkField).prop('disabled', false);
    };

    /**
     * Auxiliary method for setting data field to input select type
     * @param {Array} data
     * @param {function} [callback]
     * @private
     */
    this.__setDataFieldInfo = function (data, callback) {
        var g = this;

        screenCreator.setInfo(g.params.modalBody, false);
        $(g.params.dataField).html('');
        $.each(data, function (index, item) {
            screenCreator.appendToSelect(g.params.dataField, item['alias_field']);
        });
        $(g.params.dataField).selectpicker('refresh');
        g.functionParamsObject = data;
        if (typeof callback == "function") callback();

        screenCreator.displayLoader($(g.params.dataField).parent(), true);
        $(g.params.dataField).show();
        $(g.params.dataField).siblings('.bootstrap-select').show();
    };

    this.refreshCodeMirror = function (timeRefresh) {
        setTimeout(function(){
            $($.find('.CodeMirror')).each(function (i, obj) {
                obj.CodeMirror.refresh();
            });
        },timeRefresh);
    };
};

/**
 * Table settings class
 * @class tableConstructorApp
 */
var tableConstructorApp = function () {
    //Init after loading page
    this.load = function () {
        var g = this;
        $(document).ready(function () {
            g.bindLoadEvents();
        });
    };

    this.params = {
        button: 'setting-table-icon',
        form: '.table-constructor-form',
        modalBody: '#table-modal .modal-body',
        filter: '#table-filter-column',
        filterValue: '#table-default-filter-value',
        paramType: '#table-param-type',
        formatType: '#table-format-type',
        showColumn: '#table-show-column',
        saveFieldsBtn: '.btn-save-table',
        filterRow: 'table-filter-row',
        formatting: '#formatting-modal-table',
        rightArrow: 'right-table-arrow',
        leftArrow: 'left-table-arrow',
        upArrow: 'up-table-arrow',
        downArrow: 'down-table-arrow',
        pullUpBtn: '.pull-up-btn-table',
        listName: '#table-list-name',
        additionalBlock: {
            wrapper: '.table-additional-fields-row',
        },
        buttonActionInput: '#table-button-action'
    };

    this.paramTypes = {};
    this.paramTypes[FIELD_TYPE_TEXT] = [FORMAT_TYPE_NONE, FORMAT_TYPE_EMAIL, FORMAT_TYPE_DATE, FORMAT_TYPE_DATE_TIME];
    this.paramTypes[FIELD_TYPE_NUMERIC] = [FORMAT_TYPE_NONE, FORMAT_TYPE_CURRENCY];
    this.paramTypes[FIELD_TYPE_LIST] = null;
    this.paramTypes[FIELD_TYPE_TEXTAREA] = null;
    this.paramTypes[FIELD_TYPE_MULTI_SELECT] = null;
    this.paramTypes[FIELD_TYPE_CHECKBOX] = null;
    this.paramTypes[FIELD_TYPE_RADIO] = null;
    this.paramTypes[FIELD_TYPE_ALERT] = null;
    this.paramTypes[FIELD_TYPE_LINK] = [FORMAT_TYPE_LINK_LOCAL];
    this.paramTypes[FIELD_TYPE_LABEL] = null;
    this.paramTypes[FIELD_TYPE_BUTTON] = null;
    this.paramTypes[FIELD_TYPE_DATALIST_RELATION] = null;

    this.init = function () {
        var selectType = screenCreator.getSelectByParams(this.paramTypes);
        $(this.params.paramType).html(selectType.html());
    };

    this.bindLoadEvents = function () {
        var g = this,
            row, col, pramName;

        $(document)
            //Click on column setting button
            .on('click', '.' + g.params.button, function () {
                row = $(this).parents('.section-panel').attr('data-row');
                col = $(this).parents('.section-panel').attr('data-col');
                pramName = $(this).parents('th').attr('data-name');

                g.resetModal();

                var template = screenCreator.getTemplate(row, col),
                    paramIndex = template['layout_configuration']['params'].indexOf(pramName),
                    formatType = null,
                    aliasType;

                if (!template['data_source_get'] || !template['layout_table']) {
                    screenCreator.setInfo(g.params.modalBody, 'danger', 'First, configure', true);
                    $(g.params.saveFieldsBtn).hide();
                } else if (template['layout_type'] != "TABLE") {
                    screenCreator.setInfo(g.params.modalBody, 'danger', 'Type of this section is not TABLE', true);
                    $(g.params.saveFieldsBtn).hide();
                } else {
                    $("#js-edit-table textarea[id^='js_edit']").each(function() {
                        var editor = $(this).parent().find('.CodeMirror')[0].CodeMirror;
                        editor.clearHistory();
                        editor.setValue('');
                    });

                    if (paramIndex >= 0) {
                        aliasType = (template['layout_configuration']['type']) ? template['layout_configuration']['type'][paramIndex] : 'text';

                        switch (aliasType) {
                            case 'alert':
                                $(g.params.paramType).find('option').prop('disabled', true);
                                $(g.params.paramType).find('option[value="' + FIELD_TYPE_ALERT + '"]').prop('disabled', false);

                                break;
                            default:
                                $(g.params.paramType).find('option').prop('disabled', false);
                                break;
                        }
                    }

                    $.each(template['layout_table']['column_configuration'][pramName], function (i, item) {
                        var menuSelectedElements = [];

                        if (item.name == $(g.params.formatType).attr('name')) {
                            formatType = item.value;
                        }

                        if (item.name === 'field_link_menu') {
                            var elem =  $(g.params.modalBody).find('[data-external-link]');
                            for (var j = 0; j < elem.length; j++) {
                                menuSelectedElements.push(template['layout_table']['column_configuration'][pramName][i + j].value)
                            }
                        }

                        if (menuSelectedElements.length > 0) {
                            fieldsConstructor.setDataFieldLink(menuSelectedElements);
                        }


                        if (item.name.indexOf('js_event') != -1) {
                            $("#js-edit-table textarea[id^='js_edit']").each(function(i, j){
                                var editor = $(this).parent().find('.CodeMirror')[0].CodeMirror;
                                if (item.value) {
                                    if (item.name == $(j).attr('name')) {
                                        editor.clearHistory();
                                        editor.setValue(decodeURIComponent(escape(atob(item.value))));
                                    }
                                }
                            });
                        } else {
                            $("#js-edit-table textarea[id^='js_edit']").each(function() {
                                var editor = $(this).parent().find('.CodeMirror')[0].CodeMirror;
                                editor.clearHistory();
                                editor.setValue('');
                            });

                            var findInput = $(g.params.form).find('*[name="' + item.name + '"]');
                            if (findInput.attr('type') === 'checkbox') {
                                findInput.prop('checked', true);
                            } else {
                                findInput.val(item.value);
                            }

                            if (item.name == $(fieldsConstructor.params.additionalBlock.inlineSearchName).attr('name')) {
                                $(tableConstructor.params.additionalBlock.wrapper).find(fieldsConstructor.params.additionalBlock.inlineSearchName).trigger('change');
                            }
                        }
                    });

                    $(g.params.paramType).trigger('change');
                    $(g.params.formatType).val(formatType);

                    $(g.params.form).find('*[data-type="color"]').trigger('change');
                    $(g.params.filter).trigger('change');
                }

                if (g.getTemplate(row, col)) $(g.params.linkColumn).prop('disabled', false);
                else $(g.params.linkColumn).prop('disabled', true);
            })
            //Click on save column settings button
            .on('submit', g.params.form, function (event) {
                event.preventDefault();

                $("textarea[id^='js_edit']").each(function(i,j){
                    if (!isBase64($(j).val())) {
                        $(j).val(btoa(unescape(encodeURIComponent($(j).val()))));
                    }
                });

                $('.spectrum-source').prop('disabled', true); //Don't include additional color type field to serialize

                var me = $(g.params.form);
                g.saveTemplate(row, col, pramName, $(me).serializeArrayWithInternationalization());
                screenCreator.setInfo(g.params.modalBody, 'success', 'Column config has been added');

                $('.spectrum-source').prop('disabled', false); //Don't include additional color type field to serialize
                $(g.params.formatting).find('.close').trigger('click');

                g.updateSectionsInfo();
            })
            //Selected filter row
            .on('change', g.params.filter, function () {
                var prop = !($(this).val());
                $(g.params.filterValue).prop('disabled', prop);
            })
            //Selected type of column
            .on('change', g.params.paramType, function () {
                var select = '',
                    type = $(this).val();

                if (type in g.paramTypes) select = screenCreator.getSelectByParams(g.paramTypes[type]).html();
                $(g.params.formatType).html(select);

                $(g.params.additionalBlock.wrapper).hide();
                $(fieldsConstructor.params.fieldsModalTypeLink).hide();
                $(fieldsConstructor.params.additionalBlock.buttonExecute).hide();
                $(g.params.additionalBlock.wrapper).find('select, input').prop('disabled', true);

                $(g.params.listName).prop('disabled', true);
                $(g.params.pullUpBtn).hide().parent('div').removeClass('input-group');

                if (type == FIELD_TYPE_DATALIST_RELATION) {
                    $(g.params.additionalBlock.wrapper).show();
                    $(fieldsConstructor.params.additionalBlock.inlineSearch).show();
                    $(fieldsConstructor.params.additionalBlock.inlineSearchName).prop('disabled', false);
                    $(g.params.additionalBlock.wrapper).find(fieldsConstructor.params.additionalBlock.inlineSearch).find('input').prop('disabled', false);
                } else if (type == FIELD_TYPE_LINK) {
                    $(g.params.additionalBlock.wrapper).show();
                    $(fieldsConstructor.params.fieldsModalTypeLink).show();
                    $(fieldsConstructor.params.typeLink).prop('disabled', false);
                } else if (type == FIELD_TYPE_MULTI_SELECT || type == FIELD_TYPE_LIST) {
                    $(g.params.listName).prop('disabled', false);
                    $(g.params.pullUpBtn).show().parent('div').addClass('input-group');
                } else if (type == FIELD_TYPE_BUTTON) {
                    $(g.params.additionalBlock.wrapper).show();
                    $(fieldsConstructor.params.additionalBlock.buttonExecute).show();
                    $(fieldsConstructor.params.additionalBlock.buttonExecute).find('select, input').prop('disabled', false);
                    $(fieldsConstructor.params.executeFunctionInput).change();
                    $(fieldsConstructor.params.executeCustomInput).change();
                    fieldsConstructor.fillCustomFunction(true, $(g.params.additionalBlock.wrapper));
                } else {
                    $(fieldsConstructor.params.externalLinkBlock).hide();
                    $(fieldsConstructor.params.additionalBlock.buttonExecute).show();
                }

                if (!select) screenCreator.appendToSelect(g.params.formatType, '', '-- Empty --');
            })
            .on('click', '.' + g.params.rightArrow + ', .' + g.params.leftArrow + ', .' + g.params.upArrow + ', .' + g.params.downArrow, function () {
                row = $(this).parents('.section-panel').attr('data-row');
                col = $(this).parents('.section-panel').attr('data-col');

                var template = screenCreator.getTemplate(row, col),
                    index = ($(this).hasClass(g.params.upArrow) || $(this).hasClass(g.params.downArrow)) ? $(this).parents('tr').index() : $(this).parents('th').index(),
                    newIndex = ($(this).hasClass(g.params.rightArrow) || $(this).hasClass(g.params.downArrow)) ? index + 1 : index - 1;

                template['layout_configuration']['params'].move(index, newIndex);
                screenCreator.saveTemplate(template);
            })
            .on('change', g.params.formatType, function () {
                var paramValue = $(g.params.paramType).val(),
                    formatValue = $(this).val();

                fieldsConstructor.clearOption(true);
                if (paramValue == FIELD_TYPE_LINK) {
                    if(formatValue == FORMAT_TYPE_LINK_LOCAL) {
                        fieldsConstructor.setDataFieldLink();
                    }
                }
            })
    };

    this.resetModal = function () {
        screenCreator.setInfo(this.params.modalBody, false);
        $(this.params.form).each(function () {
            this.reset();
        });

        $(this.params.paramType).trigger('change');
    };

    /**
     * Saving settings of TABLE type sections. Added settings of TABLE type to general tab template object
     * @param {number} row
     * @param {number} col
     * @param {string} paramName - Name of checked values
     * @param {Object} data
     */
    this.saveTemplate = function (row, col, paramName, data) {
        var flag = false;

        $.each(screenCreator.templateLayout, function (index, value) {
            if (value['row_num'] == row && value['col_num'] == col && value['layout_table']) {
                screenCreator.templateLayout[index]['layout_table']['column_configuration'][paramName] = data;
                flag = true;
            }
        });

        if (!flag) screenCreator.setInfo(g.params.modalBody, 'danger', 'First configure');
    };

    this.getTemplate = function (row, col) {
        var result = screenCreator.getTemplate(row, col);
        return result['layout_table'];
    };

    /**
     * Getting result table after settings. Table with configured parameters for section with TABLE type
     * @param {Object} template
     * @returns {object}
     */
    this.getSectionTable = function (template) {
        var g = this,
            resultObject = [],
            pushData = {},
            layoutTable = template['layout_table'];

        if (!layoutTable) return false;

        var isLeftOrientation = (layoutTable && layoutTable['label_orientation'] == 'LEFT');
        if (layoutTable['count'] < 1) layoutTable['count'] = 1;
        $.each(template['layout_configuration']['params'], function (index, value) {
            var labels = template['layout_configuration']['labels'];
            if (value in labels) {
                if (isLeftOrientation) {
                    var pushArray = [labels[value]];
                    for (var i = 1; i <= layoutTable['count']; i++) {
                        pushArray.push('...');
                    }
                    resultObject.push(pushArray);
                } else pushData[labels[value]] = '...';
            }
        });

        for (var i = 1; i <= layoutTable['count']; i++) {
            resultObject.push(pushData);
        }

        var table = screenCreator.getTable(resultObject, null, isLeftOrientation),
            th = table.find('th');

        th.each(function (index) {
            var t = $(this),
                formattingObject = {},
                dataName =  template['layout_configuration']['params'][index],
                formatting = screenCreator.getFormatting(template['row_num'], template['col_num']),
                settingsBtn = $('<span />', {
                    "class": 'glyphicon glyphicon-cog ' + g.params.button,
                    "data-target": '#table-modal',
                    "data-toggle": 'modal'
                });
            t.attr("data-name", template['layout_configuration']['params'][index]);

            $.each(template['layout_table']['column_configuration'][dataName], function(i, param) {
                if (param.value) formattingObject[param.name] = param.value;
            });

            formattingObject = $.extend({}, formatting, formattingObject);
            screenCreator.setStyles(t, formattingObject, 'label');

            if (index > 0) {
                if (layoutTable['label_orientation'] == 'TOP') {
                    t.prepend($('<span />', {
                        "class": 'glyphicon glyphicon-chevron-left ' + g.params.leftArrow,
                    }));
                } else {
                    t.prepend($('<span />', {
                        "class": 'glyphicon glyphicon-arrow-up ' + g.params.upArrow,
                    }));
                }
            }
            if ((index + 1) < th.length) {
                if (layoutTable['label_orientation'] == 'TOP') {
                    t.append($('<span />', {
                        "class": 'glyphicon glyphicon-chevron-right ' + g.params.rightArrow,
                    }));
                } else {
                    t.append($('<span />', {
                        "class": 'glyphicon glyphicon-arrow-down ' + g.params.downArrow,
                    }));
                }
            }
            t.append(settingsBtn);
        });

        return table;
    };

    /**
     * Update sections with TABLE type. Used after saving
     */
    this.updateSectionsInfo = function () {
        var g = this,
            bodyClass = '.panel-body';

        $.each(screenCreator.templateLayout, function (index, val) {
            if (val['layout_type'] == "TABLE") {
                var panelElement = screenCreator.param.panelBlock + '[data-row="' + val['row_num'] + '"][data-col="' + val['col_num'] + '"]',
                    table = g.getSectionTable(val);

                if (table) table = g.configureTable(table, val['row_num'], val['col_num']);

                $(panelElement).find(bodyClass).html(table);
                $(panelElement).find(fieldsConstructor.params.button).hide();
                $(panelElement).find(fieldsConstructor.params.labelFieldConfig).hide();
                $(panelElement).find(fieldsConstructor.params.buttonFieldConfig).hide();
            }
        });
    };

    /**
     * Configure table of the specified settings.
     * @param {object} table - Object of table
     * @param {number} row
     * @param {number} col
     * @returns {object}
     */
    this.configureTable = function (table, row, col) {
        var g = this,
            tableTemplate = this.getTemplate(row, col);

        if (tableTemplate && tableTemplate['column_configuration']) {
            $.each(tableTemplate['column_configuration'], function (dataName, item) {
                $.each(item, function (i, field) {
                    var indexOfTH;

                    switch (field['name']) {
                        case 'label_align':
                            table.find('th[data-name="' + dataName + '"]').css('text-align', field['value']);
                            break;
                        case 'show_column':
                            if (!field['value']) {
                                table = g.hideColumn(table, dataName, tableTemplate['label_orientation']);
                            }
                            break;
                        case 'filter_column':
                            if (field['value']) {
                                table = g.addFilterRow(table, tableTemplate['label_orientation']);
                                table = g.addInputToFilter(table, dataName, tableTemplate['label_orientation']);
                            }
                            break;
                        case 'default_filter_value':
                            if (field['value']) {
                                if (tableTemplate['label_orientation'] == "TOP") {
                                    indexOfTH = table.find('th[data-name="' + dataName + '"]').index();
                                    table.find('.' + g.params.filterRow + ' td input').eq(indexOfTH).val(field['value']);
                                } else if (tableTemplate['label_orientation'] == "LEFT") {
                                    indexOfTH = table.find('th[data-name="' + dataName + '"]').parents('tr').index();
                                    table.find('.' + g.params.filterRow).eq(indexOfTH).find('input').val(field['value']);
                                }
                            }
                            break;
                    }
                });
            });
        }

        return table;
    };

    /**
     * Hide column with filter type. Add opacity for filter column
     * @param {Object} table
     * @param {string} dataName
     * @param {string} labelOrientation - TOP or LEFT
     * @returns {*}
     */
    this.hideColumn = function (table, dataName, labelOrientation) {
        var indexOfTH;

        if (labelOrientation == "TOP") {
            indexOfTH = table.find('th[data-name="' + dataName + '"]').index();

            table.find('tr').each(function () {
            });
        } else if (labelOrientation == "LEFT") {
            indexOfTH = table.find('th[data-name="' + dataName + '"]').parents('tr').index();
        }

        return table;
    };

    /**
     * Added filter row under header. Used if table has columns for filter
     * @param {Object} table
     * @param {string} labelOrientation - TOP or LEFT
     * @returns {*}
     */
    this.addFilterRow = function (table, labelOrientation) {
        var g = this;

        if (table.find('.' + this.params.filterRow).length > 0) return table;

        if (labelOrientation == "TOP") {
            var tr = $('<tr />', {"class": this.params.filterRow});
            table.find('th').each(function () {
                $('<td />').appendTo(tr);
            });

            table.find('thead').append(tr);
        } else if (labelOrientation == "LEFT") {
            table.find('th').each(function () {
                $('<td />', {"class": g.params.filterRow}).insertAfter($(this));
            });
        }

        return table;
    };

    /**
     * Added filter input fields to row. Used if user added filter row in column
     * @param {Object} table
     * @param {string} dataName
     * @param {string} labelOrientation - TOP or LEFT
     * @returns {*}
     */
    this.addInputToFilter = function (table, dataName, labelOrientation) {
        var indexOfTH, selector,
            input = $('<input />', {type: 'text', "class": 'form-control'});

        if (labelOrientation == "TOP") {
            indexOfTH = table.find('th[data-name="' + dataName + '"]').index();
            selector = table.find('.' + this.params.filterRow + ' td');
        } else if (labelOrientation == "LEFT") {
            indexOfTH = table.find('th[data-name="' + dataName + '"]').parents('tr').index();
            selector = table.find('.' + this.params.filterRow);
        }

        selector.eq(indexOfTH).html(input);
        return table;
    };
};

function isBase64(str) {
    var base64Matcher = new RegExp("^(?:[A-Za-z0-9+/]{4})*(?:[A-Za-z0-9+/]{2}==|[A-Za-z0-9+/]{3}=|[A-Za-z0-9+/]{4})$");
    if (!base64Matcher.test(str)) {
        return false;
    }
    return true;
}

var screenCreator = new creatorApp();
screenCreator.load();

var firstStepConfig = new firstStepApp();
firstStepConfig.load();

var fieldsConstructor = new fieldsConstructorApp();
fieldsConstructor.load();

var tableConstructor = new tableConstructorApp();
tableConstructor.load();