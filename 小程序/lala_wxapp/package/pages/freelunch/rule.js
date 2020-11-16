var n = getApp();

Page({
    data: {
        wuiLoading: {
            show: !0
        }
    },
    onLoad: function(e) {
        var o = this;
        n.util.request({
            url: "freeLunch/freeLunch/rule",
            data: {},
            success: function(e) {
                n.util.loaded();
                var a = e.data.message.message.agreement;
                n.WxParse.wxParse("agreement", "html", a, o, 0);
            }
        });
    },
    onReady: function() {},
    onShow: function() {},
    onHide: function() {},
    onUnload: function() {},
    onPullDownRefresh: function() {},
    onReachBottom: function() {},
    onShareAppMessage: function() {}
});