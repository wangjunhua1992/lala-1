var t = getApp();

Page({
    data: {
        markers: [],
        Lang: t.Lang,
        wuiLoading: {
            show: !0,
            img: t.util.getStorageSync("theme.loading.img")
        }
    },
    onLoad: function(a) {
        var e = this;
        e.data.id = a.id, t.util.request({
            url: "wmall/order/index/location",
            data: {
                id: e.data.id
            },
            success: function(a) {
                t.util.loaded();
                var n = a.data.message;
                return n.errno ? (t.util.toast(n.message), !1) : 5 == n.message.order.status ? (t.util.toast("订单已完成!"), 
                !1) : (e.setData(n.message), void setInterval(function() {
                    e.onRefresh();
                }, 3e4));
            }
        });
    },
    onRefresh: function() {
        var a = this;
        t.util.request({
            url: "system/common/deliveryer/location",
            data: {
                id: a.data.order.deliveryer_id
            },
            success: function(e) {
                var n = e.data.message;
                if (n.errno) return t.util.toast(n.message), !1;
                var o = {
                    latitude: n.message.location_x,
                    longitude: n.message.location_y
                };
                a.data.points[1] = o, a.data.markers[1].latitude = o.latitude, a.data.markers[1].longitude = o.longitude, 
                a.setData({
                    markers: a.data.markers,
                    points: a.data.points
                });
            }
        });
    },
    onQuestion: function() {
        wx.showModal({
            content: "要获取最新位置，请点击刷新按钮；如果配送员远离您，那可能是正在为更早下单的用户配送，请耐心等待~",
            showCancel: !1
        });
    },
    onReady: function() {},
    onShow: function() {},
    onHide: function() {},
    onUnload: function() {},
    onPullDownRefresh: function() {},
    onReachBottom: function() {},
    onShareAppMessage: function() {}
});