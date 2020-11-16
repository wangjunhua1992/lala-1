var e = getApp();

Page({
    data: {
        Lang: e.Lang
    },
    onLoad: function() {},
    onChangeSwitch: function(t) {
        var a = t.detail.value ? 1 : 0, n = t.currentTarget.dataset.type;
        e.util.request({
            url: "manage/shop/index/status",
            method: "POST",
            data: {
                type: n,
                value: a
            },
            success: function(t) {
                var a = t.data.message;
                if (e.util.toast(a.message), a.errno) return !1;
            }
        });
    },
    onLoginout: function() {
        wx.showModal({
            title: "",
            content: "确定退出当前登录吗？",
            success: function(t) {
                t.confirm ? e.util.request({
                    url: "manage/auth/loginout",
                    method: "POST",
                    success: function(t) {
                        var a = t.data.message;
                        if (a.errno) return e.util.toast(a.message), !1;
                        wx.removeStorageSync("clerkInfo"), wx.removeStorageSync("__sid"), e.util.toast(a.message, "/pages/auth/login", 1e3);
                    }
                }) : t.cancel;
            }
        });
    },
    onJsEvent: function(t) {
        e.util.jsEvent(t);
    },
    onShow: function() {
        var t = this;
        e.util.request({
            url: "manage/shop/index",
            success: function(a) {
                var n = a.data.message;
                if (n.errno) return e.util.toast(n.message), !1;
                n.message.wxappversion = e.util.wxappversion(), t.setData(n.message);
            }
        });
    },
    onPullDownRefresh: function() {
        this.onShow(), wx.stopPullDownRefresh();
    }
});