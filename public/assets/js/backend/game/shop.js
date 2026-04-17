define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            Table.api.init({
                extend: {
                    index_url: 'game/shop/index',
                    add_url: 'game/shop/add',
                    edit_url: 'game/shop/edit',
                    del_url: 'game/shop/del',
                    table: 'xt_shop',
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
                        {field: 'type', title: __('Type'), operate: 'LIKE'},
                        {field: 'price', title: __('Price'), operate: 'BETWEEN'},
                        {field: 'currency', title: __('Currency'), formatter: Table.api.formatter.label, searchList: {gold: __('Gold'), diamond: __('Diamond'), rmb: __('Rmb')}},
                        {field: 'stock', title: __('Stock'), operate: 'BETWEEN'},
                        {field: 'limit_type', title: __('Limit_type'), formatter: Table.api.formatter.label, searchList: {none: __('None'), daily: __('Daily'), weekly: __('Weekly'), monthly: __('Monthly')}},
                        {field: 'limit_count', title: __('Limit_count'), operate: 'BETWEEN'},
                        {field: 'sort', title: __('Sort'), operate: 'BETWEEN'},
                        {field: 'status', title: __('Status'), formatter: Table.api.formatter.status, searchList: {normal: __('Normal'), hidden: __('Hidden'), soldout: __('Soldout')}},
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
