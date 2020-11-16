var a = getApp();

Page({
    data: {
        orders: {
            page: 1,
            psize: 4,
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
    onLoad: function(a) {
        this.onReachBottom();
    },
    onGetOrders: function() {
        var e = this;
        a.util.request({
            url: "creditshop/order/list",
            data: {
                page: e.data.orders.page,
                psize: e.data.orders.psize
            },
            success: function(t) {
                a.util.loaded();
                var d = t.data.message.message;
                e.data.orders.data = e.data.orders.data.concat(d), d.length < e.data.orders.psize && (e.data.orders.loaded = 1, 
                e.data.orders.data.length || (e.data.orders.empty = 1)), e.data.orders.page++, e.setData({
                    orders: e.data.orders
                });
            }
        });
    },
    onReady: function() {},
    onShow: function() {},
    onHide: function() {},
    onUnload: function() {},
    onPullDownRefresh: function() {
        var e = this;
        e.data.orders.page = 1, e.data.orders.psize = 4, e.data.orders.loaded = 0, e.data.orders.empty = 0, 
        e.data.orders.data = [], wx.showNavigationBarLoading(), a.util.request({
            url: "creditshop/order/list",
            data: {
                page: e.data.orders.page,
                psize: e.data.orders.psize
            },
            success: function(a) {
                var t = a.data.message.message;
                console.log(t), e.data.orders.data = e.data.orders.data.concat(t), t.length < e.data.orders.psize && (e.data.orders.loaded = 1, 
                e.data.orders.data.length || (e.data.orders.empty = 1)), e.data.orders.page++, e.setData({
                    orders: e.data.orders
                }), console.log(e.data);
            },
            complete: function() {
                wx.hideNavigationBarLoading(), wx.stopPullDownRefresh();
            }
        });
    },
    onCancel: function(e) {
        var t = this, d = e.currentTarget.dataset.id;
        a.util.request({
            url: "creditshop/order/cancel",
            data: {
                order_id: d
            },
            success: function(e) {
                console.log(e);
                var d = e.data.message;
                a.util.toast(d.message), t.onPullDownRefresh();
            }
        });
    },
    onReachBottom: function() {
        this.onGetOrders();
    },
    onShareAppMessage: function() {}
});