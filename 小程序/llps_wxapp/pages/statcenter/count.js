var t = getApp();

Page({
    data: {
        Lang: t.Lang,
        type: "today"
    },
    onLoad: function(a) {
        var e = this;
        t.util.request({
            url: "delivery/statcenter/stat/index",
            data: {
                type: e.data.type
            },
            success: function(a) {
                var n = a.data.message;
                if (n.errno) return t.util.toast(n.message), !1;
                e.setData(n.message);
            }
        });
    },
    onChange: function(t) {
        var a = this, e = t.currentTarget.dataset.type;
        a.setData({
            type: e
        }), a.onLoad();
    },
    onPullDownRefresh: function() {},
    onReachBottom: function() {}
});