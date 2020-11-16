var t = getApp();

Page({
    data: {
        showloading: !1,
        Lang: t.Lang,
        wuiLoading: {
            show: !0,
            img: t.util.getStorageSync("theme.loading.img")
        }
    },
    onLoad: function() {
        var a = this;
        t.util.request({
            url: "spread/poster/qrcode",
            success: function(e) {
                if (t.util.loaded(), e.data.message.errno) return t.util.toast(e.data.message.message), 
                !1;
                a.setData(e.data.message);
            }
        });
    },
    onJsEvent: function(a) {
        t.util.jsEvent(a);
    }
});