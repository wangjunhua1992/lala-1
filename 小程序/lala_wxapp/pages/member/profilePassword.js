var o = getApp();

Page({
    data: {},
    onLoad: function(o) {},
    onReady: function() {},
    onShow: function() {},
    onHide: function() {},
    onUnload: function() {},
    onPullDownRefresh: function() {},
    onReachBottom: function() {},
    onShareAppMessage: function() {},
    oldPassword: function(o) {
        this.setData({
            password: o.detail.value
        });
    },
    newPassword: function(o) {
        this.setData({
            newpassword: o.detail.value
        });
    },
    checkPassword: function(o) {
        this.setData({
            repassword: o.detail.value
        });
    },
    onSubmit: function() {
        var a = this, n = {
            type: "account",
            password: a.data.password,
            newpassword: a.data.newpassword,
            repassword: a.data.repassword
        };
        console.log(n), o.util.request({
            url: "wmall/member/profile/edit",
            data: n,
            success: function(o) {
                0 == o.data.message.errno && wx.redirectTo({
                    url: "profile"
                });
            }
        });
    }
});