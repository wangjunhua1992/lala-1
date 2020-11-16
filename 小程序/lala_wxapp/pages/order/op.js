var a = getApp();

Page({
    data: {
        id: 0,
        Lang: a.Lang,
        wuiLoading: {
            show: !1
        }
    },
    onLoad: function(t) {
        var e = this;
        t && t.id > 0 && (e.data.id = t.id), a.util.request({
            url: "wmall/order/index/order_info_update",
            data: {
                id: e.data.id
            },
            success: function(t) {
                a.util.loaded();
                var d = t.data.message;
                if (d.errno) return a.util.toast(d.message), !1;
                e.setData({
                    order: d.message.order
                });
            }
        });
    },
    onJsEvent: function(t) {
        a.util.jsEvent(t);
    }
});