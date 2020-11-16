var a = getApp();

Page({
    data: {
        partakers: {
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
        var t = this;
        t.data.record_id = a.record_id, t.onGetDetail();
    },
    onGetDetail: function() {
        var t = this;
        a.util.request({
            url: "freeLunch/freeLunch/detail",
            data: {
                record_id: t.data.record_id
            },
            success: function(e) {
                a.util.loaded();
                var n = e.data.message;
                if (n.errno) return a.util.toast(n.message), !1;
                t.setData(n.message), t.onReachBottom();
            }
        });
    },
    onRefresh: function() {
        this.onPullDownRefresh();
    },
    onJsEvent: function(t) {
        a.util.jsEvent(t);
    },
    onReady: function() {},
    onShow: function() {},
    onHide: function() {},
    onUnload: function() {},
    onPullDownRefresh: function() {
        var a = this;
        a.data.min = 0, a.data.partakers = {
            loaded: 0,
            empty: 0,
            data: []
        }, a.onGetDetail(), wx.stopPullDownRefresh();
    },
    onReachBottom: function() {
        var t = this;
        if (-1 == t.data.min) return !1;
        a.util.request({
            url: "freeLunch/freeLunch/partakers",
            data: {
                min: t.data.min,
                record_id: t.data.record.id
            },
            success: function(a) {
                var e = t.data.partakers.data.concat(a.data.message.message);
                t.data.partakers.data = e, e.length || (t.data.partakers.empty = 1);
                var n = a.data.message.min;
                n || (n = -1), (-1 == n || a.data.message.message.length < 10) && (t.data.partakers.loaded = 1), 
                t.setData({
                    partakers: t.data.partakers,
                    min: n
                });
            }
        });
    },
    onShareAppMessage: function() {}
});