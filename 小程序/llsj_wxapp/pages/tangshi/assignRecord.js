var a = getApp();

Page({
    data: {
        Lang: a.Lang,
        showLoading: !1,
        records: {
            page: 1,
            psize: 15,
            empty: !1,
            loaded: !1,
            data: []
        }
    },
    onLoad: function(t) {
        var e = this;
        if (!t.id) return a.util.toast("参数错误"), !1;
        e.data.options = t, e.onReachBottom();
    },
    onPullDownRefresh: function() {
        var a = this;
        a.setData({
            records: {
                page: 1,
                psize: 15,
                empty: !1,
                loaded: !1,
                data: []
            }
        }), a.onReachBottom(), wx.stopPullDownRefresh();
    },
    onReachBottom: function() {
        var t = this;
        if (t.data.records.loaded) return !1;
        t.setData({
            showLoading: !0
        }), a.util.request({
            url: "manage/tangshi/assign/record",
            data: {
                id: t.data.options.id,
                page: t.data.records.page,
                psize: t.data.records.psize
            },
            success: function(e) {
                var o = e.data.message;
                if (o.errno) a.util.toast(o.message); else {
                    o = o.message;
                    var s = t.data.records.data.concat(o.records);
                    t.data.records.data = s, t.data.records.page++, s.length || (t.data.records.empty = !0), 
                    o.records.length < t.data.records.psize && (t.data.records.loaded = !0), t.setData({
                        records: t.data.records,
                        showLoading: !1,
                        title: o.queue_title,
                        id: t.data.options.id
                    });
                }
            }
        });
    },
    onJsEvent: function(t) {
        a.util.jsEvent(t);
    }
});