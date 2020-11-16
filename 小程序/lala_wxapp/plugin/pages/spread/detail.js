var e = getApp();

Page({
    data: {
        Lang: e.Lang,
        wuiLoading: {
            show: !0,
            img: e.util.getStorageSync("theme.loading.img")
        }
    },
    onLoad: function(a) {
        var t = this;
        e.util.request({
            url: "spread/order/detail",
            data: {
                order_type: a.order_type || "takeout",
                id: a.id
            },
            success: function(a) {
                e.util.loaded(), t.setData({
                    detail: a.data.message.message
                });
            }
        });
    }
});