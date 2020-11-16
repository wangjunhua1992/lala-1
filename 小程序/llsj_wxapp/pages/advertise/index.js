var n = getApp();

Page({
    data: {
        Lang: n.Lang
    },
    onLoad: function(t) {
        var e = this;
        n.util.request({
            url: "manage/advertise/index",
            success: function(t) {
                var o = t.data.message;
                if (o.errno) return n.util.toast(o.message), !1;
                e.setData(o.message);
            }
        });
    },
    onJsEvent: function(t) {
        n.util.jsEvent(t);
    },
    onReady: function() {},
    onShow: function() {},
    onHide: function() {},
    onUnload: function() {},
    onPullDownRefresh: function() {
        this.onLoad(), wx.stopPullDownRefresh();
    },
    onReachBottom: function() {},
    onShareAppMessage: function() {}
});