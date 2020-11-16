var a = getApp();

Page({
    data: {
        orders: {
            page: 1,
            psize: 15,
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
    onPullDownRefresh: function() {
        var a = this;
        a.data.orders = {
            page: 1,
            psize: 15,
            loaded: 0,
            empty: 0,
            data: []
        }, a.onReachBottom(), wx.stopPullDownRefresh();
    },
    onReachBottom: function() {
        var e = this;
        if (e.data.orders.loaded) return !1;
        a.util.request({
            url: "mealRedpacket/plus/mealorder",
            data: {
                page: e.data.orders.page,
                psize: e.data.orders.psize
            },
            success: function(t) {
                a.util.loaded();
                var d = t.data.message;
                if (d.errno) return a.util.toast(d.message), !1;
                e.data.orders.data = e.data.orders.data.concat(d.message), d.message.length < e.data.orders.psize && (e.data.orders.loaded = 1, 
                e.data.orders.data.length || (e.data.orders.empty = 1)), e.data.orders.page++, e.setData({
                    orders: e.data.orders
                });
            }
        });
    },
    onJsEvent: function(e) {
        a.util.jsEvent(e);
    }
});