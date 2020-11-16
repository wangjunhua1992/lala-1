var e = getApp();

Page({
    data: {
        Lang: e.Lang
    },
    onLoad: function(n) {
        var a = this, t = {
            key: n.key
        };
        n.pageid && (t.pageid = n.pageid), n.helpid && (t.helpid = n.helpid), (n.key = "notice") && (t.noticeid = n.noticeid), 
        e.util.request({
            url: "wmall/common/agreement",
            data: t,
            success: function(n) {
                var t = n.data.message.message.agreement;
                e.WxParse.wxParse("agreement", "html", t, a, 5), wx.setNavigationBarTitle({
                    title: n.data.message.message.title
                });
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