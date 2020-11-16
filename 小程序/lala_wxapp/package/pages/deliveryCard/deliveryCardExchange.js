var e = getApp();

Page({
    data: {
        code: "",
        member: {},
        islegal: !1,
        Lang: e.Lang,
        wuiLoading: {
            show: !0,
            img: e.util.getStorageSync("theme.loading.img")
        }
    },
    onLoad: function() {
        var a = this;
        e.util.request({
            url: "deliveryCard/deliveryExchange/index",
            success: function(t) {
                e.util.loaded();
                var n = t.data.message;
                if (n.errno) return e.util.toast(n.message), !1;
                n = n.message, a.data.member = n.member, a.data.islegal = !0, a.setData(a.data);
            }
        });
    },
    onSubmit: function() {
        var a = this;
        a.data.islegal && (a.data.code ? (a.data.islegal = !1, e.util.request({
            url: "deliveryCard/deliveryExchange/exchange",
            data: {
                code: a.data.code
            },
            success: function(t) {
                var n = t.data.message;
                if (n.errno) return e.util.toast(n.message), a.data.islegal = !0, !1;
                e.util.toast("兑换成功", "/package/pages/deliveryCard/index", 1500);
            }
        })) : e.util.toast("请输入16位兑换码"));
    },
    onChange: function(e) {
        this.setData({
            code: e.detail
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