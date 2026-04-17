define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            Table.api.init({
                extend: {
                    index_url: 'game/tiandao/index',
                    add_url: 'game/tiandao/add',
                    edit_url: 'game/tiandao/edit',
                    del_url: 'game/tiandao/del',
                    table: 'xt_tiandao',
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
                        {field: 'category', title: __('Category'), operate: 'LIKE'},
                        {field: 'question', title: __('Question'), operate: false},
                        {field: 'answer', title: __('Answer'), operate: false},
                        {field: 'difficulty', title: __('Difficulty'), formatter: Table.api.formatter.label, searchList: {1: '★☆☆☆☆', 2: '★★☆☆☆', 3: '★★★☆☆', 4: '★★★★☆', 5: '★★★★★'}},
                        {field: 'score', title: __('Score'), operate: 'BETWEEN'},
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
