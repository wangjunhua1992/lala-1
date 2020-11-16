var t = getApp();

Page({
    data: {
        showloading: !1,
        windowHeight: 0,
        Lang: t.Lang,
        wuiLoading: {
            show: !0,
            img: t.util.getStorageSync("theme.loading.img")
        }
    },
    onLoad: function() {
        var a = this;
        t.util.request({
            url: "spread/poster",
            success: function(e) {
                return t.util.loaded(), e.data.message.errno ? (t.util.toast(e.data.message.message), 
                !1) : (a.setData(e.data.message.message), e.data.message.message.respon ? void 0 : (a.setData({
                    showloading: !0
                }), !1));
            }
        }), wx.getSystemInfo({
            success: function(t) {
                var e = t.windowHeight;
                a.setData({
                    windowHeight: e
                });
            }
        });
    },
    onImageLoad: function() {
        var t = this, a = t.data.windowHeight;
        t.setData({
            windowHeight: a
        });
    },
    onJsEvent: function(a) {
        t.util.jsEvent(a);
    }
});