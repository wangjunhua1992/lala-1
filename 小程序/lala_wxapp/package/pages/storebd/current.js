var t = getApp();

Page({
    data: {
        type: 0,
        current: {
            page: 1,
            psize: 15,
            empty: !1,
            loaded: !1,
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
        a.data.current.loaded || t.util.request({
            url: "storebd/current",
            data: {
                page: a.data.current.page,
                psize: a.data.current.psize,
                trade_type: a.data.type
            },
            success: function(e) {
                t.util.loaded();
                var r = e.data.message;
                if (r.errno) return t.util.toast(r.message), !1;
                r = r.message, a.data.current.data = a.data.current.data.concat(r.current), a.data.current.data.length || (a.data.current.empty = !0), 
                r.current && r.current.length < a.data.current.psize && (a.data.current.loaded = !0), 
                a.data.current.page++, a.setData({
                    current: a.data.current
                });
            }
        });
    },
    onChangeType: function(t) {
        var a = t.currentTarget.dataset.type;
        this.data.current = {
            page: 1,
            psize: 15,
            empty: !1,
            loaded: !1,
            data: []
        }, this.setData({
            type: a
        }), this.onReachBottom();
    },
    onPullDownRefresh: function() {
        var t = this;
        t.data.current = {
            page: 1,
            psize: 15,
            empty: !1,
            loaded: !1,
            data: []
        }, t.onReachBottom(), wx.stopPullDownRefresh();
    }
});