var t = getApp();

Page({
    data: {
        Lang: t.Lang,
        notice: {
            page: 1,
            psize: 10,
            loaded: !1,
            empty: !1,
            data: []
        }
    },
    onLoad: function(t) {
        this.onPullDownRefresh();
    },
    onPullDownRefresh: function() {
        var t = this;
        t.setData({
            notice: {
                page: 1,
                psize: 10,
                loaded: !1,
                empty: !1,
                data: []
            }
        }), t.onReachBottom(), wx.stopPullDownRefresh();
    },
    onReachBottom: function(a) {
        var e = this;
        if (e.data.notice.loaded) return !1;
        t.util.request({
            url: "manage/news/notice/list",
            data: {
                page: e.data.notice.page,
                psize: e.data.notice.psize
            },
            success: function(a) {
                var n = a.data.message;
                if (n.errno) return t.util.toast(n.message), !1;
                n = n.message;
                var o = e.data.notice.data.concat(n.notice);
                e.data.notice.data = o, e.data.notice.page++, o.length || (e.data.notice.empty = !0), 
                n.notice.length < e.data.notice.psize && (e.data.notice.loaded = !0), e.setData({
                    notice: e.data.notice
                });
            }
        });
    },
    onJsEvent: function(a) {
        t.util.jsEvent(a);
    }
});