var e = getApp();

Page({
    data: {},
    onLoad: function(e) {
        console.log(e), e.username && this.setData({
            username: e.username
        });
    },
    onReady: function() {},
    onShow: function() {},
    onHide: function() {},
    onUnload: function() {},
    onPullDownRefresh: function() {},
    onReachBottom: function() {},
    onShareAppMessage: function() {},
    onInput: function(e) {
        var n = e.detail.value;
        this.setData({
            username: n
        }), console.log(n);
    },
    onSubmit: function() {
        var n = {
            type: "username",
            realname: this.data.username
        };
        e.util.request({
            url: "wmall/member/profile/edit",
            data: n,
            success: function(e) {
                0 == e.data.message.errno && wx.redirectTo({
                    url: "profile"
                });
            }
        });
    }
});