var a = getApp();

Page({
    data: {
        bargain: {
            page: 1,
            psize: 10,
            loaded: 0,
            empty: 0,
            goods: []
        },
        shareData: {
            title: "天天特价",
            path: "/plugin/pages/bargain/index",
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
    onReachBottom: function() {
        var t = this;
        if (t.data.bargain.loaded) return !1;
        a.util.request({
            url: "bargain/index",
            data: {
                page: t.data.bargain.page,
                psize: t.data.bargain.psize,
                forceLocation: 1
            },
            success: function(n) {
                a.util.loaded();
                var e = n.data.message;
                if (0 != e.errno) return a.util.toast(e.message), !1;
                t.data.bargain.page++, t.data.bargain.goods = t.data.bargain.goods.concat(e.message.bargains), 
                t.data.bargain.goods.length || (t.data.bargain.empty = 1), e.message.bargains.length < t.data.bargain.psize && (t.data.bargain.loaded = 1), 
                t.setData({
                    bargain: t.data.bargain,
                    config: e.message.config
                });
            }
        });
    },
    onJsEvent: function(t) {
        a.util.jsEvent(t);
    },
    onShareAppMessage: function() {
        return this.data.shareData;
    },
    onPullDownRefresh: function() {
        var a = this;
        a.data.bargain.page = 1, a.data.bargain.goods = [], a.data.bargain.loaded = 0, a.onReachBottom(), 
        wx.stopPullDownRefresh();
    }
});