var t = getApp();

Page({
    data: {
        Lang: t.Lang,
        getCode: !0,
        code: {
            text: "获取验证码",
            downcount: 60
        },
        readed: !1,
        idCardOne: [],
        idCardTwo: []
    },
    onLoad: function(a) {
        var e = this;
        t.util.request({
            url: "delivery/auth/register",
            success: function(a) {
                var i = a.data.message;
                if (i.errno) return t.util.toast(i.message), !1;
                e.setData(i.message);
            }
        });
    },
    onJsEvent: function(a) {
        t.util.jsEvent(a);
    },
    onMobile: function(t) {
        this.setData({
            mobile: t.detail.value
        });
    },
    getCode: function() {
        var a = this, e = a.data.code;
        if (!a.data.getCode) return !1;
        if (!a.data.mobile) return t.util.toast("手机号不能为空"), !1;
        if (!t.util.isMobile(a.data.mobile)) return t.util.toast("手机号格式错误"), !1;
        if (!a.data.register_captcha) return t.util.toast("请输入图形验证码"), !1;
        var i = {
            mobile: a.data.mobile,
            captcha: a.data.register_captcha
        };
        t.util.request({
            url: "system/common/code",
            data: i,
            success: function(i) {
                var r = i.data.message;
                if (r.errno) return t.util.toast(r.message), -10 == r.errno && a.onRefreshCaptcha(), 
                !1;
                e.text = e.downcount + "秒后重新获取", a.setData({
                    code: e,
                    getCode: !1
                });
                var s = setInterval(function() {
                    e.downcount--, e.downcount <= 0 ? (clearInterval(s), e.text = "获取验证码", e.downcount = 60, 
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
    onChangeReaded: function() {
        var t = this;
        t.setData({
            readed: !t.data.readed
        });
    },
    onSubmit: function(a) {
        var e = this, i = a.detail.value;
        if (!i.mobile) return t.util.toast("手机号不能为空"), !1;
        if (!t.util.isMobile(i.mobile)) return t.util.toast("手机号格式错误"), !1;
        if (1 == e.data.config_deliveryer.settle.mobile_verify_status && !i.code) return t.util.toast("请输入短信验证码"), 
        !1;
        if (!i.password) return t.util.toast("密码不能为空"), !1;
        var r = i.password.length;
        if (r < 8 || r > 20) return t.util.toast("请输入8-20位密码"), !1;
        if (!/[0-9]+[a-zA-Z]+[0-9a-zA-Z]*|[a-zA-Z]+[0-9]+[0-9a-zA-Z]*/.test(i.password)) return t.util.toast("密码必须由数字和字母组合"), 
        !1;
        if (!i.repassword) return t.util.toast("请重复输入密码"), !1;
        if (i.password != i.repassword) return t.util.toast("两次密码输入不一致"), !1;
        if (!i.title) return t.util.toast("请输入真实姓名"), !1;
        if (1 == e.data.config_deliveryer.settle.idCard && 1 == !e.data.idCardOne.length) return t.util.toast("手持身份证照片不能为空"), 
        !1;
        if (1 == e.data.config_deliveryer.settle.idCard && 1 == !e.data.idCardTwo.length) return t.util.toast("身份证正面照片不能为空"), 
        !1;
        var s = 0;
        if (e.data.isagent > 0 && !(s = e.data.agent.id)) return t.util.toast("请选择所属区域"), 
        !1;
        if (!e.data.readed) return t.util.toast("请确认已阅读入驻申请协议"), !1;
        var o = {
            mobile: i.mobile,
            code: i.code,
            password: i.password,
            repassword: i.repassword,
            title: i.title,
            idCardOne: e.data.idCardOne[0].attachment,
            idCardTwo: e.data.idCardTwo[0].attachment,
            agentid: s
        };
        t.util.request({
            url: "delivery/auth/register",
            data: o,
            method: "POST",
            success: function(a) {
                0 == a.data.message.errno ? t.util.toast(a.data.message.message, "login", 1e3) : t.util.toast(a.data.message.message);
            }
        });
    },
    onCaptcha: function(t) {
        this.setData({
            register_captcha: t.detail.value
        });
    },
    onRefreshCaptcha: function() {
        var a = this;
        t.util.request({
            url: "delivery/auth/register/captcha",
            success: function(t) {
                a.setData({
                    captcha: t.data.message.message.captcha
                });
            }
        });
    },
    onSelectAgent: function(t) {
        var a = t.detail.value;
        this.setData({
            agent: this.data.agents[a]
        });
    }
});