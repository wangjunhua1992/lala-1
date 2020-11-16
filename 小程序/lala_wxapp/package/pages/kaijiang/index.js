var n = getApp();

Page({
    data: {
        showPreLoading: !0,
        benqi: {},
        shangqi: {},
        xiaqi: {},
        Lang: n.Lang,
        wuiLoading: {
            show: !0,
            img: n.util.getStorageSync("theme.loading.img")
        }
    },
    onLoad: function(i) {
        var e = this;
        n.util.request({
            url: "kaijiang/index/index",
            success: function(i) {
                n.util.loaded();
                var a = i.data.message;
                if (a.errno) return n.util.toast(a.message), !1;
                var t = a.message;
                e.setData({
                    showPreLoading: !1,
                    benqi: t.benqi,
                    shangqi: t.shangqi,
                    xiaqi: t.xiaqi
                });
                var o = require("../../../static/js/utils/wxTimer.js"), s = t.xiaqi.starttime;
                new o({
                    endTime: new Date().getTime() + s,
                    name: "wxTimer1",
                    issplit: 1
                }).start(e);
                var r = t.xiaqi.content;
                console.log(r), n.WxParse.wxParse("content", "html", r, e, 5);
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