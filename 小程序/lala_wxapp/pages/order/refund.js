var a = getApp();

Page({
    data: {
        Lang: a.Lang,
        wuiLoading: {
            show: !0,
            img: a.util.getStorageSync("theme.loading.img")
        }
    },
    onLoad: function(e) {
        var t = this;
        e && e.id && (t.data.id = e.id), a.util.request({
            url: "wmall/order/index/refund",
            data: {
                id: t.data.id
            },
            success: function(e) {
                a.util.loaded();
                var d = e.data.message;
                if (d.errno) return a.util.toast(d.message), !1;
                d = d.message, t.setData({
                    refunds: d.refunds
                });
            }
        });
    }
});