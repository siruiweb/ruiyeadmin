define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            Table.api.init({
                extend: {
                    index_url: 'game/task/index',
                    add_url: 'game/task/add',
                    edit_url: 'game/task/edit',
                    del_url: 'game/task/del',
                    table: 'xt_task',
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
                        {field: 'name', title: __('Name'), operate: 'LIKE'},
                        {field: 'type', title: __('Type'), formatter: Table.api.formatter.label, searchList: {daily: __('Daily'), weekly: __('Weekly'), achievement: __('Achievement'), main: __('Main')}},
                        {field: 'description', title: __('Description'), operate: false},
                        {field: 'target_type', title: __('Target_type'), operate: 'LIKE'},
                        {field: 'target_count', title: __('Target_count'), operate: 'BETWEEN'},
                        {field: 'sort', title: __('Sort'), operate: 'BETWEEN'},
                        {field: 'status', title: __('Status'), formatter: Table.api.formatter.status, searchList: {normal: __('Normal'), hidden: __('Hidden')}},
                        {field: 'create_time', title: __('Createtime'), formatter: Table.api.formatter.datetime, operate: 'RANGE', addclass: 'datetimerange'},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
            });

            Table.api.bindevent(table);
        },
        add: function () {
            Controller.api.bindevent();
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
