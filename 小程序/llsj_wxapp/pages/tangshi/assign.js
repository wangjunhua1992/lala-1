var a = getApp();

Page({
    data: {
        Lang: a.Lang
    },
    onLoad: function() {
        var s = this;
        a.util.request({
            url: "manage/tangshi/assign/index",
            data: {},
            success: function(t) {
                var n = t.data.message;
                n.errno ? a.util.toast(n.message) : s.setData(n.message);
            }
        });
    },
    onJsEvent: function(s) {
        a.util.jsEvent(s);
    },
    onPullDownRefresh: function() {
        this.onLoad(), wx.stopPullDownRefresh();
    }
});