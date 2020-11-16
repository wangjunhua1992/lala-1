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
        showloading: !1,
        status: 0,
        order_type: "takeout",
        Lang: a.Lang,
        wuiLoading: {
            show: !0,
            img: a.util.getStorageSync("theme.loading.img")
        }
    },
    onLoad: function() {
        this.onReachBottom();
    },
    onToggleStatus: function(a) {
        var t = a.currentTarget.dataset.status;
        t != this.data.status && (this.data.refresh = 1, this.setData({
            status: t
        }), this.onReachBottom());
    },
    onToggleOrdertype: function(a) {
        var t = a.currentTarget.dataset.order_type;
        t != this.data.order_type && (this.data.refresh = 1, this.setData({
            order_type: t
        }), this.onReachBottom());
    },
    onReachBottom: function() {
        var t = this;
        1 == t.data.refresh && (t.data.records = {
            page: 1,
            psize: 15,
            empty: !1,
            loaded: !1,
            data: []
        }), t.data.records.loaded || t.data.showloading || (t.setData({
            showloading: !0
        }), a.util.request({
            url: "spread/order/index",
            data: {
                order_type: t.data.order_type,
                status: t.data.status,
                page: t.data.records.page,
                psize: t.data.records.psize,
                menufooter: 1
            },
            success: function(e) {
                a.util.loaded(), t.data.showloading = !1;
                var r = e.data.message;
                if (r.errno) return a.util.toast(r.message), !1;
                r = r.message, t.data.records.data = t.data.records.data.concat(r.records), r.records.length < t.data.records.psize && (t.data.records.loaded = !0, 
                t.data.records.data.length || (t.data.records.empty = !0)), t.data.refresh = 0, 
                t.data.records.page++, t.setData({
                    config: r.config,
                    records: t.data.records,
                    showloading: !1
                });
            }
        }));
    },
    onJsEvent: function(t) {
        a.util.jsEvent(t);
    }
});