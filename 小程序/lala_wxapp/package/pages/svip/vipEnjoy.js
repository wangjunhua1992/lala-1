var o = getApp();

Page({
    data: {
        goods: {
            page: 1,
            psize: 15,
            empty: !1,
            loaded: !1,
            data: []
        },
        Lang: o.Lang,
        wuiLoading: {
            show: !0,
            img: o.util.getStorageSync("theme.loading.img")
        }
    },
    onLoad: function() {
        this.onReachBottom();
    },
    onJsEvent: function(a) {
        o.util.jsEvent(a);
    },
    onReady: function() {},
    onShow: function() {},
    onHide: function() {},
    onUnload: function() {},
    onPullDownRefresh: function() {
        var o = this;
        o.data.goods = {
            page: 1,
            psize: 15,
            empty: !1,
            loaded: !1,
            data: []
        }, o.onReachBottom(), wx.stopPullDownRefresh();
    },
    onReachBottom: function() {
        var a = this;
        a.data.goods.loaded || o.util.request({
            url: "svip/goods/index",
            data: {
                page: a.data.goods.page,
                psize: a.data.goods.psize
            },
            success: function(t) {
                o.util.loaded();
                var d = t.data.message;
                if (d.errno) return o.util.toast(d.message), !1;
                d = d.message, a.data.goods.data = a.data.goods.data.concat(d.goods), a.data.goods.data.length || (a.data.goods.empty = !0), 
                d.goods && d.goods.length < a.data.goods.psize && (a.data.goods.loaded = !0), a.data.goods.page++, 
                a.setData({
                    goods: a.data.goods
                });
            }
        });
    },
    onShareAppMessage: function() {}
});