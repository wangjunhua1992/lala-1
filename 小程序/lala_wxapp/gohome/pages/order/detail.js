var e = function(e) {
    return e && e.__esModule ? e : {
        default: e
    };
}(require("../../../static/js/utils/qrcode.js")), t = getApp();

Page({
    data: {
        id: 0,
        Lang: t.Lang,
        wuiLoading: {
            show: !0,
            img: t.util.getStorageSync("theme.loading.img")
        }
    },
    onLoad: function(e) {
        var t = this;
        e && e.id && (t.data.id = e.id), t.onReachBottom();
    },
    newQrcode: function(t) {
        new e.default("canvas", {
            text: t,
            width: 150,
            height: 150,
            colorDark: "#000000",
            colorLight: "#ffffff",
            correctLevel: e.default.correctLevel.H
        });
    },
    onReachBottom: function() {
        var e = this;
        t.util.request({
            url: "gohome/order/detail",
            data: {
                id: e.data.id
            },
            success: function(o) {
                t.util.loaded();
                var a = o.data.message;
                if (a.errno) return t.util.toast(a.message), !1;
                a = a.message, e.newQrcode(a.qrcode), e.setData({
                    order: a.order
                });
            }
        });
    },
    onPullDownRefresh: function() {
        this.onReachBottom(), wx.stopPullDownRefresh();
    },
    onJsEvent: function(e) {
        t.util.jsEvent(e);
    }
});