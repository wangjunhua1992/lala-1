var a = getApp();

Page({
    data: {
        showNoData: !1,
        comments: [],
        shareData: {
            title: "",
            path: "",
            success: function() {},
            fail: function() {}
        },
        Lang: a.Lang,
        wuiLoading: {
            show: !0,
            img: a.util.getStorageSync("theme.loading.img")
        }
    },
    onLoad: function(a) {
        this.onReachBottom();
    },
    onReady: function() {},
    onShow: function() {},
    onHide: function() {},
    onUnload: function() {},
    onPullDownRefresh: function() {},
    onReachBottom: function() {
        var t = this;
        if (console.log("+++++++++++"), console.log(t.data.min), -1 == t.data.min) return !1;
        var n = {
            min: t.data.min
        };
        a.util.request({
            url: "ordergrant/share",
            data: n,
            success: function(n) {
                a.util.loaded();
                var o = t.data.comments.concat(n.data.message.message);
                if (!o.length) return t.setData({
                    showNoData: !0
                }), !1;
                var e = n.data.message.min;
                t.setData({
                    min: e,
                    comments: o
                }), n.data.message.min || (t.data.min = -1);
            }
        });
    },
    onShareAppMessage: function() {
        return this.data.shareData;
    },
    onJsEvent: function(t) {
        a.util.jsEvent(t);
    }
});