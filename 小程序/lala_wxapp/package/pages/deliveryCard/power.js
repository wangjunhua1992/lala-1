var e = getApp();

Page({
    data: {
        Lang: e.Lang,
        wuiLoading: {
            show: !0,
            img: e.util.getStorageSync("theme.loading.img")
        }
    },
    onLoad: function() {
        var a = this;
        e.util.request({
            url: "deliveryCard/index/power",
            success: function(t) {
                e.util.loaded(), a.setData(t.data.message.message);
            }
        });
    },
    onToHome: function() {
        e.util.gohome();
    }
});