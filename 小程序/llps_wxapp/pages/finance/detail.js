var n = getApp();

Page({
    data: {
        Lang: n.Lang
    },
    onLoad: function(e) {
        var t = this, a = e.id;
        n.util.request({
            url: "delivery/finance/current/detail",
            data: {
                id: a
            },
            success: function(e) {
                var a = e.data.message;
                if (a.errno) return n.util.toast(a.message), !1;
                t.setData(a.message);
            }
        });
    },
    onReady: function() {},
    onShow: function() {},
    onHide: function() {},
    onUnload: function() {},
    onPullDownRefresh: function() {},
    onReachBottom: function() {},
    onShareAppMessage: function() {}
});