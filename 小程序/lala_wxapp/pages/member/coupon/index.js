var a = getApp();

Page({
    data: {
        coupons: [],
        showloading: !1,
        showNodata: !1,
        status: 1,
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
    onStatus: function() {
        var a = this;
        a.setData({
            coupons: [],
            showloading: !1,
            showNodata: !1,
            status: 2
        }), a.onReachBottom();
    },
    onReachBottom: function() {
        var t = this;
        if (-1 == t.data.min) return !1;
        this.setData({
            showloading: !0
        }), a.util.request({
            url: "wmall/member/coupon",
            data: {
                min: t.data.min,
                status: t.data.status
            },
            success: function(o) {
                a.util.loaded();
                var s = t.data.coupons.concat(o.data.message.message);
                if (!s.length) return t.setData({
                    showNodata: !0,
                    showloading: !1
                }), !1;
                t.setData({
                    coupons: s,
                    min: o.data.message.min
                }), o.data.message.min || (t.data.min = -1), t.setData({
                    showloading: !1,
                    showNodata: !1,
                    status: t.data.status
                });
            }
        });
    },
    onShareAppMessage: function() {},
    onPullDownRefresh: function() {
        var a = this;
        a.data.min = 0, a.data.coupons = [], a.onReachBottom(), wx.stopPullDownRefresh();
    }
});