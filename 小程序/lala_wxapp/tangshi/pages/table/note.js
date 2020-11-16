var t = getApp(), i = require("../../../static/js/utils/underscore.js");

Page({
    data: {
        person: [ "1人", "2人", "3人" ],
        invoiceId: 0,
        Lang: t.Lang,
        wuiLoading: {
            show: !0,
            img: t.util.getStorageSync("theme.loading.img")
        },
        is_pindan: 0,
        pindan_id: 0
    },
    onLoad: function(a) {
        var e = this;
        e.data.sid = a.sid, a.pindan_id > 0 && (e.data.is_pindan = a.is_pindan, e.data.pindan_id = a.pindan_id), 
        t.util.request({
            url: "wmall/store/table/note",
            data: {
                sid: a.sid
            },
            success: function(n) {
                t.util.loaded();
                var d = n.data.message.message, o = {
                    order_note: d.store.order_note,
                    invoice_status: d.store.invoice_status,
                    invoiceId: a.invoiceId,
                    sid: d.store.id,
                    is_pindan: e.data.is_pindan,
                    pindan_id: e.data.pindan_id
                };
                (o = i.extend(o, t.util.getStorageSync("order.note"))).invoice_status && (o.invoices = d.invoices), 
                e.setData(o);
            }
        });
    },
    onNote: function(t) {
        this.setData({
            note: t.detail.value
        });
    },
    onChooseInvoice: function(t) {
        var i = this, a = t.currentTarget.dataset.id;
        i.setData({
            invoiceId: a
        });
    },
    onChooseLabel: function(t) {
        var i = this, a = i.data.note;
        a || (a = ""), a = a + " " + t.currentTarget.dataset.note, i.setData({
            note: a
        });
    },
    onSubmit: function(a) {
        var e = this, n = {
            note: e.data.note,
            invoiceId: e.data.invoiceId
        }, d = t.util.getStorageSync("order.extra");
        d && (d = i.extend(d, n)), t.util.setStorageSync("order.extra", d), t.util.setStorageSync("order.note", n), 
        wx.redirectTo({
            url: "./create?sid=" + e.data.sid + "&table_id=" + d.table_id + "&pindan_id=" + e.data.pindan_id + "&is_pindan=" + e.data.is_pindan
        });
    }
});