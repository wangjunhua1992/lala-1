var a = getApp();

Page({
    data: {
        records: {
            page: 1,
            psize: 10,
            empty: !1,
            loaded: !1,
            data: []
        },
        categorys: [],
        Lang: a.Lang,
        wuiLoading: {
            show: !0,
            img: a.util.getStorageSync("theme.loading.img")
        }
    },
    onLoad: function(a) {
        this.data.options = a, this.onReachBottom();
    },
    onReachBottom: function() {
        var t = this;
        t.data.records.loaded || a.util.request({
            url: "tongcheng/index/category",
            data: {
                id: t.data.options.id,
                childid: t.data.options.childid,
                page: t.data.records.page,
                psize: t.data.records.psize,
                menufooter: 1
            },
            success: function(o) {
                a.util.loaded();
                var e = o.data.message;
                if (e.errno) return a.util.toast(e.message), !1;
                e = e.message, t.data.records.data = t.data.records.data.concat(e.informations), 
                e.informations.length < t.data.records.psize && (t.data.records.loaded = !0, t.data.records.data.length || (t.data.records.empty = !0)), 
                t.data.records.page++;
                var d = "/gohome/pages/tongcheng/category?id=" + t.data.options.id;
                t.data.options.childid && (d = d + "&childid=" + t.data.options.childid), t.setData({
                    options: t.data.options,
                    records: t.data.records,
                    categorys: e.categorys,
                    sharedata: {
                        title: e.categorys[t.data.options.id].title,
                        desc: e.categorys[t.data.options.id].content,
                        imageUrl: e.categorys[t.data.options.id].thumb,
                        path: d
                    }
                });
            }
        });
    },
    onPullDownRefresh: function() {
        var a = this;
        a.data.records = {
            page: 1,
            psize: 10,
            empty: !1,
            loaded: !1,
            data: []
        }, a.onReachBottom(), wx.stopPullDownRefresh();
    },
    onJsEvent: function(t) {
        a.util.jsEvent(t);
    },
    onShareAppMessage: function(a) {
        return this.data.sharedata;
    }
});