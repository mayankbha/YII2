/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 */

var CustomQueryBuilderApp = function () {
    this.form = '#query-builder-form';
    this.selectTable = '#query-table-name';
    this.selectType = '#query-select-type';
    this.block = '#query-builder';
    this.hiddenInput = '#query-builder-result';
    this.paramsInput = '#query-builder-params';
    this.PKsInput = '#query-builder-pks';
    this.outParamsInput = '#query-out-params';
    this.queryPksInput = '#query-pks';
    this.tablesInfo = null;

    this.paramsArray = [];
    this.PKsArray = [];

    this.load = function () {
        var me = this;
        $(document).ready(function () {
            var sql = $(me.hiddenInput).val(),
                outList, rules;
            
            me.paramsArray = $(me.paramsInput).val().split(', ');
            me.PKsArray = $(me.PKsInput).val().split(', ');

            me.bindLoadEvents();
            if (sql && (rules = me.getRules(sql))) {
                $(me.selectTable).val(rules.from).trigger('change');
                $(me.selectType).val(rules.type);

                outList = rules.select.split(', ');
                $.each(outList, function (i, name) {
                    $(me.outParamsInput).find('option[value="' + name + '"]').prop('selected', true);
                });

                $.each(me.PKsArray, function (i, item) {
                    $(me.queryPksInput).find('option[value="' + item + '"]').prop('selected', true);
                });

                if (rules.where) {
                    $(me.block).queryBuilder('setRulesFromSQL', rules.where);
                }
            }
        });
    };

    this.getGroups = function (table) {
        return {
            columns: {
                name: 'Columns',
                prefix: table + '.',
            },
            params: {
                name: 'Params',
                prefix: ":",
            }
        }
    };

    this.setTablesInfo = function (params) {
        this.tablesInfo = params;
        this.load();
    };

    this.bindLoadEvents = function () {
        var me = this,
            currentParams;

        $(document)
            .on('change', me.selectTable, function () {
                var value = $(this).val(),
                    translateValue = null;

                if (me.tablesInfo[value]) {
                    currentParams = me.tablesInfo[value];
                    translateValue = me.translateApiResult(value, me.tablesInfo[value]);

                    $(me.outParamsInput).html('').parents('.form-group').show();
                    $(me.queryPksInput).html('').parents('.form-group').show();
                    $.each(currentParams['fields'], function (i, item) {
                        var param = value + '.' + Object.keys(item)[0];
                        $(me.outParamsInput).append($('<option />', {text: param, value: param}));
                        $(me.queryPksInput).append($('<option />', {text: param, value: param}));
                    });
                    $(me.outParamsInput).trigger('change');
                    $(me.queryPksInput).trigger('change');

                    me.startBuilder(value, translateValue);
                } else {
                    $(me.hiddenInput).val('');
                    $(me.block).queryBuilder('destroy');
                    $(me.outParamsInput).html('').parents('.form-group').hide();
                    $(me.queryPksInput).html('').parents('.form-group').hide();
                }
            })
            .on('submit', me.form, function () {
                var rules = $(me.block).queryBuilder('getSQL'),
                    outParams = $(me.outParamsInput).val(),
                    table = currentParams['table_name'];

                var query = me.buildQuery(rules.sql, outParams, table),
                    params = me.paramsArray.join(', '),
                    PKs = $(me.queryPksInput).val().join(', ');

                $(me.hiddenInput).val(query);
                $(me.paramsInput).val(params);
                $(me.PKsInput).val(PKs);
            })
    };

    this.buildQuery = function (rules, outParams, table) {
        var selectType = $(this.selectType).val();

        rules = rules.replace(/(^|[^\w\d'])([\w\d]+)\.([\w\d]+)/ig, '$1"$2"."$3"');

        table = '"' + table + '"';

        outParams = outParams.map(function (value) {
            value = value.replace('.', '"."');
            return '"' + value + '"';
        });
        outParams = outParams.join(', ');

        return selectType + ' ' + outParams + ' FROM ' + table + ' WHERE ' + rules;
    };

    this.getRules = function (sql) {
        var match = sql.match(/(SELECT\s*(?:DISTINCT)*)\s+([\w\d\s,.*"]+)\s+FROM\s+([\w."]+)\s*(?:WHERE)*\s*(.*)/i);

        if (match) {
            for (var i = 1; i < match.length; i++) {
                match[i] = match[i].replace(/"/ig, '');
            }

            return {
                type: match[1],
                select: match[2],
                from: match[3],
                where: match[4],
            };
        }

        return null;
    };

    this.translateTypes = function (type) {
        switch(type) {
            case 'character varying':
                return 'string';
            case 'double precision':
                return 'double';
            case 'timestamp without time zone':
                return 'datetime';
            default:
                return type;
        }
    };

    this.translateApiResult = function(table, params) {
        var me = this,
            resultFilterParams = [],
            groups = me.getGroups(table);

        const TYPE_PARAM = 'PARAM';
        const TYPE_VALUE = 'VALUE';

        if (params['fields']) {
            $.each(groups, function (groupName, groupData) {
                $.each(params['fields'], function (j, item) {
                    var id = Object.keys(item)[0];

                    resultFilterParams.push({
                        id:  groupData['prefix'] + id,
                        optgroup: groupData['name'],
                        type: 'string',
                        operators: (groups.params.name == groupData['name']) ? ['is_empty_or_null'] : null,
                        input: function (rule, name) {
                            var select = $('<select />', {name: name + '_1', class: 'form-control'}),
                                input = $('<input />', {type: 'text', name: name + '_2', class: 'form-control'}),
                                container = rule.$el.find('.rule-value-container');

                            select.append($('<option />', {value: TYPE_PARAM, text: 'input parameter'}));
                            select.append($('<option />', {value: TYPE_VALUE, text: 'value:', selected: true}));

                            rule.$el.find('.btn[data-delete="rule"]').on('click', function () {
                                var type = rule.$el.find('.rule-value-container [name$=_1]').val(),
                                    value = rule.$el.find('.rule-value-container [name$=_2]').val(),
                                    indexParam = me.paramsArray.indexOf(value);

                                if (value && type == TYPE_PARAM && indexParam > -1) {
                                    me.paramsArray.splice(indexParam, 1);
                                }
                            });
                            container.on('change', '[name=' + name + '_1]', function () {
                                var input = container.find('[name$=_2]'),
                                    value = rule.$el.find('.rule-filter-container select').val().replace(table + '.', '');

                                if ($(this).val() == TYPE_PARAM) {
                                    input.val(value);
                                    input.hide();
                                } else {
                                    input.show();
                                }
                            });

                            return $.merge(select, input);
                        },
                        valueGetter: function (rule) {
                            var type = rule.$el.find('.rule-value-container [name$=_1]').val(),
                                value = rule.$el.find('.rule-value-container [name$=_2]').val(),
                                indexParam = me.paramsArray.indexOf(value);

                            if (type == TYPE_PARAM) {
                                value = rule.$el.find('.rule-filter-container select').val().replace(table + '.', '');
                                indexParam = me.paramsArray.indexOf(value);

                                if (indexParam == -1) {
                                    me.paramsArray.push(value);
                                }

                                return ':' + value;
                            } else if (indexParam > -1) {
                                me.paramsArray.splice(indexParam, 1);
                            }

                            return value;
                        },
                        valueSetter: function (rule, value) {
                            if (rule.operator.nb_inputs > 0) {
                                if (value.search(":") > -1) {
                                    var paramName = value.substr(1);

                                    rule.$el.find('.rule-value-container [name$=_2]').val(paramName);
                                    rule.$el.find('.rule-value-container [name$=_1]').val(TYPE_PARAM).trigger('change');
                                } else {
                                    rule.$el.find('.rule-value-container [name$=_2]').val(value);
                                    rule.$el.find('.rule-value-container [name$=_1]').val(TYPE_VALUE).trigger('change');
                                }
                            }
                        }
                    });
                });
            });
        }

        return resultFilterParams;
    };

    this.startBuilder = function(table, params) {
        var blockObject = $(this.block),
            me = this;

        me.paramsArray = [];
        blockObject.queryBuilder('destroy');
        blockObject.queryBuilder({
            filters: params,
            allow_empty: true,
            operators:  $.fn.queryBuilder.constructor.DEFAULTS.operators.concat([
                {type: 'is_empty_or_null', nb_inputs: 0, multiple: false, apply_to: ['string', 'number', 'datetime', 'boolean'] }
            ]),
            sqlOperators: {
                is_empty_or_null: { op: 'LIKE \'\''},
            },
            sqlRuleOperator: {
                'LIKE': function(v) {
                    if (v.slice(0, 1) == '%' && v.slice(-1) == '%') {
                        return {
                            val: v.slice(1, -1),
                            op: 'contains'
                        };
                    }
                    else if (v.slice(0, 1) == '%') {
                        return {
                            val: v.slice(1),
                            op: 'ends_with'
                        };
                    }
                    else if (v == '') {
                        return {
                            val: null,
                            op: 'is_empty_or_null'
                        };
                    }
                    else {
                        Utils.error('SQLParse', 'Invalid value for LIKE operator "{0}"', v);
                    }
                }
            }
        })
        .on('ruleToSQL.queryBuilder.filter', function (e, rule) {
            var groups = me.getGroups(table);

            if (rule.id.indexOf(groups['params']['prefix']) >= 0) {
                var itemValue = rule.id.replace(groups['params']['prefix'], '');
                if ($.inArray(itemValue, me.paramsArray) < 0) {
                    me.paramsArray.push(itemValue);
                }
            }

            if (rule.operator == 'is_empty_or_null') {
                e.value = e.value.replace(rule.id, "'" + rule.id + "'");
            }
        });
    };
};

var CustomQueryBuilder = new CustomQueryBuilderApp();