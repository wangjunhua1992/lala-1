var a = getApp();

Page({
    data: {
        Lang: a.Lang,
        wuiLoading: {
            show: !0,
            img: a.util.getStorageSync("theme.loading.img")
        }
    },
    onLoad: function(t) {
        var e = this;
        t.id && (e.data.id = t.id), a.util.request({
            url: "wmall/order/index/pindan_detail",
            data: {
                id: e.data.id
            },
            success: function(t) {
                if (a.util.loaded(), (i = t.data.message).errno) return a.util.toast(i.message), 
                !1;
                var i = i.message;
                e.setData(i);
            }
        });
    }
});