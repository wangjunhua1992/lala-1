var n = getApp();

Page({
    data: {
        Lang: n.Lang
    },
    onLoad: function() {
        var o = this;
        n.util.request({
            url: "manage/goods/category/list",
            success: function(t) {
                var e = t.data.message;
                if (e.errno) return n.util.toast(e.message), !1;
                o.setData(e.message), console.log(o.data.categorys);
            }
        });
    },
    onJsEvent: function(o) {
        n.util.jsEvent(o);
    },
    onReady: function() {},
    onShow: function() {},
    onHide: function() {},
    onUnload: function() {},
    onPullDownRefresh: function() {},
    onReachBottom: function() {},
    onShareAppMessage: function() {}
});