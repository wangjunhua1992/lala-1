var t = getApp();

Page({
    data: {
        stores: [],
        showNodata: !1,
        Lang: t.Lang,
        wuiLoading: {
            show: !0,
            img: t.util.getStorageSync("theme.loading.img")
        }
    },
    onToggleDiscount: function(t) {
        var a = t.currentTarget.dataset, o = this.data.stores;
        console.log(a), o[a.index].activity.is_show_all = !o[a.index].activity.is_show_all, 
        this.setData({
            stores: o
        });
    },
    onLoad: function(t) {
        this.onReachBottom();
    },
    onReady: function() {},
    onShow: function() {},
    onReachBottom: function() {
        var a = this;
        if (-1 == a.data.min) return !1;
        this.setData({
            showloading: !0
        }), t.util.request({
            url: "wmall/member/favorite",
            data: {
                min: a.data.min
            },
            success: function(o) {
                t.util.loaded();
                var s = a.data.stores.concat(o.data.message.message);
                if (!s.length) return a.setData({
                    showNodata: !0,
                    showloading: !1
                }), !1;
                a.setData({
                    stores: s,
                    min: o.data.message.min
                }), o.data.message.min || (a.data.min = -1), a.setData({
                    showloading: !1
                });
            }
        });
    },
    onShareAppMessage: function() {},
    onPullDownRefresh: function() {
        var t = this;
        t.data.min = 0, t.data.stores = [], t.onReachBottom(), wx.stopPullDownRefresh();
    }
});