var a = getApp();

Page({
    data: {
        records: [],
        min: 0,
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
        var n = this;
        if (-1 == n.data.min) return !1;
        console.log("++++++++++"), a.util.request({
            url: "ordergrant/record",
            data: {
                min: n.data.min
            },
            success: function(o) {
                a.util.loaded();
                var t = o.data.message.min;
                console.log(o.data.message.message);
                var e = n.data.records.concat(o.data.message.message);
                if (!e.length) return n.setData({
                    showNodata: !0
                }), !1;
                n.setData({
                    records: e,
                    min: t
                }), o.data.message.min || (n.data.min = -1);
            }
        });
    },
    onPullDownRefresh: function() {},
    onReady: function() {},
    onShow: function() {},
    onHide: function() {},
    onUnload: function() {},
    onShareAppMessage: function() {}
});