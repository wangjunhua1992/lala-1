var t = getApp();

Page({
    data: {
        Lang: t.Lang,
        wuiLoading: {
            show: !0,
            img: t.util.getStorageSync("theme.loading.img")
        }
    },
    onLoad: function(a) {
        var e = this;
        if (!a.id) return t.util.toast("参数错误"), !1;
        t.util.request({
            url: "spread/current/detail",
            data: {
                id: a.id
            },
            success: function(a) {
                t.util.loaded();
                var i = a.data.message.message;
                i.errno ? t.util.toast(i.message) : e.setData(i);
            }
        });
    }
});