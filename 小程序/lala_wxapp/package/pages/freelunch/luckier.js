var a = getApp();

Page({
    data: {
        luckiers: {
            loaded: 0,
            empty: 0,
            data: []
        },
        Lang: a.Lang,
        wuiLoading: {
            show: !0,
            img: a.util.getStorageSync("theme.loading.img")
        }
    },
    onJsEvent: function(t) {
        a.util.jsEvent(t);
    },
    onLoad: function(a) {
        this.onReachBottom();
    },
    onReady: function() {},
    onShow: function() {},
    onHide: function() {},
    onUnload: function() {},
    onReachBottom: function() {
        var t = this;
        if (-1 == t.data.min) return !1;
        a.util.request({
            url: "freeLunch/freeLunch/luckier",
            data: {
                min: t.data.min
            },
            success: function(e) {
                a.util.loaded();
                var n = t.data.luckiers.data.concat(e.data.message.message);
                t.data.luckiers.data = n, n.length || (t.data.luckiers.empty = 1);
                var o = e.data.message.min;
                !o && n.length > 0 && (t.data.luckiers.loaded = 1), o || (o = -1), t.setData({
                    luckiers: t.data.luckiers,
                    min: o
                });
            }
        });
    },
    onShareAppMessage: function() {},
    onPullDownRefresh: function() {
        var a = this;
        a.data.min = 0, a.data.luckiers = {
            loaded: 0,
            empty: 0,
            data: []
        }, a.onReachBottom(), wx.stopPullDownRefresh();
    }
});