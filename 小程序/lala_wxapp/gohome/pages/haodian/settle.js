var t = getApp();

Page({
    data: {
        getCode: !0,
        code: {
            text: "获取验证码",
            downcount: 60,
            value: ""
        },
        submitting: !1,
        settle_captcha: "",
        Lang: t.Lang,
        wuiLoading: {
            show: !0,
            img: t.util.getStorageSync("theme.loading.img")
        }
    },
    onLoad: function() {
        var a = this;
        t.util.request({
            url: "haodian/settle/account",
            success: function(e) {
                t.util.loaded(), -1 != e.data.message.errno ? -1e3 == e.data.message.errno ? wx.redirectTo({
                    url: "application"
                }) : a.setData(e.data.message.message) : t.util.toast(e.data.message.message);
            }
        });
    },
    onSelectAgent: function(t) {
        var a = t.detail.value;
        this.setData({
            agent: this.data.agents[a]
        });
    },
    onInput: function(t) {
        var a = t.target.dataset.type;
        "code" == a ? this.data.code.value = t.detail : this.data[a] = t.detail;
    },
    getCode: function() {
        var a = this, e = a.data.code;
        if (!a.data.getCode) return !1;
        if (!a.data.mobile) return t.util.toast("手机号不能为空"), !1;
        if (!t.util.isMobile(a.data.mobile)) return t.util.toast("手机号格式错误"), !1;
        if (!a.data.settle_captcha) return t.util.toast("请输入图形验证码"), !1;
        var s = {
            mobile: a.data.mobile,
            captcha: a.data.settle_captcha
        };
        t.util.request({
            url: "system/common/code",
            data: s,
            method: "POST",
            success: function(s) {
                var o = s.data.message;
                if (o.errno) return t.util.toast(o.message), -10 == o.errno && a.onRefreshCaptcha(), 
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
        });
    },
    onSubmit: function(a) {
        var e = this;
        if (e.data.submitting) return !1;
        if (!e.data.mobile) return t.util.toast("手机号不能为空"), !1;
        if (!t.util.isMobile(e.data.mobile)) return t.util.toast("手机号格式错误"), !1;
        if (1 == e.data.mobile_verify_status) {
            if (!e.data.code.value) return t.util.toast("请输入短信验证码"), !1;
            if (!e.data.settle_captcha) return t.util.toast("请输入图形验证码"), !1;
        }
        if (!e.data.password) return t.util.toast("密码不能为空"), !1;
        var s = e.data.password.length;
        if (s < 8 || s > 20) return t.util.toast("请输入8-20位密码"), !1;
        if (!/[0-9]+[a-zA-Z]+[0-9a-zA-Z]*|[a-zA-Z]+[0-9]+[0-9a-zA-Z]*/.test(e.data.password)) return t.util.toast("密码必须由数字和字母组合"), 
        !1;
        if (!e.data.repassword) return t.util.toast("请重复输入密码"), !1;
        if (e.data.password != e.data.repassword) return t.util.toast("两次密码输入不一致"), !1;
        if (!e.data.title) return t.util.toast("请输入姓名"), !1;
        var o = 0;
        if (e.data.isagent > 0 && !(o = e.data.agent.id)) return t.util.toast("请选择所属区域"), 
        !1;
        e.data.submitting = !0;
        var i = {
            mobile: e.data.mobile,
            code: e.data.code.value,
            password: e.data.password,
            repassword: e.data.repassword,
            title: e.data.title,
            agentid: o
        };
        t.util.request({
            url: "haodian/settle/account",
            data: i,
            method: "POST",
            success: function(a) {
                -1e3 == a.data.message.errno ? (t.util.toast(a.data.message.message), e.onLoad()) : (t.util.toast(a.data.message.message), 
                a.data.message.errno && (e.data.submitting = !1));
            }
        });
    },
    onRefreshCaptcha: function() {
        var a = this;
        t.util.request({
            url: "haodian/settle/captcha",
            success: function(t) {
                a.setData({
                    captcha: t.data.message.message.captcha
                });
            }
        });
    }
});