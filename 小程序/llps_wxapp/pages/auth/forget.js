var t = getApp();

Page({
    data: {
        Lang: t.Lang,
        getCode: !0,
        code: {
            text: "获取验证码",
            downcount: 60
        }
    },
    onLoad: function(t) {
        this.onRefreshCaptcha();
    },
    onMobile: function(t) {
        this.setData({
            mobile: t.detail.value
        });
    },
    onCaptcha: function(t) {
        this.setData({
            forget_captcha: t.detail.value
        });
    },
    getCode: function() {
        var a = this, e = a.data.code;
        if (!a.data.getCode) return !1;
        if (!a.data.mobile) return t.util.toast("手机号不能为空"), !1;
        if (!t.util.isMobile(a.data.mobile)) return t.util.toast("手机号格式错误"), !1;
        if (!a.data.forget_captcha) return t.util.toast("请输入图形验证码"), !1;
        var o = {
            mobile: a.data.mobile,
            captcha: a.data.forget_captcha
        };
        t.util.request({
            url: "system/common/code",
            data: o,
            method: "POST",
            success: function(o) {
                var s = o.data.message;
                if (s.errno) return t.util.toast(s.message), -10 == s.errno && a.onRefreshCaptcha(), 
                !1;
                e.text = e.downcount + "秒后重新获取", a.setData({
                    code: e,
                    getCode: !1
                });
                var r = setInterval(function() {
                    e.downcount--, e.downcount <= 0 ? (clearInterval(r), e.text = "获取验证码", e.downcount = 60, 
                    a.setData({
                        getCode: !0
                    })) : e.text = e.downcount + "秒后重新获取", a.setData({
                        code: e
                    });
                }, 1e3);
                t.util.toast("验证码发送成功, 请注意查收");
            }
        });
    },
    onSubmit: function(a) {
        var e = a.detail.value;
        if (!e.mobile) return t.util.toast("手机号不能为空"), !1;
        if (!t.util.isMobile(e.mobile)) return t.util.toast("手机号格式错误"), !1;
        if (!e.code) return t.util.toast("请输入短信验证码"), !1;
        if (!e.password) return t.util.toast("密码不能为空"), !1;
        var o = e.password.length;
        if (o < 8 || o > 20) return t.util.toast("请输入8-20位密码"), !1;
        if (!/[0-9]+[a-zA-Z]+[0-9a-zA-Z]*|[a-zA-Z]+[0-9]+[0-9a-zA-Z]*/.test(e.password)) return t.util.toast("密码必须由数字和字母组合"), 
        !1;
        if (!e.repassword) return t.util.toast("请重复输入密码"), !1;
        if (e.password != e.repassword) return t.util.toast("两次密码输入不一致"), !1;
        var s = {
            mobile: e.mobile,
            code: e.code,
            password: e.password,
            repassword: e.repassword
        };
        t.util.request({
            url: "delivery/auth/forget",
            data: s,
            success: function(a) {
                0 == a.data.message.errno ? t.util.toast(a.data.message.message, "login", 1e3) : t.util.toast(a.data.message.message);
            }
        });
    },
    onRefreshCaptcha: function() {
        var a = this;
        t.util.request({
            url: "delivery/auth/forget/captcha",
            success: function(t) {
                a.setData({
                    captcha: t.data.message.message.captcha
                });
            }
        });
    }
});