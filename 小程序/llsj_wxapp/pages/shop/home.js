var e = getApp();

Page({
    data: {
        Lang: e.Lang
    },
    onLoad: function() {
        var t = this;
        e.util.request({
            url: "manage/shop/index/index",
            success: function(s) {
                var a = s.data.message;
                if (a.errno) return e.util.toast(a.message), !1;
                t.setData(a.message), wx.setNavigationBarTitle({
                    title: a.message.store.title
                });
            }
        });
    },
    onJsEvent: function(t) {
        e.util.jsEvent(t);
    },
    onPullDownRefresh: function() {
        this.onLoad(), wx.stopPullDownRefresh();
    }
});