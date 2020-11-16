var a = getApp();

Page({
    data: {
        down: [],
        showloading: !1,
        showNodata: !1,
        status: "spread1",
        spreadid: "spread1",
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
        var e = this, t = a.currentTarget.dataset.spreadid;
        e.setData({
            spreadid: t,
            status: t,
            click: 1
        }), e.onReachBottom();
    },
    onReachBottom: function() {
        var e = this;
        if (1 == e.data.click) e.setData({
            down: [],
            click: 0,
            min: 0
        }); else if (-1 == e.data.min) return !1;
        e.setData({
            showloading: !0
        });
        var t = {
            min: e.data.min,
            spreadid: e.data.spreadid,
            menufooter: 1
        };
        a.util.request({
            url: "spread/down/index",
            data: t,
            success: function(t) {
                a.util.loaded();
                var o = t.data.message.message, n = e.data.down.concat(o.members);
                if (!n.length) return e.setData({
                    showNodata: !0,
                    showloading: !1,
                    level1: o.level1,
                    level2: o.level2,
                    config: o.config
                }), !1;
                e.setData({
                    level1: o.level1,
                    level2: o.level2,
                    down: n,
                    min: t.data.message.min,
                    config: o.config
                }), t.data.message.min || (e.data.min = -1), e.setData({
                    showloading: !1,
                    showNodata: !1
                });
            }
        });
    }
});