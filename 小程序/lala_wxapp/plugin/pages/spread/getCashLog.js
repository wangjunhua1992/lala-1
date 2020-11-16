var a = getApp();

Page({
    data: {
        staus: 0,
        num: -1,
        records: [],
        showloading: !1,
        showNodata: !1,
        Lang: a.Lang,
        wuiLoading: {
            show: !0,
            img: a.util.getStorageSync("theme.loading.img")
        }
    },
    onLoad: function(a) {
        this.onReachBottom();
    },
    onSubmit: function(a) {
        var t = this, s = a.currentTarget.dataset.status;
        t.setData({
            status: s,
            num: s,
            frquency: 1
        }), t.onReachBottom();
    },
    onReachBottom: function() {
        var t = this;
        if (1 == t.data.frquency) t.setData({
            frquency: 0,
            records: [],
            min: 0
        }); else if (-1 == t.data.min) return !1;
        this.setData({
            showloading: !0
        });
        var s = {
            min: t.data.min,
            status: t.data.status,
            menufooter: 1
        };
        a.util.request({
            url: "spread/getCash/index",
            data: s,
            success: function(s) {
                a.util.loaded();
                var e = t.data.records.concat(s.data.message.message);
                if (!e.length) return t.setData({
                    showNodata: !0,
                    showloading: !1
                }), !1;
                t.setData({
                    records: e,
                    min: s.data.message.min
                }), s.data.message.min || (t.data.min = -1), t.setData({
                    showloading: !1,
                    showNodata: !1
                });
            }
        });
    }
});