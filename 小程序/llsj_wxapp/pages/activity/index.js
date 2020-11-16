var t = getApp();

Page({
    data: {
        Lang: t.Lang
    },
    onLoad: function() {
        var e = this;
        t.util.request({
            url: "manage/activity/index",
            success: function(n) {
                var a = n.data.message;
                if (a.errno) return t.util.toast(a.message), !1;
                e.setData(a.message);
            }
        });
    },
    onJsEvent: function(e) {
        t.util.jsEvent(e);
    },
    onPullDownRefresh: function() {
        this.onLoad(), wx.stopPullDownRefresh();
    }
});