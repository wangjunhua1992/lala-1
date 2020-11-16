var t = getApp();

Page({
    data: {
        logo: "",
        title: "",
        Lang: t.Lang,
        wuiLoading: {
            show: !0,
            img: t.util.getStorageSync("theme.loading.img")
        }
    },
    onLoad: function(n) {
        var a = this, i = t.util.getStorageSync("mallBasic");
        a.setData(i);
    },
    onCancel: function() {
        var n = t.util.getStorageSync("backUrl");
        t.util.jump2url(n, "redirectTo");
    },
    onShow: function() {},
    onJsEvent: function(n) {
        t.util.jsEvent(n);
    }
});