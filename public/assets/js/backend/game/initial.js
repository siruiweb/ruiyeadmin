define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            Table.api.init({
                extend: {
                    index_url: 'game/initial/index',
                    edit_url: 'game/initial/edit',
                    del_url: 'game/initial/del',
                    table: 'xt_initial',
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
                        {field: 'player_name', title: __('Player_name'), operate: 'LIKE'},
                        {field: 'content', title: __('Content'), operate: false},
                        {field: 'word_count', title: __('Word_count'), operate: 'BETWEEN'},
                        {field: 'likes', title: __('Likes'), operate: 'BETWEEN', sortable: true},
                        {field: 'is_top', title: __('Is_top'), formatter: Table.api.formatter.label, searchList: {0: __('No'), 1: __('Yes')}},
                        {field: 'status', title: __('Status'), formatter: Table.api.formatter.status, searchList: {normal: __('Normal'), hidden: __('Hidden')}},
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
