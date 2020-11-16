var n = getApp();

Page({
    data: {
        Lang: n.Lang,
        wuiLoading: {
            show: !0,
            img: n.util.getStorageSync("theme.loading.img")
        }
    },
    onLoad: function(t) {
        var s = this;
        s.data.sid = t.sid, n.util.request({
            url: "wmall/store/assign/mine",
            data: {
                sid: t.sid
            },
            success: function(t) {
                n.util.loaded();
                var a = t.data.message;
                0 == a.errno ? s.setData(a.message) : -1e3 == a.errno && n.util.toast(a.message, "redirect:/tangshi/pages/assign/assign?sid=" + s.data.sid, 1e3);
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
        this.onLoad({
            sid: this.data.store.id
        }), wx.stopPullDownRefresh();
    },
    onReachBottom: function() {},
    onShareAppMessage: function() {}
});