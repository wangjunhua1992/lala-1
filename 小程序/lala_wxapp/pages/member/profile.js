var n = getApp();

Page({
    data: {
        user: [],
        Lang: n.Lang,
        wuiLoading: {
            show: !0,
            img: n.util.getStorageSync("theme.loading.img")
        }
    },
    onLoad: function(e) {
        var o = this;
        n.util.request({
            url: "wmall/member/profile",
            success: function(e) {
                n.util.loaded();
                var t = e.data.message.message, i = n.util.getExtConfigSync();
                i.siteInfo.version || (i.siteInfo.version = "8.0"), o.setData({
                    user: t,
                    ext: i
                });
            }
        });
    },
    onJsEvent: function(e) {
        n.util.jsEvent(e);
    },
    onReady: function() {},
    onShow: function() {},
    onHide: function() {},
    onUnload: function() {},
    onPullDownRefresh: function() {},
    onReachBottom: function() {},
    onShareAppMessage: function() {}
});