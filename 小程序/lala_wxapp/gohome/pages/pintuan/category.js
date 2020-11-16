var a = getApp();

Page({
    data: {
        records: {
            page: 1,
            psize: 15,
            empty: !1,
            loaded: !1,
            data: []
        },
        goods: [],
        Lang: a.Lang,
        wuiLoading: {
            show: !1
        }
    },
    onLoad: function(a) {
        this.data.cateid = a.cid, this.onReachBottom();
    },
    onReachBottom: function() {
        var e = this;
        e.data.records.loaded || a.util.request({
            url: "pintuan/index/index",
            data: {
                cateid: e.data.cateid,
                page: e.data.records.page,
                psize: e.data.records.psize,
                menufooter: 1
            },
            success: function(t) {
                a.util.loaded();
                var d = t.data.message;
                if (d.errno) return a.util.toast(d.message), !1;
                d = d.message, e.data.records.data = e.data.records.data.concat(d.goods), d.goods.length < e.data.records.psize && (e.data.records.loaded = !0, 
                e.data.records.data.length || (e.data.records.empty = !0)), e.data.records.page++, 
                e.setData({
                    records: e.data.records
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
    onJsEvent: function(e) {
        a.util.jsEvent(e);
    }
});