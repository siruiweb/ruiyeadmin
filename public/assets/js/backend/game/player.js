define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            Table.api.init({
                extend: {
                    index_url: 'game/player/index',
                    edit_url: 'game/player/edit',
                    del_url: 'game/player/del',
                    table: 'xt_player',
                }
            });

            var table = $("#table");

            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id'), sortable: true},
                        {field: 'player_id', title: __('Player_id'), operate: 'LIKE'},
                        {field: 'username', title: __('Username'), operate: 'LIKE'},
                        {field: 'level', title: __('Level'), operate: 'BETWEEN'},
                        {field: 'experience', title: __('Experience'), operate: 'BETWEEN'},
                        {field: 'spirit', title: __('Spirit'), operate: 'BETWEEN'},
                        {field: 'gold', title: __('Gold'), operate: 'BETWEEN'},
                        {field: 'diamond', title: __('Diamond'), operate: 'BETWEEN'},
                        {field: 'stamina', title: __('Stamina'), operate: 'BETWEEN'},
                        {field: 'last_login_time_text', title: __('Last Login Time'), formatter: Table.api.formatter.datetime},
                        {field: 'last_login_ip', title: __('Loginip'), operate: false},
                        {field: 'status', title: __('Status'), formatter: Table.api.formatter.status, searchList: {normal: __('Normal'), banned: __('Banned')}},
                        {field: 'create_time', title: __('Createtime'), formatter: Table.api.formatter.datetime, operate: 'RANGE', addclass: 'datetimerange'},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
            });

            Table.api.bindevent(table);
        },
        edit: function () {
            Controller.api.bindevent();
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            }
        }
    };
    return Controller;
});
