var a = getApp();

Page({
    data: {
        shareData: {
            title: "为您优选",
            path: "/pages/channel/brand",
            success: function() {},
            fail: function() {}
        },
        Lang: a.Lang,
        wuiLoading: {
            show: !0,
            img: a.util.getStorageSync("theme.loading.img")
        }
    },
    onLoad: function(t) {
        var e = this;
        a.util.request({
            url: "wmall/channel/brand",
            data: {
                forceLocation: 1
            },
            success: function(t) {
                a.util.loaded(), e.setData(t.data.message.message);
            }
        });
    },
    onReachBottom: function() {},
    onShareAppMessage: function() {
        return this.data.shareData;
    }
});