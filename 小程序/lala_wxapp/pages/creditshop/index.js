var o = getApp();

Page({
    data: {
        goods: {
            page: 1,
            psize: 20,
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
        var a = this;
        o.util.request({
            url: "creditshop/index",
            data: {
                page: a.data.goods.page,
                psize: a.data.goods.psize
            },
            success: function(d) {
                o.util.loaded();
                var t = d.data.message.message;
                t.goods.length || (a.data.goods.empty = !0), t.goods && t.goods.length < a.data.goods.psize && (a.data.goods.loaded = !0), 
                a.data.goods.page++, a.data.goods.data = t.goods, a.setData({
                    adv: t.adv,
                    category: t.category,
                    goods: a.data.goods,
                    member: t.member
                });
            }
        });
    },
    onReady: function() {},
    onShow: function() {},
    onHide: function() {},
    onUnload: function() {},
    onPullDownRefresh: function() {
        var o = this;
        o.data.goods = {
            page: 1,
            psize: 20,
            empty: !1,
            loaded: !1,
            data: []
        }, o.onLoad(), wx.stopPullDownRefresh();
    },
    onReachBottom: function() {
        var a = this;
        a.data.goods.loaded || o.util.request({
            url: "creditshop/index/goods",
            data: {
                page: a.data.goods.page,
                psize: a.data.goods.psize
            },
            success: function(d) {
                var t = d.data.message;
                if (t.errno) return o.util.toast(t.message), !1;
                t = t.message, a.data.goods.data = a.data.goods.data.concat(t.goods), t.goods.length || (a.data.goods.empty = !0), 
                t.goods && t.goods.length < a.data.goods.psize && (a.data.goods.loaded = !0), a.data.goods.page++, 
                a.setData({
                    goods: a.data.goods
                });
            }
        });
    },
    onShareAppMessage: function() {},
    onJsEvent: function(a) {
        o.util.jsEvent(a);
    }
});