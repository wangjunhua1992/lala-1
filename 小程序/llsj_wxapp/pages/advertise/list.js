var t = getApp();

Page({
    data: {
        Lang: t.Lang,
        status: 1
    },
    onLoad: function(a) {
        var n = this;
        a && a.status && (n.data.status = a.status), t.util.request({
            url: "manage/advertise/list/index",
            data: {
                status: n.data.status
            },
            success: function(a) {
                var s = a.data.message;
                if (s.errno) return t.util.toast(s.message), !1;
                n.setData(s.message);
            }
        });
    },
    onChangeStatus: function(t) {
        this.setData({
            status: t.target.dataset.status
        }), this.onLoad();
    },
    onJsEvent: function(a) {
        t.util.jsEvent(a);
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