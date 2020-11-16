var a = getApp();

Page({
    data: {
        status: -1,
        records: {
            page: 1,
            psize: 15,
            empty: !1,
            loaded: !1,
            data: []
        },
        Lang: a.Lang,
        wuiLoading: {
            show: !0,
            img: a.util.getStorageSync("theme.loading.img")
        }
    },
    onLoad: function() {
        this.onReachBottom();
    },
    onReachBottom: function() {
        var t = this;
        t.data.records.loaded || a.util.request({
            url: "gohome/order/list",
            data: {
                status: t.data.status,
                page: t.data.records.page,
                psize: t.data.records.psize,
                menufooter: 1
            },
            success: function(e) {
                a.util.loaded();
                var s = e.data.message;
                if (s.errno) return a.util.toast(s.message), !1;
                s = s.message, t.data.records.data = t.data.records.data.concat(s.records), t.data.records.data.length || (t.data.records.empty = !0), 
                s.records && s.records.length < t.data.records.psize && (t.data.records.loaded = !0), 
                t.data.records.page++, t.setData({
                    records: t.data.records
                });
            }
        });
    },
    onPullDownRefresh: function() {
        var a = this;
        a.data.records = {
            page: 1,
            psize: 15,
            empty: !1,
            loaded: !1,
            data: []
        }, a.onReachBottom(), wx.stopPullDownRefresh();
    },
    onChangeStatus: function(a) {
        var t = a.currentTarget.dataset.status;
        this.data.records = {
            page: 1,
            psize: 15,
            empty: !1,
            loaded: !1,
            data: []
        }, this.setData({
            status: t
        }), this.onReachBottom();
    },
    onJsEvent: function(t) {
        a.util.jsEvent(t);
    }
});