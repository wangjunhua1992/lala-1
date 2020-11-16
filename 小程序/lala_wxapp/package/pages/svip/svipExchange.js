var a = getApp();

Page({
    data: {
        code: "",
        nickname: "",
        islegal: !1,
        Lang: a.Lang,
        wuiLoading: {
            show: !0,
            img: a.util.getStorageSync("theme.loading.img")
        }
    },
    onLoad: function() {
        var e = this;
        a.util.request({
            url: "svip/svipExchange/index",
            success: function(t) {
                a.util.loaded();
                var n = t.data.message;
                if (n.errno) return a.util.toast(n.message), !1;
                n = n.message, e.data.nickname = n.nickname, e.data.islegal = !0, e.setData(e.data);
            }
        });
    },
    onSubmit: function() {
        var e = this;
        e.data.islegal && (e.data.code ? (e.data.islegal = !1, a.util.request({
            url: "svip/svipExchange/exchange",
            data: {
                code: e.data.code
            },
            success: function(t) {
                var n = t.data.message;
                if (n.errno) return a.util.toast(n.message), e.data.islegal = !0, !1;
                a.util.toast("兑换成功", "/package/pages/svip/mine", 1500);
            }
        })) : a.util.toast("请输入16位兑换码"));
    },
    onChange: function(a) {
        this.setData({
            code: a.detail
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