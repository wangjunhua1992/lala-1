var a = getApp();

Page({
    data: {
        Lang: a.Lang
    },
    onLoad: function(a) {},
    onSubmit1: function(s) {
        var t = s.detail.value;
        if (!t.password) return a.util.toast("原密码不能为空"), !1;
        if (!t.newpassword) return a.util.toast("密码不能为空"), !1;
        var e = t.newpassword.length;
        if (e < 8 || e > 20) return a.util.toast("请输入8-20位密码"), !1;
        if (!/[0-9]+[a-zA-Z]+[0-9a-zA-Z]*|[a-zA-Z]+[0-9]+[0-9a-zA-Z]*/.test(t.newpassword)) return a.util.toast("密码必须由数字和字母组合"), 
        !1;
        if (!t.repassword) return a.util.toast("请重复输入密码"), !1;
        if (t.newpassword != t.repassword) return a.util.toast("两次密码输入不一致"), !1;
        var o = {
            password: t.password,
            newpassword: t.newpassword,
            repassword: t.repassword,
            formid: s.detail.formId
        };
        a.util.request({
            url: "manage/more/profile/password",
            data: o,
            method: "POST",
            success: function(s) {
                0 == s.data.message.errno ? a.util.toast(s.data.message.message, "../shop/setting", 1e3) : a.util.toast(s.data.message.message);
            }
        });
    },
    onReady: function() {},
    onShow: function() {},
    onHide: function() {},
    onUnload: function() {},
    onPullDownRefresh: function() {},
    onReachBottom: function() {},
    onShareAppMessage: function() {},
    oldPassword: function(a) {
        this.setData({
            password: a.detail.value
        });
    },
    newPassword: function(a) {
        this.setData({
            newPassword: a.detail.value
        });
    },
    checkPassword: function(a) {
        this.setData({
            rePassword: a.detail.value
        });
    },
    onSubmit: function() {
        var s = this, t = {
            password: s.data.password,
            newpassword: s.data.newPassword,
            repassword: s.data.rePassword
        };
        a.util.request({
            url: "manage/more/profile/password",
            data: t,
            method: "POST",
            success: function(a) {
                0 == a.data.message.errno && wx.redirectTo({
                    url: "profile"
                });
            }
        });
    }
});