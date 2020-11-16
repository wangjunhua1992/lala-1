var a = getApp();

Page({
    data: {
        list: [],
        showLoading: !1,
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
    onReachBottom: function() {
        var t = this;
        if (-1 == t.data.min) return !1;
        this.setData({
            showloading: !0
        }), a.util.request({
            url: "spread/rank",
            data: {
                min: t.data.min,
                menufooter: 1
            },
            success: function(e) {
                a.util.loaded(), "-1" == e.data.message.errno && a.util.toast(e.data.message.message, "/plugin/pages/spread/index", 1e3);
                var n = e.data.message.message;
                t.setData({
                    rank: n.rank,
                    member: n.member,
                    count: n.count,
                    final_fee: n.final_fee,
                    count_final_fee: n.count_final_fee
                });
                var s = t.data.list.concat(n.list);
                if (!s.length) return t.setData({
                    showLoading: !1,
                    showNodata: !0
                }), !1;
                t.setData({
                    list: s,
                    min: e.data.message.min
                }), e.data.message.min || (t.data.min = -1), t.setData({
                    showLoading: !1
                });
            }
        });
    }
});