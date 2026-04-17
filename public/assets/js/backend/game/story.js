define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            Table.api.init({
                extend: {
                    index_url: 'game/story/index',
                    add_url: 'game/story/add',
                    edit_url: 'game/story/edit',
                    del_url: 'game/story/del',
                    table: 'xt_story',
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
                        {field: 'chapter', title: __('Chapter'), operate: 'BETWEEN'},
                        {field: 'title', title: __('Title'), operate: 'LIKE'},
                        {field: 'content', title: __('Content'), operate: false},
                        {field: 'type', title: __('Type'), formatter: Table.api.formatter.label, searchList: {main: __('Main'), branch: __('Branch'), event: __('Event')}},
                        {field: 'next_id', title: __('Next_id'), operate: 'BETWEEN'},
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
