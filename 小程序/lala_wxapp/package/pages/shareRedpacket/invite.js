var t = getApp();

Page({
    data: {
        uid: 0,
        mobile: "",
        captcha: "",
        inputcode: "",
        getCode: !0,
        code: {
            text: "获取验证码",
            downcount: 60
        },
        Lang: t.Lang,
        wuiLoading: {
            show: !0,
            img: t.util.getStorageSync("theme.loading.img")
        }
    },
    onLoad: function(a) {
        var e = this;
        a && a.u && (e.data.uid = a.u), t.util.request({
            url: "shareRedpacket/share/invite",
            data: {
                u: e.data.uid
            },
            success: function(a) {
                t.util.loaded();
                var o = a.data.message;
                if (o.errno) return -1e3 == o.errno ? (t.util.toast(o.message, "/package/pages/shareRedpacket/repeat", 1e3), 
                !1) : (t.util.toast(o.message), !1);
                e.setData(o.message);
            }
        });
    },
    onInput: function(t) {
        var a = t.target.dataset.type;
        this.data[a] = t.detail.value;
    },
    onRefreshCaptcha: function() {
        var a = this;
        t.util.request({
            url: "shareRedpacket/share/captcha",
            success: function(t) {
                a.setData({
                    captcha: t.data.message.message.captcha
                });
            }
        });
    },
    getCode: function() {
        var a = this, e = a.data.code;
        return !!a.data.getCode && (a.data.mobile ? t.util.isMobile(a.data.mobile) ? a.data.invite_captcha ? void t.util.request({
            url: "system/common/code",
            data: {
                mobile: a.data.mobile,
                captcha: a.data.invite_captcha
            },
            method: "POST",
            success: function(o) {
                var s = o.data.message;
                if (s.errno) return t.util.toast(s.message), -10 == s.errno && a.onRefreshCaptcha(), 
                !1;
                e.text = e.downcount + "秒后重新获取", a.setData({
                    code: e,
                    getCode: !1
                });
                var i = setInterval(function() {
                    e.downcount--, e.downcount <= 0 ? (clearInterval(i), e.text = "获取验证码", e.downcount = 60, 
                    a.setData({
                        getCode: !0
                    })) : e.text = e.downcount + "秒后重新获取", a.setData({
                        code: e
                    });
                }, 1e3);
                t.util.toast("验证码发送成功, 请注意查收");
            }
        }) : (t.util.toast("请输入图形验证码"), !1) : (t.util.toast("手机号格式错误"), !1) : (t.util.toast("手机号不能为空"), 
        !1));
    },
    onSubmit: function() {
        var a = this;
        if (!a.data.mobile) return t.util.toast("手机号不能为空"), !1;
        if (!t.util.isMobile(a.data.mobile)) return t.util.toast("手机号格式错误"), !1;
        if (!a.data.inputcode) return t.util.toast("请输入短信验证码"), !1;
        var e = {
            mobile: a.data.mobile,
            code: a.data.inputcode,
            uid: a.data.uid
        };
        t.util.request({
            url: "shareRedpacket/share/invite",
            data: e,
            method: "POST",
            success: function(a) {
                var e = a.data.message;
                e.errno ? (t.util.toast(e.message), -1e3 == e.errno && t.util.toast("您已是老用户", "/package/pages/shareRedpacket/repeat", 1500)) : t.util.toast("领取成功", "/package/pages/shareRedpacket/success?uid=" + e.message, 1500);
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