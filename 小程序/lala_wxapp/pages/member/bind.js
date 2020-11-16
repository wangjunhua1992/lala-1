var e = getApp();

Page({
    data: {
        getCode: !0,
        code: {
            text: "获取验证码",
            downcount: 60
        },
        Lang: e.Lang,
        wuiLoading: {
            show: !0,
            img: e.util.getStorageSync("theme.loading.img")
        }
    },
    onLoad: function(t) {
        var a = this;
        e.util.request({
            url: "wmall/member/profile/info",
            method: "POST",
            success: function(t) {
                e.util.loaded();
                var s = t.data.message;
                if (s.errno) return e.util.toast(s.message), !1;
                a.setData({
                    mobile: s.message.mobile,
                    isverifymobile: s.message.isverifymobile
                });
            }
        }), this.onRefreshCaptcha();
    },
    getCode: function() {
        var t = this, a = t.data.code;
        if (!t.data.getCode) return !1;
        if (!t.data.mobile) return e.util.toast("手机号不能为空"), !1;
        if (!e.util.isMobile(t.data.mobile)) return e.util.toast("手机号格式错误"), !1;
        if (!t.data.bind_captcha) return e.util.toast("请输入图形验证码"), !1;
        var s = {
            mobile: t.data.mobile,
            captcha: t.data.bind_captcha
        };
        e.util.request({
            url: "system/common/code",
            data: s,
            method: "POST",
            success: function(s) {
                var o = s.data.message;
                if (o.errno) return e.util.toast(o.message), -10 == o.errno && t.onRefreshCaptcha(), 
                !1;
                a.text = a.downcount + "秒后重新获取", t.setData({
                    code: a,
                    getCode: !1
                });
                var i = setInterval(function() {
                    a.downcount--, a.downcount <= 0 ? (clearInterval(i), a.text = "获取验证码", a.downcount = 60, 
                    t.setData({
                        getCode: !0
                    })) : a.text = a.downcount + "秒后重新获取", t.setData({
                        code: a
                    });
                }, 1e3);
                e.util.toast("验证码发送成功, 请注意查收");
            }
        });
    },
    onMobile: function(e) {
        this.setData({
            mobile: e.detail.value
        });
    },
    onCaptcha: function(e) {
        this.setData({
            bind_captcha: e.detail.value
        });
    },
    onSubmit: function(t) {
        var a = this;
        if (!t.detail.value.mobile) return e.util.toast("手机号不能为空"), !1;
        if (!e.util.isMobile(t.detail.value.mobile)) return e.util.toast("手机号格式错误"), !1;
        if (1 == a.data.isverifymobile && !t.detail.value.code) return e.util.toast("请输入短信验证码"), 
        !1;
        if (!t.detail.value.password) return e.util.toast("密码不能为空"), !1;
        var s = t.detail.value.password.length;
        if (s < 8 || s > 20) return e.util.toast("请输入8-20位密码"), !1;
        if (!/[0-9]+[a-zA-Z]+[0-9a-zA-Z]*|[a-zA-Z]+[0-9]+[0-9a-zA-Z]*/.test(t.detail.value.password)) return e.util.toast("密码必须由数字和字母组合"), 
        !1;
        if (!t.detail.value.repassword) return e.util.toast("请重复输入密码"), !1;
        if (t.detail.value.password != t.detail.value.repassword) return e.util.toast("两次密码输入不一致"), 
        !1;
        var o = {
            mobile: t.detail.value.mobile,
            code: t.detail.value.code,
            password: t.detail.value.password,
            repassword: t.detail.value.repassword,
            force: 0
        };
        e.util.request({
            url: "wmall/member/profile/bind",
            data: o,
            success: function(t) {
                0 == t.data.message.errno ? e.util.toast(t.data.message.message, "profile") : -2 != t.data.message.errno ? e.util.toast(t.data.message.message) : wx.showModal({
                    content: "该手机号已绑定其他账号，确定进行合并吗",
                    confirmColor: "#ff2d4b",
                    success: function(a) {
                        a.confirm ? (o.force = 1, e.util.request({
                            method: "POST",
                            url: "wmall/member/profile/bind",
                            data: o,
                            success: function(a) {
                                if ((t = a.data.message).errno) return e.util.toast(t.message), !1;
                                e.util.toast(t.message, "profile");
                            }
                        })) : a.cancel && console.log("cancel");
                    }
                });
            }
        });
    },
    onRefreshCaptcha: function() {
        var t = this;
        e.util.request({
            url: "wmall/member/profile/captcha",
            success: function(a) {
                if (a.data.message.errno) return e.util.toast(a.data.message.message), !1;
                t.setData({
                    captcha: a.data.message.message.captcha
                });
            }
        });
    }
});