var a = getApp();

Page({
    data: {
        redPackets: [],
        showloading: !1,
        showNodata: !1,
        Lang: a.Lang,
        wuiLoading: {
            show: !0,
            img: a.util.getStorageSync("theme.loading.img")
        }
    },
    onLoad: function(a) {
        this.onReachBottom();
    },
    onReady: function() {},
    onPullDownRefresh: function() {},
    onReachBottom: function() {
        var t = this;
        if (console.log(t), -1 == t.data.min) return !1;
        this.setData({
            showloading: !0
        });
        var e = {
            min: t.data.min,
            status: 2
        };
        a.util.request({
            url: "wmall/member/redPacket",
            data: e,
            success: function(e) {
                a.util.loaded();
                var n = t.data.redPackets.concat(e.data.message.message);
                if (!n.length) return t.setData({
                    showNodata: !0,
                    showloading: !1
                }), !1;
                t.setData({
                    redPackets: n,
                    min: e.data.message.min
                }), e.data.message.min || (t.data.min = -1), t.setData({
                    showloading: !1
                });
            }
        });
    },
    onShareAppMessage: function() {}
});