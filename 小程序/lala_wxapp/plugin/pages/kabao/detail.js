var n = getApp();

Page({
    data: {
        showFixedBtn: !1
    },
    onLoad: function(n) {},
    onShow: function() {},
    onPullDownRefresh: function() {},
    onReachBottom: function() {},
    onPageScroll: function(n) {
        var t = this;
        n.scrollTop > 210 ? t.data.showFixedBtn || t.setData({
            showFixedBtn: !0
        }) : t.data.showFixedBtn && t.setData({
            showFixedBtn: !1
        });
    },
    onJsEvent: function(t) {
        n.util.jsEvent(t);
    },
    onShareAppMessage: function() {}
});