var e = getApp();

Page({
    data: {
        uid: 0,
        Lang: e.Lang,
        wuiLoading: {
            show: !0,
            img: e.util.getStorageSync("theme.loading.img")
        }
    },
    onLoad: function(t) {
        var n = this;
        t && t.uid && (n.data.uid = t.uid), e.util.request({
            url: "shareRedpacket/share/success",
            data: {
                uid: n.data.uid
            },
            success: function(t) {
                e.util.loaded();
                var a = t.data.message;
                if (a.errno) return e.util.toast(a.message), !1;
                e.WxParse.wxParse("richtext", "html", a.message.redPacket.agreement, n, 5), n.setData(a.message);
            }
        });
    },
    onJsEvent: function(t) {
        e.util.jsEvent(t);
    },
    onReady: function() {},
    onShow: function() {},
    onHide: function() {},
    onUnload: function() {},
    onPullDownRefresh: function() {},
    onReachBottom: function() {},
    onShareAppMessage: function() {}
});