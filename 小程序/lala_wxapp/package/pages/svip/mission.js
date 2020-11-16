var t = getApp();

Page({
    data: {
        records: {
            page: 1,
            psize: 10,
            empty: !1,
            loaded: !1,
            data: []
        },
        stat: {},
        submitting: !0,
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
        a.data.records.loaded || t.util.request({
            url: "svip/task/index",
            data: {
                page: a.data.records.page,
                psize: a.data.records.psize,
                menufooter: 1
            },
            success: function(e) {
                t.util.loaded();
                var s = e.data.message;
                if (s.errno) return t.util.toast(s.message), !1;
                s = s.message, a.data.records.data = a.data.records.data.concat(s.tasks), a.data.records.data.length || (a.data.records.empty = !0), 
                s.tasks && s.tasks.length < a.data.records.psize && (a.data.records.loaded = !0), 
                a.data.records.page++, a.data.submitting = !1, a.setData({
                    records: a.data.records,
                    stat: s.stat
                });
            }
        });
    },
    onTakepart: function(a) {
        var e = this, s = a.currentTarget.dataset;
        if (1 != s.link_type) {
            if (!e.data.submitting) {
                e.data.submitting = !0;
                var r = s.id;
                t.util.request({
                    url: "svip/task/takepart",
                    data: {
                        id: r
                    },
                    success: function(a) {
                        e.data.submitting = !1;
                        var s = a.data.message;
                        return t.util.toast(s.message), s.errno || e.onPullDownRefresh(), !1;
                    }
                });
            }
        } else t.util.jump2url(s.link, "navigateTo");
    },
    onPullDownRefresh: function() {
        var t = this;
        t.data.records = {
            page: 1,
            psize: 10,
            empty: !1,
            loaded: !1,
            data: []
        }, t.onReachBottom(), wx.stopPullDownRefresh();
    },
    onJsEvent: function(a) {
        t.util.jsEvent(a);
    }
});