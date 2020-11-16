var a = getApp();

Page({
    data: {
        Lang: a.Lang,
        trade_type: 0,
        showLoading: !1,
        records: {
            page: 1,
            psize: 15,
            empty: !1,
            loaded: !1,
            data: []
        }
    },
    onLoad: function(a) {
        this.onReachBottom();
    },
    onChangeType: function(a) {
        var e = this, t = a.currentTarget.dataset.type;
        if (t == e.data.trade_type) return !1;
        e.setData({
            trade_type: t,
            records: {
                page: 1,
                psize: 15,
                empty: !1,
                loaded: !1,
                data: []
            }
        }), e.onReachBottom();
    },
    onPullDownRefresh: function() {
        var a = this;
        a.setData({
            trade_type: 0,
            records: {
                page: 1,
                psize: 15,
                empty: !1,
                loaded: !1,
                data: []
            }
        }), a.onLoad(), wx.stopPullDownRefresh();
    },
    onReachBottom: function() {
        var e = this;
        if (e.data.records.loaded) return !1;
        e.setData({
            showLoading: !0
        }), a.util.request({
            url: "manage/finance/yucunjin",
            data: {
                trade_type: e.data.trade_type,
                page: e.data.records.page,
                psize: e.data.records.psize
            },
            success: function(t) {
                var d = t.data.message;
                if (d.errno) a.util.toast(d.message); else {
                    d = d.message;
                    var r = e.data.records.data.concat(d.records);
                    e.data.records.data = r, e.data.records.page++, r.length || (e.data.records.empty = !0), 
                    d.records.length < e.data.records.psize && (e.data.records.loaded = !0), e.setData({
                        records: e.data.records,
                        showLoading: !1
                    });
                }
            }
        });
    },
    onJsEvent: function(e) {
        a.util.jsEvent(e);
    }
});