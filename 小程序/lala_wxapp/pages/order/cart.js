var a = getApp();

Page({
    data: {
        cartsInfo: [],
        min: 0,
        showNodata: !1,
        showloading: !1,
        Lang: a.Lang,
        wuiLoading: {
            show: !0,
            img: a.util.getStorageSync("theme.loading.img")
        }
    },
    onLoad: function(t) {
        var o = this;
        a.util.setStorageSync("pageHome.forceOnload", 1), o.onReachBottom();
    },
    onTurncateCart: function(t) {
        var o = this, n = t.currentTarget.dataset.sid, e = t.currentTarget.dataset.index;
        console.log(n), wx.showModal({
            content: "确定删除该购物车吗?",
            success: function(t) {
                t.confirm && a.util.request({
                    url: "wmall/order/cart/truncate",
                    data: {
                        sid: n
                    },
                    success: function(a) {
                        o.data.cartsInfo.splice(e, 1), o.data.cartsInfo.length <= 0 && (o.data.showNodata = !0), 
                        o.setData({
                            cartsInfo: o.data.cartsInfo,
                            showNodata: o.data.showNodata
                        });
                    }
                });
            }
        });
    },
    onReady: function() {},
    onShow: function() {},
    onHide: function() {},
    onUnload: function() {},
    onPullDownRefresh: function() {
        var a = this;
        a.data.min = 0, a.data.cartsInfo = [], a.onReachBottom(), wx.stopPullDownRefresh();
    },
    onReachBottom: function() {
        var t = this;
        if (-1 == t.data.min) return !1;
        t.setData({
            showloading: !0
        });
        var o = {
            min: t.data.min,
            forceLocation: 1
        };
        a.util.request({
            url: "wmall/order/cart/index",
            data: o,
            success: function(o) {
                a.util.loaded();
                var n = o.data.message;
                if (n.errno) return a.util.toast(n.message), !1;
                var e = t.data.cartsInfo.concat(n.cartsInfo);
                if (!e.length) return t.setData({
                    showNodata: !0,
                    showloading: !1
                }), !1;
                t.setData({
                    cartsInfo: e,
                    min: n.min
                }), n.cartsInfo.length < 10 && (t.data.min = -1), t.setData({
                    showloading: !1
                });
            }
        });
    },
    onShareAppMessage: function() {}
});