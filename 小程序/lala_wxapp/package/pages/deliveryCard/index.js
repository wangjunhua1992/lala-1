var e = getApp();

Page({
    data: {
        showAgreement: !1,
        Lang: e.Lang,
        wuiLoading: {
            show: !0,
            img: e.util.getStorageSync("theme.loading.img")
        }
    },
    onLoad: function(t) {
        var a = this;
        e.util.request({
            url: "deliveryCard/index",
            success: function(t) {
                e.util.loaded(), 1 == a.data.deliveryCard_setmeal_ok && wx.setNavigationBarTitle({
                    title: "会员中心"
                }), a.setData(t.data.message.message);
            }
        });
    },
    onJsEvent: function(t) {
        e.util.jsEvent(t);
    },
    onShowAgreement: function() {
        this.setData({
            showAgreement: !this.data.showAgreement
        });
    }
});