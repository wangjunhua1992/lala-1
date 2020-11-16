var e = getApp();

Page({
    data: {
        Lang: e.Lang
    },
    onLoad: function() {
        var t = this;
        e.util.request({
            url: "manage/statcenter/index",
            success: function(a) {
                var s = a.data.message;
                if (s.errno) return e.util.toast(s.message), !1;
                t.setData(s.message);
            }
        });
    },
    onPullDownRefresh: function() {
        this.onLoad(), wx.stopPullDownRefresh();
    }
});