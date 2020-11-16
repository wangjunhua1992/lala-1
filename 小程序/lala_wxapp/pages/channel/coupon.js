var a = getApp();

Page({
    data: {
        coupons: [],
        showloading: !1,
        showNodata: !1,
        shareData: {
            title: "领券中心",
            path: "/pages/channel/coupon",
            success: function() {},
            fail: function() {}
        },
        Lang: a.Lang,
        wuiLoading: {
            show: !1
        }
    },
    onLoad: function(a) {
        this.onReachBottom();
    },
    onReachBottom: function() {
        var t = this;
        if (-1 == t.data.min) return !1;
        this.setData({
            showloading: !0
        }), a.util.request({
            url: "wmall/channel/coupon/list",
            data: {
                min: t.data.min
            },
            success: function(n) {
                a.util.loaded();
                var o = t.data.coupons.concat(n.data.message.message);
                if (!o.length) return t.setData({
                    showNodata: !0,
                    showloading: !1
                }), !1;
                t.setData({
                    coupons: o,
                    min: n.data.message.min
                }), n.data.message.min || (t.data.min = -1), t.setData({
                    showloading: !1
                });
            }
        });
    },
    getCoupon: function(t) {
        var n = this, o = n.data.coupons, s = t.currentTarget.dataset.sid, e = t.currentTarget.dataset.id;
        a.util.request({
            url: "wmall/channel/coupon/get",
            data: {
                sid: s
            },
            success: function(a) {
                wx.showToast({
                    title: "领取成功",
                    icon: "success"
                }), o[e].get = !o[e].get, n.setData({
                    coupons: o
                });
            }
        });
    },
    onShareAppMessage: function() {
        return this.data.shareData;
    },
    onPullDownRefresh: function() {
        var a = this;
        a.data.min = 0, a.data.coupons = [], a.onReachBottom(), wx.stopPullDownRefresh();
    }
});