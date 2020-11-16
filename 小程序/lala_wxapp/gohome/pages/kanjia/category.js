var a = getApp();

Page({
    data: {
        cateid: 0,
        records: {
            page: 1,
            psize: 15,
            empty: !1,
            loaded: !1,
            data: []
        },
        navs: [],
        Lang: a.Lang,
        wuiLoading: {
            show: !0,
            img: a.util.getStorageSync("theme.loading.img")
        }
    },
    onLoad: function(a) {
        var e = this;
        a && a.cateid && (e.data.cateid = a.cateid), e.onReachBottom();
    },
    onReachBottom: function() {
        var e = this;
        e.data.records.loaded || a.util.request({
            url: "kanjia/activity/index",
            data: {
                page: e.data.records.page,
                psize: e.data.records.psize,
                cateid: e.data.cateid,
                menufooter: 1
            },
            success: function(t) {
                a.util.loaded();
                var d = t.data.message;
                if (d.errno) return a.util.toast(d.message), !1;
                d = d.message, e.data.records.data = e.data.records.data.concat(d.records), e.data.records.data.length || (e.data.records.empty = !0), 
                d.records && d.records.length < e.data.records.psize && (e.data.records.loaded = !0), 
                e.data.records.page++, e.setData({
                    records: e.data.records
                }), wx.setNavigationBarTitle({
                    title: d.category.title
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