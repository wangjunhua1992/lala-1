var a = getApp();

Page(function(a, e, t) {
    return e in a ? Object.defineProperty(a, e, {
        value: t,
        enumerable: !0,
        configurable: !0,
        writable: !0
    }) : a[e] = t, a;
}({
    data: {
        orders: {
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
    onJsEvent: function(e) {
        a.util.jsEvent(e);
    },
    onLoad: function(a) {
        this.onReachBottom();
    },
    onFinishMealPay: function() {
        wx.showModal({
            title: "",
            content: "您的支付方式为餐后支付，请到商家收银台付款",
            success: function(a) {}
        });
    },
    onReady: function() {},
    onShow: function() {},
    onHide: function() {},
    onUnload: function() {},
    onPullDownRefresh: function() {},
    onReachBottom: function() {
        var e = this;
        if (-1 == e.data.min) return !1;
        a.util.request({
            url: "wmall/order/index",
            data: {
                min: e.data.min,
                menufooter: 1
            },
            success: function(t) {
                a.util.loaded();
                var n = e.data.orders.data.concat(t.data.message.message);
                e.data.orders.data = n, n.length || (e.data.orders.empty = 1);
                var o = t.data.message.min;
                o || (o = -1), (-1 == o || n.length < 20) && (e.data.orders.loaded = 1), e.setData({
                    orders: e.data.orders,
                    min: o,
                    config_mall: t.data.message.config_mall,
                    errander_status: t.data.message.errander_status,
                    showloading: !1
                });
            }
        });
    },
    onShareAppMessage: function() {}
}, "onPullDownRefresh", function() {
    var a = this;
    a.data.min = 0, a.data.orders = {
        loaded: 0,
        empty: 0,
        data: []
    }, a.onReachBottom(), wx.stopPullDownRefresh();
}));