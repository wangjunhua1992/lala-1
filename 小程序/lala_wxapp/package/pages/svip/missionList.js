var a = getApp();

Page({
    data: {
        status: 1,
        records: {
            page: 1,
            psize: 10,
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
    onReady: function() {},
    onShow: function() {},
    onHide: function() {},
    onUnload: function() {},
    onPullDownRefresh: function() {
        var a = this;
        a.data.records = {
            page: 1,
            psize: 10,
            empty: !1,
            loaded: !1,
            data: []
        }, a.onLoad(), wx.stopPullDownRefresh();
    },
    onReachBottom: function() {
        var t = this;
        a.util.request({
            url: "svip/task/takepartlist",
            data: {
                page: t.data.records.page,
                psize: t.data.records.psize,
                status: t.data.status
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
    onShareAppMessage: function() {},
    onToggleStatus: function(a) {
        var t = this;
        t.data.status = a.currentTarget.dataset.status, t.data.records = {
            page: 1,
            psize: 10,
            empty: !1,
            loaded: !1,
            data: []
        }, t.setData({
            status: t.data.status
        }), t.onReachBottom();
    },
    onJsEvent: function(t) {
        a.util.jsEvent(t);
    }
});