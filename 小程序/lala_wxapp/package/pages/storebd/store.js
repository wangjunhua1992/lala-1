var t = getApp();

Page({
    data: {
        stores: {
            page: 1,
            psize: 15,
            loaded: !1,
            empty: !1,
            data: []
        },
        Lang: t.Lang,
        wuiLoading: {
            show: !0,
            img: t.util.getStorageSync("theme.loading.img")
        }
    },
    onLoad: function() {
        this.onReachBottom();
    },
    onReachBottom: function() {
        var a = this;
        a.data.stores.loaded || t.util.request({
            url: "storebd/store",
            data: {
                page: a.data.stores.page,
                psize: a.data.stores.psize
            },
            success: function(e) {
                t.util.loaded();
                var s = e.data.message;
                if (s.errno) return t.util.toast(s.message), !1;
                s = s.message, a.data.stores.data = a.data.stores.data.concat(s.stores), a.data.stores.data.length || (a.data.stores.empty = !0), 
                s.stores && s.stores.length < a.data.stores.psize && (a.data.stores.loaded = !0), 
                a.data.stores.page++, a.setData({
                    stores: a.data.stores
                });
            }
        });
    },
    onPullDownRefresh: function() {
        var t = this;
        t.data.stores = {
            page: 1,
            psize: 15,
            loaded: !1,
            empty: !1,
            data: []
        }, t.onReachBottom(), wx.stopPullDownRefresh();
    }
});