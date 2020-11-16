var t = getApp();

Page({
    data: {
        Lang: t.Lang,
        status: 0,
        psize: 10,
        showLoading: !1,
        records: {
            status_0: {
                loaded: 0,
                empty: 0,
                page: 1,
                status: 0,
                list: []
            },
            status_1: {
                loaded: 0,
                empty: 0,
                page: 1,
                status: 1,
                list: []
            },
            status_2: {
                loaded: 0,
                empty: 0,
                page: 1,
                status: 2,
                list: []
            },
            status_3: {
                loaded: 0,
                empty: 0,
                page: 1,
                status: 3,
                list: []
            }
        }
    },
    onLoad: function(t) {
        this.onGetRecords();
    },
    onJsEvent: function(a) {
        t.util.jsEvent(a);
    },
    onChange: function(t) {
        var a = this, s = t.currentTarget.dataset.index;
        a.setData({
            status: s
        }), a.onGetRecords();
    },
    onGetRecords: function() {
        var a = this, s = "status_" + a.data.status;
        if (1 == a.data.records[s].loaded) return !1;
        a.setData({
            showLoading: !0
        }), t.util.request({
            url: "delivery/finance/getcash/list",
            data: {
                psize: a.data.psize,
                page: a.data.records[s].page,
                status: a.data.records[s].status
            },
            success: function(e) {
                var o = e.data.message;
                o.errno && t.util.toast(o.message);
                var d = a.data.records[s].list.concat(o.message.records);
                a.data.records[s].list = d, d.length || (a.data.records[s].empty = 1), o.message.records.length < a.data.psize && (a.data.records[s].loaded = 1), 
                a.data.records[s].page++, a.setData({
                    records: a.data.records,
                    showLoading: !1
                });
            }
        });
    },
    onReachBottom: function() {
        this.onGetRecords();
    },
    onPullDownRefresh: function() {
        var t = this, a = t.data.status, s = "status_" + a;
        t.data.records[s] = {
            loaded: 0,
            empty: 0,
            page: 1,
            status: a,
            list: []
        }, t.onGetRecords(), wx.stopPullDownRefresh();
    }
});