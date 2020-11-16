var e = getApp();

Page({
    data: {
        Lang: e.Lang
    },
    onLoad: function(t) {
        wx.removeStorageSync("timer");
        var a = wx.getStorageSync("deliveryerInfo");
        if (a && a.token) e.util.jump2url("/pages/order/list"); else {
            var s = this;
            e.util.request({
                url: "delivery/auth/login",
                success: function(t) {
                    var a = t.data.message;
                    if (a.errno) return e.util.toast(a.message), !1;
                    s.setData(a.message);
                }
            });
        }
    },
    onSubmit: function(t) {
        var a = t.detail.value;
        a.mobile ? a.password ? e.util.request({
            url: "delivery/auth/login",
            method: "POST",
            data: {
                mobile: a.mobile,
                password: a.password
            },
            success: function(t) {
                var a = t.data.message;
                if (a.errno) return e.util.toast(a.message), !1;
                wx.setStorageSync("deliveryerInfo", {
                    token: a.message.deliveryer.token
                }), e.util.toast("登录成功", "/pages/order/list", 1e3);
            }
        }) : e.util.toast("请输入密码", "", 1e3) : e.util.toast("请输入手机号", "", 1e3);
    },
    onJsEvent: function(t) {
        e.util.jsEvent(t);
    }
});